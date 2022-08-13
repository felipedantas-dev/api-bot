<?php

namespace App\Helpers\Correios;

use App\Helpers\Telegram\ResponsesHelper;
use App\Services\Correios\CEPService;
use Exception;

class CEPHelper extends ResponsesHelper
{

    public function getCEPInfos ()
    {
        $CEPCode = $this->getCEPCode(explode(" ", $this->message));

        try {
            
            $data = (new CEPService())->getCEP($CEPCode);

            $sendData = [];

            array_push($sendData, $this->sendMessage("🔰 Informações do <b>CEP {$data->cep}</b>"));
    
            array_push($sendData, $this->sendMessage($this->getCEPMessage($data)));
    
            return $sendData;

        } catch (Exception $e) {

            return $this->sendMessage(self::INVALID_CEP);

        }

    }

    private function getCEPMessage ($data)
    {
        $msg =  "\n🚩 <b>{$data->logradouro}</b> ({$data->complemento})";
        $msg .= "\n🗺 <b>{$data->bairro}</b>, <b>{$data->localidade}</b> - <b>{$data->uf}</b>";
        $msg .= "\n🟢 <i><b>IBGE</b>: {$data->ibge}</i>";
        $msg .= "\n📱 <i><b>DDD</b>: {$data->ddd}</i>";

        return $msg;
    }

    private function getCEPCode ($texts)
    {
        foreach ($texts as $text) {
            if ($this->isCEPCode($text)) {
                return $this->sanitizeCEP($text);
            }
        }
    }

    private function isCEPCode ($CEP)
    {
        return preg_match('/[0-9]{8}$/', $this->sanitizeCEP($CEP));
    }

    private function sanitizeCEP ($CEP)
    {
        return str_replace("-", "", $CEP);
    }
    
}