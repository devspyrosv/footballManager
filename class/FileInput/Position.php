<?php
namespace Football\FileInput;

class Position
{
    private $filename;

    /**
     * @var \Football\DatabaseConnector\DB
     */
    private $db;

    /**
     * Position constructor.
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
                    
                    //Keep only the unique rows
                    $dataArray[trim($data[1])] = trim($data[0]);
                    
                    $row++;
                }
                fclose($handle);
                $row = 1;
                foreach($dataArray as $code => $pos){
                    //Create assoc array for pg_insert.
                    $temp = array(
                        'position' => $pos,
                        'position_code' => $code
                    );

                    //Insert into the table
                    $res = pg_insert($this->db->getConnection(), 'positions', $temp);
                    if(!$res){
                        echo basename(__FILE__, '.php'). " Row: $pos has a problem!".PHP_EOL;
                    }else{
                        //echo "Row $row:  $pos, $code".PHP_EOL;
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