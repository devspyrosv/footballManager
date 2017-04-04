<?php
require_once("db/DB.php");
require_once("class/CmdDelete/Match.php");
require_once("class/CmdDelete/Player.php");
require_once("class/CmdDelete/Stadium.php");
require_once("class/CmdDelete/Team.php");

use Football\CmdDelete\Match;
use Football\CmdDelete\Player;
use Football\CmdDelete\Stadium;
use Football\CmdDelete\Team;
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
          DELETE OBJECTS MENU
------------------------------------------------------
1. Match
2. Player
3. Team
4. Stadium

Please input the number of the object you would like 
to delete: 
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

