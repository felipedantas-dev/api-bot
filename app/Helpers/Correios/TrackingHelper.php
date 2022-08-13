<?php

namespace App\Helpers\Correios;

use App\Helpers\Telegram\ResponsesHelper;
use App\Services\Correios\TrackingService;

class TrackingHelper extends ResponsesHelper
{

    public function getTrackingInfos ()
    {
        $texts_message = explode(" ", $this->message);
        $trackingCode = $this->getTrackingCode($texts_message);
        $trackingData = (new TrackingService())->getTracking($trackingCode);

        $trackingKey = array_search($trackingCode, array_column($trackingData->objetos, "codObjeto"));

        if ($this->trackingIsInvalid((array) $trackingData->objetos[$trackingKey])) {
            return $this->sendMessage(self::INVALID_TRACKING);
        }

        $sendData = [];

        array_push($sendData,$this->sendMessage("📫 Histórico do objeto de rastreio <b>{$trackingData->objetos[$trackingKey]->codObjeto}</b>\n\n"));

        foreach (array_reverse($trackingData->objetos[$trackingKey]->eventos) as $index => $evento) {
            array_push($sendData, $this->sendMessage($this->setTrackingResponse($evento)));
        }

        return $sendData;
    }

    private function trackingIsInvalid($tracking)
    {
        return array_key_exists("mensagem", (array) $tracking);
    }

    private function setTrackingResponse ($event)
    {
        $status = $event->descricao == "Objeto entregue ao destinatário" ? "📪" : ($event->descricao == "Objeto postado" ? "📦" : "🚚");
        $data = date('d/m/Y', strtotime($event->dtHrCriado));
        $hora = date('H:i:s', strtotime($event->dtHrCriado));

        $msg =  "\n{$status} <b>{$event->descricao}</b>";
        $msg .= "\n📅 <i>{$data} às {$hora}</i>";
        $msg .= "\n🚩 Localizado em <b>{$event->unidade->endereco->cidade} - {$event->unidade->endereco->uf}</b>";

        return $msg;
    }


    private function getTrackingCode ($texts)
    {
        foreach ($texts as $text) {
            if ($this->isTrackingCode($text)) {
                return $text;
            }
        }
    }

    private function isTrackingCode ($var)
    {
        return str_contains(strtoupper($var), "BR");
    }

}