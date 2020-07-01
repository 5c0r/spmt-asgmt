<?php

namespace App\Service;

use GuzzleHttp\Client;
use App\Model\SimpleHttpClientInterface;

// Try Guzzle retry https://github.com/caseyamcl/guzzle_retry_middleware
final class GuzzleHttpClient implements SimpleHttpClientInterface
{
    protected Client $guzzleClient;

    public function __construct(string $baseUri)
    {
        $this->guzzleClient = new Client(['base_uri' => $baseUri, 'verify' => false]);
    }

    public function getRequest(string $url, ?array $getParams)
    {
        try {
            $getOptions = [];

            if (!empty($getParams) && !is_null($getParams)) {
                $getOptions = [
                    'query' => $getParams
                ];
            }

            return $this->guzzleClient->request('GET', $url, $getOptions);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function postRequest(string $url, array $postData)
    {
        return $this->guzzleClient->request('POST', $url, [
            'form_params' => $postData
        ]);
    }
}