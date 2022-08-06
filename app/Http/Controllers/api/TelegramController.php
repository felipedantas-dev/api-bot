<?php

namespace App\Http\Controllers\api;

use App\Helpers\Telegram\ResponsesHelper;
use App\Http\Controllers\Controller;
use Exception;
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
        
            $telegram = new Api("5523489756:AAF1k4hkaWHMrtjNI8PnIREuJ6YEHXFN4aM");
    
            $response = $this->telegramResponses->getResponse($request->message->text);
    
            $data = $telegram->sendMessage([
                'chat_id' => $request->message->chat->id, 
                'text' => $response
            ]);
    
            return response()->json(["data" => $data], 200);
        
        } catch (Exception $e) {

            return response()->json(['error' => $e->getMessage()], 400);

        }
    }

}