<?php
namespace App\service;


class SqliteDatabaseService
{
    // Would prefer to use statement, but not sure if it is applicable here
    public \SQLite3 $instance;

    public function __construct(string $dbPath) {

        $this->instance=new \SQLite3($dbPath);
    }

    public function executeWrite(string $query) : bool
    {
        return $this->instance->exec($query);
    }

    public function executeRead(string $query) : array
    {
        $statement = $this->instance->prepare($query);
        $result = $statement->execute();
        $arrResult = [];

        while($row = $result->fetchArray())
        {
            array_push($arrResult, $row);
        }
        return $arrResult;
    }
}