<?php


namespace Football\FileInput;


class PlaysFor
{
    /**
     * @var \Football\DatabaseConnector\DB
     */
    private $db;

    private $filename;

    /**
     * PlaysFor constructor.
     * @param null $filename
     * @param null $db
     */
    public function __construct($filename = null, $db = null){

        if(is_null($filename)){
            die("Error! No filename given in " . basename(__FILE__, '.php') . "!".PHP_EOL);
        }

        if(is_null($db)){
            die("Error! No DB object given!".PHP_EOL);
        }

        $this->filename = $filename;
        $this->db = $db;

    }

    /**
     * @param $name
     * @param $surname
     * @param $age
     * @return mixed
     * Gets player id from his name
     */
    private function getPlayerId($name,$surname,$age){
        //Check for empty age...
        $age = $age?$age:0;
        $res =  pg_query_params($this->db->getConnection(), 'SELECT player_id FROM players WHERE name = $1 AND surname = $2 AND age = $3', array($name,$surname,$age));
        $row = pg_fetch_row($res);
        return $row[0];
    }

    /**
     * @param $name_short
     * @return mixed
     * Gets the team id from its name
     */
    private function getTeamId($name_short){
        $res =  pg_query_params($this->db->getConnection(), 'SELECT team_id FROM teams WHERE name_short = $1', array($name_short));
        $row = pg_fetch_row($res);
        return $row[0];
    }

    /**
     * Reads the file containing the data and stores it in the database table.
     */
    public function readFile(){

        if(!is_readable($this->filename)){
            die(basename(__FILE__, '.php'). " Error! File: $this->filename is not readable!".PHP_EOL);
        }
        //Open database connection
        $this->db->connect();

        $dataArray = array();

        try{
            $row = 1;
            if (($handle = fopen($this->filename, "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

                    //Skip first line
                    if($row == 1){
                        $row++;
                        continue;
                    }

                    $player_id = $this->getPlayerId(trim($data[0]),trim($data[1]),trim($data[5])); //0,1,5
                    $team_id = $this->getTeamId(trim($data[3]));
                    if(!is_null($player_id)){
                        $dataArray[$player_id.'_'.$team_id] = array(
                            'fk_player_id' => $player_id,
                            'fk_team_id' => $team_id
                        );                        
                    }
                    $row++;
                }
                fclose($handle);
                
                $row = 1;
                foreach($dataArray as $key=>$val){

                    //Insert into the table
                    $res = pg_insert($this->db->getConnection(), 'plays_for', $val);
                    if(!$res){
                        echo basename(__FILE__, '.php'). " Row for player: " .$data[0] .$data[1] . " in team: " . $data[3]. " has a problem.".PHP_EOL;
                    }else{
                        //echo "Row $key.".PHP_EOL;
                    }
                    $row++;
                }

            }
        }catch(\Exception $e){
            //echo $e->getMessage();
        }

        //Close database connection
        $this->db->disconnect();

    }
}