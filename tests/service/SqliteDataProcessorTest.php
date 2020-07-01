<?php

namespace service;

use App\model\DataProcessingInterface;
use App\service\SqliteDatabaseService;
use App\Service\SqliteDataProcessor;
use App\Service\SupermetricsService;
use PHPUnit\Framework\TestCase;

class SqliteDataProcessorTest extends TestCase
{
    private DataProcessingInterface $serviceUnderTest;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $dataSvcToInject = new SqliteDatabaseService('processing_test.db');
        $supermetricSvcToInject = new SupermetricsService('https://api.supermetrics.com',
            'ju16a6m81mhid5ue1z3v2g0uh','test@test.com', 'Testeri');

        $this->serviceUnderTest = new SqliteDataProcessor($dataSvcToInject, $supermetricSvcToInject);
    }

    // TODO: Remove processing_test.db and uncomment to start importing new data
    // public function testIfWorking()
    // {
    //     $this->assertTrue($this->serviceUnderTest->initializeDatabase());
    // }

    // public function testAddStuff()
    // {
    //     $this->serviceUnderTest->initializeData();
    // }
    // End of TODO

    public function testCanGetAverageCharacterLengthPerMonth()
    {
        $result = $this->serviceUnderTest->getAverageCharacterLengthPerMonth();

        print_r(json_encode($result));

        $this->assertGreaterThan(0, sizeof($result));
    }

    public function testCanGetLongestPostPerMonth()
    {
        $result = $this->serviceUnderTest->getLongestPostPerMonth();

        print_r(json_encode($result));

        $this->assertGreaterThan(0, sizeof($result));
    }

    public function testCanGetTotalPostsSplitByWeekNumber()
    {
        $result = $this->serviceUnderTest->getTotalPostsSplitByWeek();

        print_r(json_encode($result));
        $this->assertGreaterThan(0, sizeof($result));
    }

    public function testCanGetAveratePostPerUserPerMonth()
    {
        $result = $this->serviceUnderTest->getAveragePostPerUserPerMonth();

        print_r(json_encode($result));
        $this->assertGreaterThan(0, sizeof($result));
    }
}
