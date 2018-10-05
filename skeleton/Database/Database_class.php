<?php
    
    namespace Skeleton\Database;

    require_once('database_config.php');

    class Database {
        
    	private $conn;

    	public function __construct() {
    	    try{
    	    	$dsn = DATABASE.':dbname='.SCHEMA.';host='.HOST;
		        //$dsn = "mysql:dbname=snn;host=localhost";
		        $user = USER;
		        $password = PASSWORD;
	            $this->conn = new PDO($dsn,$user,$password);
		    }
	        catch (PDOException $e){
		        echo 'Connection failed: ' . $e->getMessage();
	        }
    	}
    	
    }

?>