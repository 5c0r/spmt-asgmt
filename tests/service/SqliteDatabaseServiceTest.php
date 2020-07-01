<?php

namespace Integration;

use App\service\SqliteDatabaseService;
use PHPUnit\Framework\TestCase;

class SqliteDatabaseServiceTest extends TestCase
{
    private SqliteDatabaseService $serviceUnderTest;
    private string $sqlitePath = 'test.db';

    public function testServiceShouldWork()
    {
        $serviceUnderTest = new SqliteDatabaseService($this->sqlitePath);
        $this->assertNotNull($serviceUnderTest->instance);
    }
}
