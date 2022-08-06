<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class TelegramController extends Controller
{

    public function __construct ()
    {
    }

    /**
     * Display a listing of the comics.
     *
     * @return \Illuminate\Http\Response
     */
    public function process ()
    {

        try {

            return response()->json(["data" => "data"], 200);
        
        } catch (Exception $e) {

            return response()->json(['error' => $e->getMessage()], 400);

        }
    }

}