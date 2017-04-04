<?php
namespace Football\FileInput;

class Team
{
    private $teamNamesFilename;

    private $matchesFilename;



    /**
     * @var \Football\DatabaseConnector\DB
     */
    private $db;


    /**
     * Team constructor.
     * @param null $teamNamesFilename
     * @param null $matchesFilename
     * @param null $db    
     */
    public function __construct($matchesFilename = null, $teamNamesFilename = null, $db = null){

        if(is_null($teamNamesFilename)){
            die("Error! No filename given in " . basename(__FILE__, '.php') . "!".PHP_EOL);
        }
        if(is_null($matchesFilename)){
            die("Error! No filename given in " . basename(__FILE__, '.php') . "!".PHP_EOL);
        }
        if(is_null($db)){
            die("Error! No DB object given!".PHP_EOL);
        }


        $this->teamNamesFilename = $teamNamesFilename;
        //echo $this->teamNamesFilename . "team".PHP_EOL;
        $this->matchesFilename = $matchesFilename;
        //echo $this->matchesFilename . "match".PHP_EOL;
        $this->db = $db;


    }

    /**
     * @param $param
     * @return mixed
     * Gets the city id.
     */
    private function getCityId($param){        
        $res =  pg_query_params($this->db->getConnection(), 'SELECT city_id FROM cities WHERE city = $1', array($param));
        $row = pg_fetch_row($res);
        return $row[0];
    }

    /**
     * @param $param
     * @return mixed
     * Gets the league id
     */
    private function getLeagueId($param){
        $res = pg_query_params($this->db->getConnection(), 'SELECT league_id FROM leagues WHERE league = $1', array($param));
        $row = pg_fetch_row($res);
        return $row[0];
    }

    /**
     * Read the files.
     */
    public function readFiles(){
        $teamNamesArray = $this->readTeamNamesFile();
        $matchesArray = $this->readMatchesFile();

        //match arrays
        //There are certain omissions in the teams' names (eg. AEK is nowhere to be found). 
        foreach($matchesArray as $teamName => $data){            
            if(isset($teamNamesArray[$data['name']])){
                //search by key
                $matchesArray[$teamName]['name_short'] = $teamNamesArray[$data['name']];                
            }else if($key = array_search($data['name'],$teamNamesArray)){
                //search by value and get key.
                $matchesArray[$teamName]['name_short'] = $key;
            }
        }


        //Now matchesarray has all the data a team needs.
        //Open database connection
        $this->db->connect();
        try{
            $row = 0;
            foreach($matchesArray as $teamName => $data){


                $city_id = $this->getCityId($data['city']);
                $league_id = $this->getLeagueId($data['league']);
                
                //Create assoc array for pg_insert.
                $temp = array(
                    'est' => $data['est']?$data['est']:0,
                    'name' => $data['name'],
                    'fk_city_id' => $city_id?$city_id:NULL,
                    'fk_league_id' => $league_id?$league_id:NULL,
                    'name_short' => $data['name_short']
                );
               
                //Insert into the table
                $res = pg_insert($this->db->getConnection(), 'teams', $temp);
                if(!$res){
                    echo basename(__FILE__, '.php') . " Row: " . $data['name'] . " has a problem!".PHP_EOL;
                }else{
                    //echo "Row $row:  $teamName.".PHP_EOL;
                }
                $row++;
            }
        }catch(\Exception $e){
            //echo $e->getMessage();
        }
        //Close database connection
        $this->db->disconnect();
    }

    private function readMatchesFile(){
        //Set encoding for mb_functions.
        mb_internal_encoding("UTF-8");

        if(!is_readable($this->matchesFilename)){
            die(basename(__FILE__, '.php')." Error! File: $this->matchesFilename is not readable!".PHP_EOL);
        }

        $dataArray = array();


        try{
            $row = 1;
            $dataArray = array();
            if (($handle = fopen($this->matchesFilename, "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

                    //Skip first line
                    if($row == 1){
                        $row++;
                        continue;
                    }

                    $dataArray[trim($data[2])] = array(
                        'est' => trim($data[18]?$data[18]:0),
                        'name' => trim($data[2]),
                        'city' => trim($data[19]),
                        'league' => trim($data[0]),
                        'name_short' => NULL,

                    );
                    $dataArray[trim($data[3])] = array(
                        'est' => trim($data[20]?$data[20]:0),
                        'name' => trim($data[3]),
                        'city' => trim($data[21]),
                        'league' => trim($data[0]),
                        'name_short' => NULL,
                    );




                    $row++;
                }
                fclose($handle);

            }
        }catch(\Exception $e){
            //echo $e->getMessage();
        }

        return $dataArray;
    }

    /**
     * @return array
     */
    private function readTeamNamesFile(){
        
        //Set encoding for mb_functions.
        //
        mb_internal_encoding("UTF-8");
        
        if(!is_readable($this->teamNamesFilename)){
            die(basename(__FILE__, '.php') ." Error! File: $this->teamNamesFilename is not readable!".PHP_EOL);
        }

        $normalizedArray = array();


        try{
            $row = 1;
            if (($handle = fopen($this->teamNamesFilename, "r")) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

                    $data[1] = trim($data[1]);
                    $data[0] = trim($data[0]);
                    //Skip first line
                    if($row == 1){
                        $row++;
                        continue;
                    }
                    
                    /**
                     * The first time we find the team id
                     * we just add to the array whatever we find.
                     */
                    if(!array_key_exists($data[1],$normalizedArray)){
                        $normalizedArray[$data[1]] = array(
                            'short' => $data[0],
                            'long' => $data[0]
                        );                      
                    }else{
                        /**
                         * If the short version is longer than the long version, exchange them.
                         */
                        if(mb_strlen($normalizedArray[$data[1]]['short']) >  mb_strlen($data[0])){                            
                            $normalizedArray[$data[1]]['long'] = $normalizedArray[$data[1]]['short'];
                            $normalizedArray[$data[1]]['short'] = $data[0];
                        }
                        if(mb_strlen($normalizedArray[$data[1]]['long']) <  mb_strlen($data[0])){
                            $normalizedArray[$data[1]]['long'] = $data[0];                            ;
                        }
                        
                    }




                    $row++;
                }
                fclose($handle);
            }
        }catch(\Exception $e){
            //echo $e->getMessage();
        }
        
        $finalArray = array();
        foreach($normalizedArray as $key=>$data){
            $finalArray[$data['long']] = $data['short'];
        }
        
        //var_dump($finalArray);
        return $finalArray;
    }

    
}