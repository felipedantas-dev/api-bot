<?php

namespace App\Services\Correios;

use App\Helpers\Correios\CEPRequest;
use Exception;

class CEPService extends CEPRequest
{

    public function getCEP ($CEP)
    {
        $url = $this->getUrl();

        try {

            return $this->call($url, "GET", $CEP);

        } catch (Exception $e) {
            throw new Exception($e);
        }
    }

}