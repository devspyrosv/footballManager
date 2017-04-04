<?php
namespace Football\CmdInput;


class Match
{
    /**
     * @var \Football\DatabaseConnector\DB
     */
    private $db;

    private $dataArray;
    
    /**
     * Match constructor.
     * @param \Football\DatabaseConnector\DB $db
     */
    public function __construct($db)
    {
        if(is_null($db)){
            die("Error! No DB object given!".PHP_EOL);
        }
        $this->db = $db;

        $this->dataArray = array(
            'home' => array(
                'fk_team_id' => NULL,
                'goals' => NULL,
                'shots' => NULL,
                'fouls' => NULL,
                'yellows' => NULL,
                'reds' => NULL,
            ),
            'away' => array(
                'fk_team_id' => NULL,
                'goals' => NULL,
                'shots' => NULL,
                'fouls' => NULL,
                'yellows' => NULL,
                'reds' => NULL,
            ),
            'match' => array(
                'fk_home_id' => NULL,
                'fk_away_id' => NULL,
                'fk_stadium_id' => NULL,
                'match_date' => NULL,
            ),
        );

        //Open database connection
        $this->db->connect();
        $this->showInputMenu();
    }


    private function showInputMenu(){
        $this->selectHomeTeam();
        $this->selectAwayTeam();
        $this->selectStadium();
        try{
            /**
             * Insert home and away and fetch their keys.
             * Keys needed for match table.
             */
            $homeId = $this->insertHome($this->dataArray['home']);
            $this->dataArray['match']['fk_home_id']  = $homeId;
            if(is_null($homeId)){
                throw new \Exception('Could not find key for team with id: '.$this->dataArray['home']['fk_team_id']);
            }
            $awayId = $this->insertAway($this->dataArray['away']);
            $this->dataArray['match']['fk_away_id'] = $awayId;
            if(is_null($awayId)){
                throw new \Exception('Could not find key for team with id: '.$this->dataArray['away']['fk_team_id']);
            }

            /**
             * Insert the match
             */
            set_error_handler(function() { /* ignore errors */ });            
            $res = pg_insert($this->db->getConnection(), 'matches', $this->dataArray['match']);
            if(!$res) {
                restore_error_handler();
                throw new \Exception("Problem inserting! Check for duplicates!".PHP_EOL);
            }
            restore_error_handler();
        }catch(\Exception $e){
            echo $e->getMessage();
        }
        
    }

    /**
     * @return string
     * Handle safe date input from stdin
     */
    private function inputStdinSafeDate(){
        $handle = fopen ("php://stdin","r");
        $line = trim(fgets($handle));
        //http://stackoverflow.com/questions/13194322/php-regex-to-check-date-is-in-yyyy-mm-dd-format
        while(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$line)){
            echo "Please try again!".PHP_EOL;
            $line = trim(fgets($handle));
        }
        return $line;
    }

    /**
     * @param $options
     * @return string
     * Handle Stdin while checking for domain values
     */
    private function inputStdinSafe($options){

        $handle = fopen ("php://stdin","r");
        $line = trim(fgets($handle));
        while(!in_array(($line),$options)){
            echo "Please try again!".PHP_EOL;
            $line = trim(fgets($handle));
        }
        return $line;

    }

    /**
     * @return string
     * Handle stdin for numeric values
     */
    private function inputStdin(){

        $handle = fopen ("php://stdin","r");
        $line = trim(fgets($handle));

        while(!is_numeric($line)){
            echo "Please try again!".PHP_EOL;
            $line = trim(fgets($handle));
        }
        return $line;
    }

    /**
     * Show menu to select team
     */
    private function selectHomeTeam(){
        $str1 = <<<'EOD'
Please select home team id:
EOD;
        $str2 = <<<'EOD'
Please select home team goals:
EOD;
        $str3 = <<<'EOD'
Please select home team shots:
EOD;
        $str4 = <<<'EOD'
Please select home team fouls:
EOD;
        $str5 = <<<'EOD'
Please select home team yellow cards:
EOD;
        $str6 = <<<'EOD'
Please select home team red cards:
EOD;

        $safeTeamIds = $this->showTeams();
        echo $str1.PHP_EOL;
        $this->dataArray['home']['fk_team_id'] = $this->inputStdinSafe($safeTeamIds);

        echo $str2.PHP_EOL;
        $this->dataArray['home']['goals'] = $this->inputStdin();

        echo $str3.PHP_EOL;
        $this->dataArray['home']['shots'] = $this->inputStdin();

        echo $str4.PHP_EOL;
        $this->dataArray['home']['fouls'] = $this->inputStdin();

        echo $str5.PHP_EOL;
        $this->dataArray['home']['yellows'] = $this->inputStdin();

        echo $str6.PHP_EOL;
        $this->dataArray['home']['reds'] = $this->inputStdin();

    }
    
    /**
     * Show menu to select team
     */
    private function selectAwayTeam(){
        $str1 = <<<'EOD'
Please select away team id:
EOD;
        $str2 = <<<'EOD'
Please select away team goals:
EOD;
        $str3 = <<<'EOD'
Please select away team shots:
EOD;
        $str4 = <<<'EOD'
Please select away team fouls:
EOD;
        $str5 = <<<'EOD'
Please select away team yellow cards:
EOD;
        $str6 = <<<'EOD'
Please select away team red cards:
EOD;
        $safeTeamIds = $this->showTeams();
        echo $str1.PHP_EOL;
        $this->dataArray['away']['fk_team_id'] = $this->inputStdinSafe($safeTeamIds);

        echo $str2.PHP_EOL;
        $this->dataArray['away']['goals'] = $this->inputStdin();

        echo $str3.PHP_EOL;
        $this->dataArray['away']['shots'] = $this->inputStdin();

        echo $str4.PHP_EOL;
        $this->dataArray['away']['fouls'] = $this->inputStdin();

        echo $str5.PHP_EOL;
        $this->dataArray['away']['yellows'] = $this->inputStdin();

        echo $str6.PHP_EOL;
        $this->dataArray['away']['reds'] = $this->inputStdin();

    }

    /**
     * Show menu to select stadium
     */
    private function selectStadium(){
        $str1 = <<<'EOD'
Please select stadium id:
EOD;
        $str2 = <<<'EOD'
Please enter date (YYYY-MM-DD):
EOD;

        $safeStadiumIds = $this->showStadiums();
        echo $str1.PHP_EOL;
        $this->dataArray['match']['fk_stadium_id'] = $this->inputStdinSafe($safeStadiumIds);

        echo $str2.PHP_EOL;
        $this->dataArray['match']['match_date'] = $this->inputStdinSafeDate();

    }

    /**
     * @return array
     * Shows stadium names and ids and returns array with all the allowed ids.
     */
    private function showStadiums(){
        $res =  pg_query_params($this->db->getConnection(), 'SELECT stadium.stadium_id,stadium.name FROM stadiums AS stadium', array());
        $rows = pg_fetch_all($res);
        $safeArray = array();
        foreach($rows as $row){
            echo $row['stadium_id'] . ':' . $row['name'].PHP_EOL;            
            array_push($safeArray,$row['stadium_id']);
        }
        return $safeArray;
    }

    /**
     * @return array
     * Shows team names and ids and returns array with all the allowed team ids.
     */
    private function showTeams(){
        $res =  pg_query_params($this->db->getConnection(), 'SELECT team.team_id,team.name FROM teams AS team', array());
        $rows = pg_fetch_all($res);
        $safeArray = array();        
        foreach($rows as $row){
            echo $row['team_id'] . ':' . $row['name'].PHP_EOL;            
            array_push($safeArray,$row['team_id']);
        }
        return $safeArray;
    }


    /**
     * @param $dataArrayHome
     * @return mixed Inserts into table home
     * Inserts into table home
     * @throws \Exception
     */
    private function insertHome($dataArrayHome){
        set_error_handler(function() { /* ignore errors */ });        
        $res =  pg_query_params($this->db->getConnection(),
            'INSERT INTO home (fk_team_id,goals,shots,fouls,yellows,reds) VALUES ($1,$2,$3,$4,$5,$6) RETURNING home_id',
            array($dataArrayHome['fk_team_id'],$dataArrayHome['goals'],$dataArrayHome['shots'],$dataArrayHome['fouls'],$dataArrayHome['yellows'],$dataArrayHome['reds'])
        );
        if(!$res) {
            restore_error_handler();
            throw new \Exception("Problem inserting! Check for duplicates!".PHP_EOL);
        }
        restore_error_handler();
        $row = pg_fetch_row($res);
        return $row[0];
    }

    /**
     * @param $dataArrayAway
     * @return mixed Inserts into table away
     * Inserts into table away
     * @throws \Exception
     */
    private function insertAway($dataArrayAway){
        set_error_handler(function() { /* ignore errors */ });        
        $res =  pg_query_params($this->db->getConnection(),
            'INSERT INTO away (fk_team_id,goals,shots,fouls,yellows,reds) VALUES ($1,$2,$3,$4,$5,$6) RETURNING away_id',
            array($dataArrayAway['fk_team_id'],$dataArrayAway['goals'],$dataArrayAway['shots'],$dataArrayAway['fouls'],$dataArrayAway['yellows'],$dataArrayAway['reds'])
        );
        if(!$res) {
            restore_error_handler();
            throw new \Exception("Problem inserting! Check for duplicates!".PHP_EOL);
        }
        restore_error_handler();
        $row = pg_fetch_row($res);
        return $row[0];
    }

    

    
}