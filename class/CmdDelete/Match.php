<?php
namespace Football\CmdDelete;


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
            ),
            'away' => array(
                'fk_team_id' => NULL,
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
             * Delete the match
             */            
            $res =  pg_query_params($this->db->getConnection(),
                'DELETE FROM matches WHERE fk_home_id = $1 AND fk_away_id = $2 AND fk_stadium_id = $3 AND match_date = $4',
                array($this->dataArray['match']['fk_home_id'],$this->dataArray['match']['fk_away_id'], $this->dataArray['match']['fk_stadium_id'], $this->dataArray['match']['match_date'])
            );            
            if(!$res) {               
                throw new \Exception("Problem deleting match!".PHP_EOL);
            }            

            /**
             * DELETE home and away and fetch their keys.
             * Keys needed for match table.
             */
            $this->deleteHome();


            $this->deleteAway();



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


        $safeTeamIds = $this->showTeams();
        echo $str1.PHP_EOL;
        $this->dataArray['home']['fk_team_id'] = $this->inputStdinSafe($safeTeamIds);



    }
    
    /**
     * Show menu to select team
     */
    private function selectAwayTeam(){
        $str1 = <<<'EOD'
Please select away team id:
EOD;

        $safeTeamIds = $this->showTeams();
        echo $str1.PHP_EOL;
        $this->dataArray['away']['fk_team_id'] = $this->inputStdinSafe($safeTeamIds);



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

        $res =  pg_query_params($this->db->getConnection(), 'SELECT * FROM matches WHERE fk_stadium_id = $1 AND match_date = $2', array( $this->dataArray['match']['fk_stadium_id'], $this->dataArray['match']['match_date']));
        $row = pg_fetch_assoc($res);        
        if(!is_null($row) && isset($row['fk_away_id']) && isset($row['fk_home_id'])){
            $this->dataArray['match']['fk_away_id'] = $row['fk_away_id'];
            $this->dataArray['match']['fk_home_id'] = $row['fk_home_id'];            
        }
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
     * @return mixed Deletes from table home
     * @throws \Exception
     */
    private function deleteHome(){
        set_error_handler(function() { /* ignore errors */ });        
        $res =  pg_query_params($this->db->getConnection(),
            'DELETE FROM home WHERE fk_team_id = $1 AND home_id = $2',
            array($this->dataArray['home']['fk_team_id'],$this->dataArray['match']['fk_home_id'])
        );
        if(!$res) {
            restore_error_handler();
            throw new \Exception("Problem deleting home!".PHP_EOL);
        }
        restore_error_handler();
        return $res;
    }

    /**
     * @return mixed deletes from table away
     * @throws \Exception
     */
    private function deleteAway(){
        set_error_handler(function() { /* ignore errors */ });        
        $res =  pg_query_params($this->db->getConnection(),
            'DELETE FROM away WHERE fk_team_id = $1 AND away_id = $2',
            array($this->dataArray['away']['fk_team_id'],$this->dataArray['match']['fk_away_id'])
        );
        if(!$res) {
            restore_error_handler();
            throw new \Exception("Problem deleting away!".PHP_EOL);
        }
        restore_error_handler();
        return $res;
    }

    

    
}