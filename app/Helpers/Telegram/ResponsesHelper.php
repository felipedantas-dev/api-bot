<?php

namespace App\Helpers\Telegram;

use App\Services\Correios\RastreioService;
use Carbon\Carbon;
use Telegram\Bot\Api;

class ResponsesHelper
{

    const START = [
        "/start" => [
            "text" => "🤖 Olá, sou o FDevBot e estou aqui para te ajudar, escolha uma das opções abaixo:
                        \n1️⃣ <b>Rastreamento de ecomenda do correios</b>;
                        \n2️⃣ <b>Consulta de CEP</b>;
                        \n\n<i>Por favor digite o número da opção desejada, por exemplo: <b>1</b></i>"
        ]
    ];

    const OPTIONS = [
        "1" => [
            "text" => "Você escolheu a opção para rastreamento de encomenda do Correios, copie o exemplo abaixo e envie com o seu código de rastreio:
                        \n <i>!Rastreio <b>CD123456789BR</b></i>"
        ],
        "2" => [
            "text" => "Você escolheu a opção de consulta de CEP, por favor envie o número do CEP que deseja consultar."
        ]
    ];

    const REESEND_OPTION = "\n<i>Caso não seja a opção desejada, envie novamente <b>/start</b> e confira as opções novamente.</i>";

    const NOT_FOUND = "😢 Não consegui entender... Confira nossas funcionalidades enviando /start ou envie o número da funcionalidade que deseja.";

    const INVALID_TRACKING = "😩 Não conseguimos encontrar seu objeto, por favor tente novamente com um código de rastreio válido.";

    public function __construct($request)
    {
        $this->telegram = new Api(env("BOT_FDEV_TELEGRAM_TOKEN"));
        $this->message = $request->message->text;
        $this->chat_id = $request->message->chat->id;
    }

    public function sendResponse()
    {

        switch ($this->message) {
            case array_key_exists($this->message, self::OPTIONS):
                $response = self::OPTIONS[$this->message]["text"];
                $response .= self::REESEND_OPTION;
                $sendData = $this->sendMessage($response);

                break;

            case array_key_exists($this->message, self::START):
                $response = self::START[$this->message]["text"];
                $sendData = $this->sendMessage($response);
                break;

            //Entra na opção de código de rastreio
            case str_contains(strtoupper($this->message), "!RASTREIO"):
                $sendData = $this->getTrackingInfos(strtoupper($this->message));
                break;

            default:
                $response = self::NOT_FOUND;
                $sendData = $this->sendMessage($response);
                break;
        }

        return $sendData;
    }


    private function getTrackingInfos ($message)
    {
        $texts_message = explode(" ", $message);
        $trackingCode = $this->getTrackingCode($texts_message);
        $trackingData = (new RastreioService())->getTracking($trackingCode);

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
        return array_key_exists("mensagem", (array) $tracking) && (str_contains($tracking["mensagem"], "inválido") || str_contains($tracking["mensagem"], "não encontrado"));
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

    private function sendMessage ($response)
    {
        return $this->telegram->sendMessage([
            'chat_id' => $this->chat_id, 
            'text' => $response,
            'parse_mode' => 'html'
        ]);
    }


}