<?php

namespace App\Services\Correios;

use App\Helpers\Correios\TrackingRequest;
use Exception;

class TrackingService extends TrackingRequest
{

    public function getTracking ($trackingCode)
    {
        $url = $this->getUrl("sro-rastro");

        try {

            return $this->call($url, "GET", $trackingCode);

        } catch (Exception $e) {
            throw new Exception($e);
        }
    }

}