<?php
namespace Football\FileInput;

use DateTime;

class Match
{
    /**
     * @var \Football\DatabaseConnector\DB
     */
    private $db;

    private $filename;

    /**
     * Match constructor.
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
     * @return mixed
     * Gets the team id from its name.
     */
    private function getTeamId($name){
        $res =  pg_query_params($this->db->getConnection(), 'SELECT team_id FROM teams WHERE name = $1', array($name));
        $row = pg_fetch_row($res);
        return $row[0];
    }

    /**
     * @param $name
     * @return mixed
     * Gets the stadium_id from its name.
     */
    private function getStadiumId($name){
        $res =  pg_query_params($this->db->getConnection(), 'SELECT stadium_id FROM stadiums WHERE name = $1', array($name));
        $row = pg_fetch_row($res);
        return $row[0];
    }

    /**
     * @param $dataArrayHome
     * @return mixed
     * Inserts into table home
     */
    private function insertHome($dataArrayHome){
        $res =  pg_query_params($this->db->getConnection(),
            'INSERT INTO home (fk_team_id,goals,shots,fouls,yellows,reds) VALUES ($1,$2,$3,$4,$5,$6) RETURNING home_id',
            array($dataArrayHome['fk_team_id'],$dataArrayHome['goals'],$dataArrayHome['shots'],$dataArrayHome['fouls'],$dataArrayHome['yellows'],$dataArrayHome['reds'])
        );
        $row = pg_fetch_row($res);        
        return $row[0];
    }

    /**
     * @param $dataArrayAway
     * @return mixed
     * Inserts into table away
     */
    private function insertAway($dataArrayAway){
        $res =  pg_query_params($this->db->getConnection(),
            'INSERT INTO away (fk_team_id,goals,shots,fouls,yellows,reds) VALUES ($1,$2,$3,$4,$5,$6) RETURNING away_id',
            array($dataArrayAway['fk_team_id'],$dataArrayAway['goals'],$dataArrayAway['shots'],$dataArrayAway['fouls'],$dataArrayAway['yellows'],$dataArrayAway['reds'])
        );
        $row = pg_fetch_row($res);        
        return $row[0];
    }

    /**
     * @param $date
     * @return string
     * Formats the date
     */
    private function formatDate($date){
        $interm =  DateTime::createFromFormat('d/m/y', $date);
        return $interm->format(('Y-m-d'));
    }

    /**
     * @param $data
     * @return int|string
     * Trims and checks for empty data.
     */
    private static function checkForEmptyTrim($data){
        $data = trim($data);
        return $data?$data:0;
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

        try{
            $row = 1;
            if (($handle = fopen($this->filename, "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

                    //Skip first line
                    if($row == 1){
                        $row++;
                        continue;
                    }

                    $dataArray = array(
                        'home' => array(
                            'fk_team_id' => $this->getTeamId(trim($data[2])),
                            'goals' => Match::checkForEmptyTrim($data[4]),
                            'shots' => Match::checkForEmptyTrim($data[6]),
                            'fouls' => Match::checkForEmptyTrim($data[10]),
                            'yellows' => Match::checkForEmptyTrim($data[14]),
                            'reds' => Match::checkForEmptyTrim($data[16]),
                        ),
                        'away' => array(
                            'fk_team_id' => $this->getTeamId(trim($data[3])),
                            'goals' => Match::checkForEmptyTrim($data[5]),
                            'shots' => Match::checkForEmptyTrim($data[7]),
                            'fouls' => Match::checkForEmptyTrim($data[11]),
                            'yellows' => Match::checkForEmptyTrim($data[15]),
                            'reds' => Match::checkForEmptyTrim($data[17]),
                        ),
                        'match' => array(
                            'fk_home_id' => NULL,
                            'fk_away_id' => NULL,
                            'fk_stadium_id' => $this->getStadiumId(trim($data[22])),
                            'match_date' => $this->formatDate(trim($data[1])),
                        ),
                    );

                    /**
                     * Insert home and away and fetch their keys.
                     * Keys needed for match table.
                     */
                    $homeId = $this->insertHome($dataArray['home']);
                    $dataArray['match']['fk_home_id']  = $homeId;
                    if(is_null($homeId)){
                       throw new \Exception('Could not find key for: '.$data[2]);
                    }
                    $awayId = $this->insertAway($dataArray['away']);
                    $dataArray['match']['fk_away_id'] = $awayId;
                    if(is_null($awayId)){
                        throw new \Exception('Could not find key for: '.$data[3]);
                    }

                    /**
                     * Insert the match
                     */
                    $res = pg_insert($this->db->getConnection(), 'matches', $dataArray['match']);
                    if(!$res){
                        echo basename(__FILE__, '.php'). " Row: " . $data[2] . ' - ' . $data[3] . ' - ' . $dataArray['match']['match_date'] . " has a problem!".PHP_EOL;
                    }else{
                        //echo "Row " . $dataArray['match']['match_date'] . PHP_EOL;
                    }
                    
                    
                    $row++;
                }
                fclose($handle);                

            }
        }catch(\Exception $e){
            //echo $e->getMessage();
        }

        //Close database connection
        $this->db->disconnect();

    }
}