<?php
namespace Football\FileInput;
class Player
{
    private $filename;

    /**
     * @var \Football\DatabaseConnector\DB
     */
    private $db;

    /**
     * Player constructor.
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
            $dataArray = array();
            $row = 1;
            if (($handle = fopen($this->filename, "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

                    //Skip first line
                    if($row == 1){
                        $row++;
                        continue;
                    }

                    //Keep only the unique rows
                    $dataArray[$data[0].$data[1].$data[5]] = array(
                        'fk_nationality' => trim($data['4']),
                        'fk_position_code' => trim($data['2']),
                        'name' => trim($data['0']),
                        'surname' => trim($data['1']),
                        'age' => Player::checkForEmptyTrim($data['5']),
                        'speed' => Player::checkForEmptyTrim($data['6']),
                        'shoot' => Player::checkForEmptyTrim($data['8']),
                        'drible' => Player::checkForEmptyTrim($data['7']),
                        'defence' => Player::checkForEmptyTrim($data['9']),
                        'pass' => Player::checkForEmptyTrim($data['10']),

                    );

                    $row++;
                }
                fclose($handle);

                $row=1;
                foreach($dataArray as $key => $data){
                    
                    //Insert into the table
                    $res = pg_insert($this->db->getConnection(), 'players', $data);
                    if(!$res){
                        echo basename(__FILE__, '.php'). " Row: " . $data['0'] . '-' . $data['1'] . " has a problem!".PHP_EOL;
                    }else{
                        //echo "Row $row:  $key".PHP_EOL;
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