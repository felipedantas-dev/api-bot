<?php

namespace App\Http\Controllers\api;

use App\Helpers\Telegram\ResponsesHelper;
use App\Http\Controllers\Controller;
use Exception;

class TelegramController extends Controller
{

    public function __construct()
    {
    }

    /**
     * Process webhook bot api telegram
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
    
            $telegramResponses = new ResponsesHelper($request);
            $response = $telegramResponses->sendResponse();
    
            return response()->json(["data" => $response], 200);
        
        } catch (Exception $e) {

            return response()->json(['error' => $e->getMessage()], 400);

        }
    }

}