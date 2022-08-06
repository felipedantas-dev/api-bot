<?php

namespace App\Helpers\Telegram;

class ResponsesHelper
{

    const START = "/start";
    const OPTIONS = ["1", "2", "3"];
    const NOT_FOUND = "😢 Não consegui entender... Confira nossas funcionalidades enviando /start ou envie o número da funcionalidade que deseja.";


    public function getResponse ($message)
    {

        switch ($message) {
            case in_array($message, self::OPTIONS):
                $response = "A opção escolhida foi: {$message} 🥰!!";
                break;

            case self::START:
                $response = "Olá, sou o FDevBot e estou aqui para te ajudar, escolha uma das opções abaixo:";
                break;

            case "Testando":
                $response = "😁👍 Testado!";
                break;

            default:
                $response = self::NOT_FOUND;
                break;
        }

        return $response;
    }


}