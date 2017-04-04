<?php
namespace Football\CmdDelete;


class Team
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
            
            'name' => NULL,            
            'fk_city_id' => NULL,
            'fk_league_id' => NULL,
        );

        //Open database connection
        $this->db->connect();
        $this->showInputMenu();
    }


    private function showInputMenu(){

        $this->customizeTeam();
        try{
            //Delete
            $res =  pg_query_params($this->db->getConnection(),
                'DELETE FROM teams AS t WHERE t.name = $1 AND t.fk_city_id=$2 AND t.fk_league_id=$3',
                array($this->dataArray['name'],$this->dataArray['fk_city_id'],$this->dataArray['fk_league_id'])
            );
            if(!$res) {
                throw new \Exception("Problem deleting team!".PHP_EOL);
            }

        }catch(\Exception $e){
            echo $e->getMessage();
        }

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
     * Handle stdin for string values
     */
    private function inputStdinString(){

        $handle = fopen ("php://stdin","r");
        $line = trim(fgets($handle));

        while(empty($line)){
            echo "Please try again!".PHP_EOL;
            $line = trim(fgets($handle));
        }
        return $line;
    }

    /**
     * Show menu to select team
     */
    private function customizeTeam(){

        $str1 = <<<'EOD'
Please select city id:
EOD;
        $str2 = <<<'EOD'
Please league id:
EOD;
        $str3 = <<<'EOD'
Please enter name:
EOD;




        $safeCities = $this->showCities();
        echo $str1.PHP_EOL;
        $this->dataArray['fk_city_id'] = $this->inputStdinSafe($safeCities);

        $safeLeagues = $this->showLeagues();
        echo $str2.PHP_EOL;
        $this->dataArray['fk_league_id'] = $this->inputStdinSafe($safeLeagues);

        echo $str3.PHP_EOL;
        $this->dataArray['name'] = $this->inputStdinString();
        




    }

    /**
     * @return array
     * Shows city names and ids and returns array with all the allowed ones.
     */
    private function showCities(){
        $res =  pg_query_params($this->db->getConnection(), 'SELECT cit.city_id,cit.city FROM cities AS cit', array());
        $rows = pg_fetch_all($res);
        $safeArray = array();
        foreach($rows as $row){
            echo $row['city_id'].':'.$row['city'].PHP_EOL;
            array_push($safeArray,$row['city_id']);
        }
        return $safeArray;
    }

    /**
     * @return array
     * Shows league names and ids and returns array with all the allowed ones.
     */
    private function showLeagues(){
        $res =  pg_query_params($this->db->getConnection(), 'SELECT leg.league_id, leg.league FROM leagues AS leg', array());
        $rows = pg_fetch_all($res);
        $safeArray = array();
        foreach($rows as $row){
            echo $row['league_id'] . ':' . $row['league'] .PHP_EOL;
            array_push($safeArray,$row['league_id']);
        }
        return $safeArray;
    }
}