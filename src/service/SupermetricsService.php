<?php

namespace App\Service;

use App\Model\SimpleHttpClientInterface;
use App\Model\SupermetricsServiceInterface;

final class SupermetricsService implements SupermetricsServiceInterface {

    private string $client_id;
    private string $email;
    private string $name;

    private string $sl_token;

    // Using HttpInterface does not sounds like a good idea, we are still flooding Guzzle/PSR- 's response body classes here
    private SimpleHttpClientInterface $httpClient;

    public function __construct(string $base_uri, string $client_id, string $email, string $name)
    {
        $this->client_id=$client_id;
        $this->email=$email;
        $this->name=$name;
        // Try using DI
        $this->httpClient= new GuzzleHttpClient($base_uri);
    }

    public function authenticate() : bool
    {
        try {
            $requestBody = [
                'client_id' => $this->client_id,
                'email' => $this->email,
                'name' => $this->name
            ];
            $response = $this->httpClient->postRequest('/assignment/register', $requestBody);
            $responseCode = $response->getStatusCode();

            // Invalid email/client_id/name
            switch($responseCode)
            {
                // There's no 404, only 500 with error->message
                case 500:
                    return false;
                case 200:
                {
                    $responseBody = json_decode($response->getBody()->getContents());
                    $this->sl_token = $responseBody->{'data'}->{'sl_token'};

                    return true;
                }
            }
        }
        catch (Exception $e)
        {
            print_r($e);
            return false;
        }
    }

    function getPosts(int $pageNumber) : array
    {
        $getParams = [
            'sl_token' => $this->sl_token,
            'page' => $pageNumber
        ];

        $response=$this->httpClient->getRequest('/assignment/posts', $getParams);

        // Check response code and output error?
        return json_decode($response->getBody()->getContents())->{'data'}->{'posts'};
    }

    function getPostsFromMultiplePages(int $fromPage, int $toPage) : array
    {
            $totalPages = range($fromPage,$toPage);
            $allPosts = array();

            // Can I use array_map here?
            foreach($totalPages as $pageNumber)
            {
                $postsPerPage = $this->getPosts($pageNumber);
                array_push($allPosts, ...$postsPerPage );
            }
            return $allPosts;
    }
}