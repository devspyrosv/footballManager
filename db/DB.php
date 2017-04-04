<?php
namespace Football\DatabaseConnector ;

class DB
{
    private $host;
    private $dbname;
    private $user;
    private $password;
    private $connection;

    /**
     * DB constructor. 
     */
    public function __construct(){
        $this->parseCredentials();
    }

    /**
     * Connect to the database.
     */
    public function connect(){        
        $this->connection = pg_connect("host=$this->host dbname=$this->dbname user=$this->user password=$this->password");
        if(!$this->connection){
            die("Could not connect to server!".PHP_EOL);
        }        
    }

    /**
     * @return mixed
     * Get the database connection.
     */
    public function getConnection(){
        return $this->connection;
    }

    /**
     * Disconnect from the database.
     */
    public function disconnect(){
        pg_close($this->connection);
    }

    /**
     * Parse the database credentials from the ini file.
     */
    private function parseCredentials(){
        $ini_array = parse_ini_file("db/credentials.ini");
        
        $this->host = $ini_array['host'];
        $this->dbname = $ini_array['database'];
        $this->user = $ini_array['username'];
        $this->password = $ini_array['password'];
    }
   
}