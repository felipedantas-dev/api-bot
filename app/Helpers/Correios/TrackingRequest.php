<?php

namespace App\Helpers\Correios;

class TrackingRequest
{

    public function __construct()
    {
        $this->client =  new \GuzzleHttp\Client();
    }

    public function getUrl ($end_point)
    {
        return env("API_VIACEP_CEP") . (substr($end_point, 0, 1) == '/'
            ? $end_point
            : '/' . $end_point);
    }

    public function getUrlTrackCode ($url, $trackCode)
    {
        return $url . (substr($trackCode, 0, 1) == '/'
            ? $trackCode
            : '/' . $trackCode);
    }

    /**
     * Getter the headers to access
     *
     * @return array
     */
    public function getHeaders()
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];
    }

    protected function call($url, $type, $trackingCode, $headers = null)
    {

        $response = $this->client->request(
            $type,
            $this->getUrlTrackCode($url, $trackingCode),
            [
                'headers' => is_null($headers) ? $this->getHeaders() : $headers
            ]
        );

        return json_decode((string) $response->getBody());
    }
    

}