<?php
    
    namespace Skeleton\Database;

    echo $GLOBALS['USER'];

    require_once('database_config.php');

    class Database {
        
    	public $conn;

    	public function __construct() {
    	    $dsn = DATABASE.':dbname='.SCHEMA.';host='.HOST;
		    $user = USER;
		    $password = PASSWORD;
	        $this->conn = new \PDO($dsn,$user,$password);
    	}

    }

?>