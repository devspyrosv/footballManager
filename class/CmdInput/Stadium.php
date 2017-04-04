<?php
namespace Football\CmdInput;


class Stadium
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
            'seats' => NULL,            
        );

        //Open database connection
        $this->db->connect();
        $this->showInputMenu();
    }


    private function showInputMenu(){

        $this->customizeStadium();
        try{
            //Insert into the table
            set_error_handler(function() { /* ignore errors */ });            
            $res = pg_insert($this->db->getConnection(), 'stadiums', $this->dataArray);
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
     * Handle stdin for numeric values
     */
    private function inputStdinNum(){

        $handle = fopen ("php://stdin","r");
        $line = trim(fgets($handle));

        while(!is_numeric($line)){
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
     * Show menu to insert Stadium
     */
    private function customizeStadium(){

        $str1 = <<<'EOD'
Please enter stadium name:
EOD;
        $str2 = <<<'EOD'
Please enter number of seats:
EOD;
        
        echo $str1.PHP_EOL;
        $this->dataArray['name'] = $this->inputStdinString();
       
        echo $str2.PHP_EOL;
        $this->dataArray['seats'] = $this->inputStdinNum();

    }

}