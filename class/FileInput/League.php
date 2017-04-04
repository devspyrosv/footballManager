<?php

namespace Football\FileInput;


class League
{
    /**
     * @var \Football\DatabaseConnector\DB
     */
    private $db;

    private $filename;

    /**
     * League constructor.
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
                    array_push($dataArray,trim($data[0]));

                    $row++;
                }
                fclose($handle);

                //Keep only the unique rows
                $normalizedArray = array();
                foreach($dataArray as $key=>$val){
                    $normalizedArray[$val] = true;
                }
                $normalizedArray = array_keys($normalizedArray);
                //Set encoding for sorting
                mb_internal_encoding("UTF-8");
                sort($normalizedArray,SORT_LOCALE_STRING);

                $row = 1;
                foreach($normalizedArray as $val){

                    $temp = array('league' => $val);

                    //Insert into the table
                    $res = pg_insert($this->db->getConnection(), 'leagues', $temp);
                    if(!$res){
                        echo basename(__FILE__, '.php'). " Row: $val has a problem!".PHP_EOL;
                    }else{
                        //echo "Row $val.".PHP_EOL;
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