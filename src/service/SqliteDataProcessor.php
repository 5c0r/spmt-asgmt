<?php


namespace App\Service;


use App\model\DataProcessingInterface;
use App\Model\SupermetricsServiceInterface;

class SqliteDataProcessor implements DataProcessingInterface
{
    // Hopefully we had time for DI
    private SqliteDatabaseService $dataService;
    private SupermetricsServiceInterface $superSvc;

    private array $dataFromSupermetrics = [];

    public function __construct(SqliteDatabaseService $dataSvc, SupermetricsServiceInterface $superSvc)
    {
        $this->dataService=$dataSvc;
        $this->superSvc=$superSvc;
    }

    function initializeDatabase()
    {
        $success = true;

        $tableCommands = [
            'CREATE TABLE IF NOT EXISTS users( user_id TEXT PRIMARY KEY, user_name TEXT NOT NULL )',
            'CREATE TABLE IF NOT EXISTS posts( id TEXT PRIMARY KEY, message TEXT, 
                type TEXT, created_time TEXT NOT NULL,
                user_id TEXT,
                FOREIGN KEY(user_id) REFERENCES users (user_id) ON UPDATE CASCADE ON DELETE CASCADE  )'
        ];

        foreach ($tableCommands as $command)
        {
            $success = $this->dataService->executeWrite($command);
        }

        return $success;
    }

    function initializeData()
    {
        $this->dataFromSupermetrics = [];

        $this->superSvc->authenticate();
        $this->dataFromSupermetrics = $this->superSvc->getPostsFromMultiplePages(0,10);

        $this->getAndSaveAllUsers();
        $this->getAndSaveAllPosts();
    }

    private function getAndSaveAllUsers()
    {
        $userIdColumn = 'from_id';
        $allUsers = $this->getUserFromAllPosts();
//            array_unique(array_column($this->dataFromSupermetrics,$userIdColumn));

        foreach ($allUsers as $user_id => $user)
        {
            $statement = $this->dataService->instance->prepare('INSERT INTO users VALUES(:user_id,:user_name)');
            $statement->bindValue(':user_id', $user->{'from_id'});
            $statement->bindValue(':user_name', $user->{'from_name'});

            $result = $statement->execute();
        }
    }

    // Maybe this is the best "approach" to get unique stuff , prove me wrong :D
    private function getUserFromAllPosts() : array
    {
        $allUsers = [];

        foreach($this->dataFromSupermetrics as $data)
        {
            $newUser = new \stdClass();
            $newUser->{'from_id'}=$data->{'from_id'};
            $newUser->{'from_name'}=$data->{'from_name'};

            $allUsers[$data->{'from_id'}] = $newUser;
        }

        return $allUsers;
    }

    private function getAllPosts(): array {
        $allPosts = [];

        foreach($this->dataFromSupermetrics as $data)
        {
            $allPosts[$data->{'id'}] = $data;
        }

        return $allPosts;
    }

    private function getAndSaveAllPosts()
    {
        foreach($this->getAllPosts() as $post_id => $post)
        {
            $statement = $this->dataService->instance->prepare('INSERT INTO posts VALUES(:id,:message,:type,:created_time,:user_id)');
            $statement->bindValue(':id', $post->{'id'});
            $statement->bindValue(':message', $post->{'message'});
            $statement->bindValue(':type', $post->{'message'});
            $statement->bindValue(':created_time', $post->{'created_time'});
            $statement->bindValue(':user_id', $post->{'from_id'});

            $result = $statement->execute();
        }
    }

    function getAverageCharacterLengthPerMonth() : array
    {
        $sqlQuery = 'SELECT avg(a.len) as averageLen , a.month, a.year
                        FROM ( 
                            SELECT length(message) as len, strftime(\'%m\',created_time) as month, strftime(\'%Y\',created_time) as year from posts
                            ) as a
                        GROUP BY a.month';

        $sqlResults = $this->dataService->executeRead($sqlQuery);
        $arrResult = [];

        foreach($sqlResults as $row)
        {
            $newRow = new \stdClass();
            $newRow->{'averageLen'}=$row['averageLen'];
            $newRow->{'month'}=$row['month'];
            $newRow->{'year'}=$row['year'];

            $arrResult[] = $newRow;
        }

        return $arrResult;
    }

    function getLongestPostPerMonth()
    {
        $sqlQuery = 'SELECT max(a.len) as maxLen , a.month, a.year
                        FROM ( 
                            SELECT length(message) as len, strftime(\'%m\',created_time) as month, strftime(\'%Y\',created_time) as year from posts
                            ) as a
                        GROUP BY a.month';

        $sqlResults = $this->dataService->executeRead($sqlQuery);
        $arrResult = [];

        foreach($sqlResults as $row)
        {
            $newRow = new \stdClass();
            $newRow->{'maxLen'}=$row['maxLen'];
            $newRow->{'month'}=$row['month'];
            $newRow->{'year'}=$row['year'];

            $arrResult[] = $newRow;
        }

        return $arrResult;
    }

    function getAveragePostPerUserPerMonth()
    {
        $sqlQuery = 'SELECT u.user_id ,u.user_name , AVG(p.monthlyPost) as averagePpm
            FROM (
            SELECT posts.user_id, count(*) as monthlyPost,strftime(\'%m\',created_time) as month
            FROM posts
            GROUP BY posts.user_id, month
            ) as p
            INNER JOIN users u ON u.user_id = p.user_id
            GROUP BY u.user_id';

        $sqlResults = $this->dataService->executeRead($sqlQuery);
        $arrResult = [];

        foreach($sqlResults as $row)
        {
            $newRow = new \stdClass();
            $newRow->{'user_id'}=$row['user_id'];
            $newRow->{'user_name'}=$row['user_name'];
            $newRow->{'averagePpm'}=$row['averagePpm'];

            $arrResult[] = $newRow;
        }
        return $arrResult;
    }

    function getTotalPostsSplitByWeek()
    {
        $sqlQuery = 'SELECT strftime(\'%W\',created_time) as weekNumber,strftime(\'%Y\',created_time) as year, COUNT(*) as total FROM posts GROUP BY weekNumber,year';

        $sqlResults = $this->dataService->executeRead($sqlQuery);
        $arrResult = [];

        foreach($sqlResults as $row)
        {
            $newRow = new \stdClass();
            $newRow->{'weekNumber'}=$row['weekNumber'];
            $newRow->{'year'}=$row['year'];
            $newRow->{'total'}=$row['total'];

            $arrResult[] = $newRow;
        }

        return $arrResult;
    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
        $this->dataService->instance->close();
    }
}