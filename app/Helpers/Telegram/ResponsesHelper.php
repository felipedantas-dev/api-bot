<?php

namespace App\Helpers\Telegram;

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
            "text" => "Você escolheu a opção para rastreamento de encomenda do Correios, por favor envie o código da encomenda."
        ],
        "2" => [
            "text" => "Você escolheu a opção de consulta de CEP, por favor envie o número do CEP que deseja consultar."
        ]
    ];
    const REESEND_OPTION = "\n<i>Caso não seja a opção desejada, envie novamente <b>/start</b> e confira as opções novamente.</i>";

    const NOT_FOUND = "😢 Não consegui entender... Confira nossas funcionalidades enviando /start ou envie o número da funcionalidade que deseja.";

    public function getResponse ($message)
    {

        switch ($message) {
            case array_key_exists($message, self::OPTIONS):
                $response = self::OPTIONS[$message]["text"];
                $response .= self::REESEND_OPTION;
                break;

            case array_key_exists($message, self::START):
                $response = self::START[$message]["text"];
                break;

            default:
                $response = self::NOT_FOUND;
                break;
        }

        return $response;
    }


}