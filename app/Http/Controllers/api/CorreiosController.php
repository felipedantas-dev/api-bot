<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Services\Correios\RastreioService;
use Exception;
use Illuminate\Http\Request;

class CorreiosController extends Controller
{

    public function __construct(RastreioService $rastreioService)
    {
        $this->rastreioService = $rastreioService;
    }
    
    /**
     * Undocumented function
     *
     * @param Request $request
     * @param string $code
     * @return void
     */
    public function tracking(Request $request, $trackingCode)
    {
        try {

            $dataTracking = $this->rastreioService->getTracking($trackingCode);

            return response()->json(["data" => $dataTracking], 200);

        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()], 400);
        }
    }

}