<?php
require_once("db/DB.php");
require_once("class/FileInput/Position.php");
require_once("class/FileInput/Team.php");
require_once("class/FileInput/League.php");
require_once("class/FileInput/Nationality.php");
require_once("class/FileInput/City.php");
require_once("class/FileInput/Stadium.php");
require_once("class/FileInput/Player.php");
require_once("class/FileInput/PlaysFor.php");
require_once("class/FileInput/Match.php");


use Football\FileInput\Player;
use Football\FileInput\Position;
use Football\FileInput\Team;
use Football\FileInput\League;
use Football\FileInput\City;
use Football\FileInput\Nationality;
use Football\FileInput\Stadium;
use Football\FileInput\PlaysFor;
use Football\FileInput\Match;
use Football\DatabaseConnector\DB as DB;

//Short options
$shortOpts = "";
$shortOpts .= "t:";//type
$shortOpts .= "f:";//file 1
$shortOpts .= "g::";//file 2
$shortOpts .= "h::";//file 3
$shortOpts .= "i::";//file 4

$options = getopt($shortOpts);

//var_dump($options);

//Create a shared database object.
$db = new DB();

if($options['t'] == 'position'){
    /**
     * php inputFromFile.php -t position -f "data/Θέσεις_Παιχτών.csv" 
     */
    $positions = new Position($options['f'], $db);
    $positions->readFile();
}
else if($options['t'] == 'league'){
    /**
     * php inputFromFile.php -t league -f "data/Αγώνες.csv"
     */
    $league = new League($options['f'], $db);
    $league->readFile();
}
else if($options['t'] == 'city'){
    
    /**
     * php inputFromFile.php -t city -f "data/Αγώνες.csv" 
     */
    $cities = new City($options['f'], $db);
    $cities->readFile();
}
else if($options['t'] == 'stadium'){
    
    /**
     * php inputFromFile.php -t stadium -f "data/Αγώνες.csv"
     */
    $stadiums = new Stadium($options['f'], $db);
    $stadiums->readFile();
}
else if($options['t'] == 'nationality'){
    /**
     * php inputFromFile.php -t nationality -f "data/Παίχτες.csv"
     */
    $nationalities = new Nationality($options['f'], $db);
    $nationalities->readFile();
}
else if($options['t'] == 'team'){
    /**
     * Προσοχή πρέπει τα ορίσματα να δωθούν ακριβώς όπως αναγράφεται από κάτω!
     *   php inputFromFile.php -tteam -f"data/Αγώνες.csv" -g"data/Ονόματα_Ομάδων.csv"
     */
    $teams = new Team($options['f'], $options['g'], $db);
    $teams->readFiles();
}
else if($options['t'] == 'player'){
    /**
     * php inputFromFile.php -t player -f "data/Παίχτες.csv"
     */
    $players = new Player($options['f'], $db);
    $players->readFile();
}
else if($options['t'] == 'playsfor'){
    /** 
     * php inputFromFile.php -t playsfor -f "data/Παίχτες.csv"
     */ 
    $playsFor = new PlaysFor($options['f'], $db);
    $playsFor->readFile();
}
else if($options['t'] == 'match'){
    /** 
     * php inputFromFile.php -t match -f "data/Αγώνες.csv"
     */ 
    $match = new Match($options['f'], $db);
    $match->readFile();
}
else if($options['t'] == 'all'){
    /**
     * Προσοχή πρέπει τα ορίσματα να δωθούν ακριβώς όπως αναγράφεται από κάτω!
     * php inputFromFile.php -t all -f"data/Παίχτες.csv" -g"data/Αγώνες.csv" -h"data/Ονόματα_Ομάδων.csv" -i"data/Θέσεις_Παιχτών.csv"
     */

    $positions = new Position($options['i'], $db);
    $positions->readFile();

    $league = new League($options['g'], $db);
    $league->readFile();

    $cities = new City($options['g'], $db);
    $cities->readFile();

    $stadiums = new Stadium($options['g'], $db);
    $stadiums->readFile();

    $nationalities = new Nationality($options['f'], $db);
    $nationalities->readFile();

    $teams = new Team($options['g'], $options['h'], $db);
    $teams->readFiles();

    $players = new Player($options['f'], $db);
    $players->readFile();

    $playsFor = new PlaysFor($options['f'], $db);
    $playsFor->readFile();

    $match = new Match($options['g'], $db);
    $match->readFile();
}
