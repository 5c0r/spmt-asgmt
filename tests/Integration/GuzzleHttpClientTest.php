<?php

namespace App\Tests\Integration;

use HttpClientTests;
use App\Service\GuzzleHttpClient;
use PHPUnit\Framework\TestCase;

class GuzzleHttpClientTest extends TestCase
{
    private GuzzleHttpClient $clientUnderTests;

    public function testClientShouldAbleToGet()
    {
        $this->clientUnderTests  = new GuzzleHttpClient('https://google.com');
        $result = $this->clientUnderTests->getRequest('', null);
        $this-> assertEquals(200, $result->getStatusCode());
    }

    public function testClientShouldBeAbleToPost()
    {
        $postData = [
            'client_id' => 'ju16a6m81mhid5ue1z3v2g0uh',
            'email' => 'john_doe@mail.com',
            'name' => 'Octane'
        ];
        $this->clientUnderTests = new GuzzleHttpClient('https://api.supermetrics.com');
        $result = $this->clientUnderTests
            ->postRequest('/assignment/register', $postData);

        $statusCode = $result -> getStatusCode();
        $responseBody = $result -> getBody()->getContents();

        $this->assertEquals(200, $statusCode);
        $this->assertNotEmpty($responseBody);
    }


}
