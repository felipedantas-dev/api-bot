<?php

namespace App\Helpers\Correios;

class CEPRequest
{

    public function __construct()
    {
        $this->client =  new \GuzzleHttp\Client();
    }

    public function getUrl ()
    {
        return env("API_VIACEP_CEP");
    }

    public function getUrlCEP ($url, $CEP)
    {
        return $url . (substr($CEP, 0, 1) == '/'
            ? $CEP . '/json'
            : '/' . $CEP . '/json');
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

    protected function call($url, $type, $CEP, $headers = null)
    {

        $response = $this->client->request(
            $type,
            $this->getUrlCEP($url, $CEP),
            [
                'headers' => is_null($headers) ? $this->getHeaders() : $headers
            ]
        );

        return json_decode((string) $response->getBody());
    }
    

}