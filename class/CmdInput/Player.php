<?php
namespace Football\CmdInput;


class Player
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
            'fk_nationality' => NULL,
            'fk_position_code' => NULL,
            'name' => NULL,
            'surname' => NULL,
            'age' => NULL,
            'speed' => NULL,
            'shoot' => NULL,
            'drible' => NULL,
            'defence' => NULL,
            'pass' => NULL,
        );

        //Open database connection
        $this->db->connect();
        $this->showInputMenu();
    }


    private function showInputMenu(){
        
        $this->customizePlayer();
        try{
            //Insert into the table
            set_error_handler(function() { /* ignore errors */ });            
            $res = pg_insert($this->db->getConnection(), 'players', $this->dataArray);
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
    private function inputStdinNum(){

        $handle = fopen ("php://stdin","r");
        $line = trim(fgets($handle));

        while(!is_numeric($line) || $line < 0 || $line > 100){
            echo "Please try again!".PHP_EOL;
            $line = trim(fgets($handle));
        }
        return $line;
    }

    /**
     * @return string
     * Handle stdin for numeric values
     */
    private function inputStdinString(){

        $handle = fopen ("php://stdin","r");
        $line = trim(fgets($handle));

        while(empty($line) || strlen($line) > 150){
            echo "Please try again!".PHP_EOL;
            $line = trim(fgets($handle));
        }
        return $line;
    }

    /**
     * Show menu to select team
     */
    private function customizePlayer(){

        $str1 = <<<'EOD'
Please select nationality (sorry full text):
EOD;
        $str2 = <<<'EOD'
Please select position:
EOD;
        $str3 = <<<'EOD'
Please enter name:
EOD;
        $str4 = <<<'EOD'
Please enter surname:
EOD;
        $str5 = <<<'EOD'
Please enter age:
EOD;
        $str6 = <<<'EOD'
Please enter speed:
EOD;
        $str7 = <<<'EOD'
Please enter shoot:
EOD;
        $str8 = <<<'EOD'
Please enter drible:
EOD;
        $str9 = <<<'EOD'
Please enter defence:
EOD;
        $str10 = <<<'EOD'
Please enter pass:
EOD;
        

        $safeNationalities = $this->showNationalities();
        echo $str1.PHP_EOL;
        $this->dataArray['fk_nationality'] = $this->inputStdinSafe($safeNationalities);

        $safePositions = $this->showPositions();
        echo $str2.PHP_EOL;
        $this->dataArray['fk_position_code'] = $this->inputStdinSafe($safePositions);

        echo $str3.PHP_EOL;
        $this->dataArray['name'] = $this->inputStdinString();
        echo $str4.PHP_EOL;
        $this->dataArray['surname'] = $this->inputStdinString();
        echo $str5.PHP_EOL;
        $this->dataArray['age'] = $this->inputStdinNum();
        echo $str6.PHP_EOL;
        $this->dataArray['speed'] = $this->inputStdinNum();
        echo $str7.PHP_EOL;
        $this->dataArray['shoot'] = $this->inputStdinNum();
        echo $str8.PHP_EOL;
        $this->dataArray['drible'] = $this->inputStdinNum();
        echo $str9.PHP_EOL;
        $this->dataArray['defence'] = $this->inputStdinNum();
        echo $str10.PHP_EOL;
        $this->dataArray['pass'] = $this->inputStdinNum();



    }

    /**
     * @return array
     * Shows nationality names and returns array with all the allowed ones.
     */
    private function showNationalities(){
        $res =  pg_query_params($this->db->getConnection(), 'SELECT nat.nationality FROM nationalities AS nat', array());
        $rows = pg_fetch_all($res);
        $safeArray = array();
        foreach($rows as $row){
            echo $row['nationality'].PHP_EOL;
            array_push($safeArray,$row['nationality']);
        }
        return $safeArray;
    }

    /**
     * @return array
     * Shows positions and position codes and returns array with all the allowed ones.
     */
    private function showPositions(){
        $res =  pg_query_params($this->db->getConnection(), 'SELECT pos.position, pos.position_code FROM positions AS pos', array());
        $rows = pg_fetch_all($res);
        $safeArray = array();
        foreach($rows as $row){
            echo $row['position_code'] . ':' . $row['position'] .PHP_EOL;
            array_push($safeArray,$row['position_code']);
        }
        return $safeArray;
    }

}