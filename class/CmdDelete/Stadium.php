<?php
namespace Football\CmdDelete;


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
        );

        //Open database connection
        $this->db->connect();
        $this->showInputMenu();
    }


    private function showInputMenu(){

        $this->customizeStadium();
        try{            
            $res =  pg_query_params($this->db->getConnection(),
                'DELETE FROM stadiums AS std WHERE std.name = $1',
                array($this->dataArray['name'])
            );
            if(!$res) {
                throw new \Exception("Problem deleting stadium!".PHP_EOL);
            }            

        }catch(\Exception $e){
            echo $e->getMessage();
        }

    }

     

    /**
     * @return string
     * Handle stdin for numeric values
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
     * Show menu to delete Stadium
     */
    private function customizeStadium(){

        $str1 = <<<'EOD'
Please enter stadium name (You cannot delete stadiums that are still linked to matches!):
EOD;
        
        echo $str1.PHP_EOL;
        $this->dataArray['name'] = $this->inputStdinString();       

    }

}