### Introduction
This is an Assignment from Supermetrics . Tested on Both Windows and Arch Linux

### How to run the project
- Make sure you have php_sqlite3/curl/pdo available in php.ini 
    - `extension=pdo_sqlite` and `extension=curl` and `extension=sqlite3` should be uncommented in `php.ini`
- `composer install`
- `SqliteDataProcessorTest.php` should have all the tests , you can run the tests with PhpStorm
- Imported data is already initialized in `processing_test.db` , in case you would like to try re-initialize data:
    - Remove the db file
    - Uncomment first two test cases `testIfWorking` , `testAddStuff`

### What has been done ?
- Used `Guzzle` (https://github.com/guzzle/guzzle) for HTTP requests, it seems to wrap `cURL` so might be handy
- Made use of `Sqlite3` for data querying instead of doing things in-memory.
- `PHPUnit` for Integration tests , but all assignment requirements should be run under tests

### Why ?
- Using SQL for querying/reporting would be a preferable way 
- I personally would prefer something LINQ-ish to manage Collections in memory, but PHP's array is special :)

### What I would like to do || What could be improved ? If I have more time
- Trying Dependency Injection with DI , so use environment variables instead of poor-man's DI
- Wrap a rest API
- Do exception handling
- Retry strategy for failure HTTP requests..
- Finding out how to serialize->deserialize data more than just json_encode/decode...