<?php
require_once("db/DB.php");
require_once("class/CmdInput/Match.php");
require_once("class/CmdInput/Player.php");
require_once("class/CmdInput/Stadium.php");
require_once("class/CmdInput/Team.php");

use Football\CmdInput\Match;
use Football\CmdInput\Player;
use Football\CmdInput\Stadium;
use Football\CmdInput\Team;
use Football\DatabaseConnector\DB as DB;

$str = <<<'EOD'
/****************************************************/
 _    _      _                          _ 
| |  | |    | |                        | |
| |  | | ___| | ___ ___  _ __ ___   ___| |
| |/\| |/ _ \ |/ __/ _ \| '_ ` _ \ / _ \ |
\  /\  /  __/ | (_| (_) | | | | | |  __/_|
 \/  \/ \___|_|\___\___/|_| |_| |_|\___(_)
                                          
/****************************************************/
          INSERT OBJECTS MENU
------------------------------------------------------
1. Match
2. Player
3. Team
4. Stadium

Please input the number of the object you would like 
to insert: 
------------------------------------------------------


EOD;

echo $str;

$handle = fopen ("php://stdin","r");
$line = trim(fgets($handle));
$options = array(1,2,3,4);
if(!in_array(($line),$options)){
    echo "ABORTING!\n";
    exit;
}

//Create a shared database object.
$db = new DB();

switch ($line){
    case 1: 
        $match = new Match($db);            
        break;
    case 2: 
        $player = new Player($db);
        break;
    case 3: 
        $team = new Team($db);
        break;
    case 4: 
        $stadium = new Stadium($db);
        break;
    default:
        break;
}