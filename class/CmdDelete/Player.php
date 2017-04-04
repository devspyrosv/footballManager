<?php
namespace Football\CmdDelete;


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
            'name' => NULL,
            'surname' => NULL,
            'age' => NULL,
        );

        //Open database connection
        $this->db->connect();
        $this->showInputMenu();
    }


    private function showInputMenu(){
        
        $this->customizePlayer();
        try{
            //Delete player
            $res =  pg_query_params($this->db->getConnection(),
                'DELETE FROM players AS pl WHERE pl.name = $1 AND pl.surname=$2 AND pl.age=$3',
                array($this->dataArray['name'],$this->dataArray['surname'],$this->dataArray['age'])
            );
            if(!$res) {
                throw new \Exception("Problem deleting player!".PHP_EOL);
            }


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

        while(empty($line)){
            echo "Please try again!".PHP_EOL;
            $line = trim(fgets($handle));
        }
        return $line;
    }

    /**
     * Show menu to select team
     */
    private function customizePlayer(){

       
        $str3 = <<<'EOD'
Please enter name:
EOD;
        $str4 = <<<'EOD'
Please enter surname:
EOD;
        $str5 = <<<'EOD'
Please enter age:
EOD;
                     
        echo $str3.PHP_EOL;
        $this->dataArray['name'] = $this->inputStdinString();
        echo $str4.PHP_EOL;
        $this->dataArray['surname'] = $this->inputStdinString();
        echo $str5.PHP_EOL;
        $this->dataArray['age'] = $this->inputStdinNum();
       
        
    }    

}