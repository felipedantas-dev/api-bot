<?php

namespace App\Http\Controllers\api;

use App\Helpers\Telegram\ResponsesHelper;
use App\Http\Controllers\Controller;
use Error;
use Exception;
use Illuminate\Support\Facades\DB;
use Telegram\Bot\Api;

class TelegramController extends Controller
{

    public function __construct(ResponsesHelper $telegramResponses)
    {
        $this->telegramResponses = $telegramResponses;
    }

    public function index()
    {
        return response()->json(["data" => "Hello World"], 200);
    }

    /**
     * Display a listing of the comics.
     *
     * @return \Illuminate\Http\Response
     */
    public function process()
    {
        
        try {

            $request = json_decode((string) file_get_contents('php://input'));
            $headers = getallheaders();

            if ($headers["X-Telegram-Bot-Api-Secret-Token"] != env("TELEGRAM_WEBHOOK_SECRET_TOKEN")) {
                throw new Exception("WEBHOOK SECRET TOKEN INVÁLIDO");
            }

            $telegram = new Api(env("BOT_FDEV_TELEGRAM_TOKEN"));
    
            $response = $this->telegramResponses->getResponse($request->message->text);
    
            $data = $telegram->sendMessage([
                'chat_id' => $request->message->chat->id, 
                'text' => $response,
                'parse_mode' => 'html'
            ]);
    
            return response()->json(["data" => $data], 200);
        
        } catch (Exception $e) {

            return response()->json(['error' => $e->getMessage()], 400);

        }
    }

}