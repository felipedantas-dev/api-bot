<?php

namespace App\Helpers\Telegram;

use App\Helpers\Correios\CEPHelper;
use App\Helpers\Correios\TrackingHelper;
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
            "text" => "Você escolheu a opção de consulta de CEP, por favor envie o número do CEP que deseja consultar, seguindo o exemplo abaixo:
                        \n <i>!CEP <b>12345-678</b></i>"
        ]
    ];

    const REESEND_OPTION = "\n<i>Caso não seja a opção desejada, envie novamente <b>/start</b> e confira as opções novamente.</i>";

    const NOT_FOUND = "😢 Não consegui entender... Confira nossas funcionalidades enviando /start ou envie o número da funcionalidade que deseja.";

    const INVALID_TRACKING = "😩 Não conseguimos encontrar seu objeto, por favor tente novamente com um código de rastreio válido.";
    const INVALID_CEP = "😩 Não conseguimos encontrar o CEP digitado, por favor tente novamente com um CEP válido.";

    public function __construct($request)
    {
        $this->telegram = new Api(env("BOT_FDEV_TELEGRAM_TOKEN"));

        $this->request = $request;
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
                $sendData = (new TrackingHelper($this->request))->getTrackingInfos();
                break;

            //Entra na opção de CPF
            case str_contains(strtoupper($this->message), "!CEP"):
                $sendData = (new CEPHelper($this->request))->getCEPInfos();
                break;
                
            default:
                $response = self::NOT_FOUND;
                $sendData = $this->sendMessage($response);
                break;
        }

        return $sendData;
    }

    protected function sendMessage ($response)
    {
        return $this->telegram->sendMessage([
            'chat_id' => $this->chat_id, 
            'text' => $response,
            'parse_mode' => 'html'
        ]);
    }


}