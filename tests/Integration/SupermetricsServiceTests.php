<?php

namespace App\Tests\Integration;

use PHPUnit\Framework\TestCase;
use App\Service\SupermetricsService;

class SupermetricsServiceTests extends TestCase
{
    private SupermetricsService $serviceUnderTest;

    // setUp ?
    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->serviceUnderTest = new SupermetricsService('https://api.supermetrics.com',
            'ju16a6m81mhid5ue1z3v2g0uh','test@test.com', 'Testeri');
    }

    public function testCanAuthenticate()
    {
        $authenticated = $this->serviceUnderTest->authenticate();
        $this->assertTrue($authenticated);
    }

    public function testCanGetPosts()
    {
        $this->testCanAuthenticate();
        $response = $this->serviceUnderTest->getPosts(1);

        $this->assertNotNull($response);
        $this->assertEquals(100, sizeOf($response));
    }

    public function testCanGetAllPosts()
    {
        $this->testCanAuthenticate();
        $postsArray = $this->serviceUnderTest->getPostsFromMultiplePages(1, 10);

        $this->assertEquals(1000, sizeof($postsArray));
    }
}
