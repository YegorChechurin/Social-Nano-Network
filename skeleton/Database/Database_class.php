<?php
    
    namespace Skeleton\Database;

    class Database {
        
    	private $conn;

    	public function __construct() {
    	    try{
		        $dsn = "mysql:dbname=snn;host=localhost";
		        $user="root";
		        $password="";
	            $this->conn = new PDO($dsn,$user,$password);
		    }
	        catch (PDOException $e){
		        echo 'Connection failed: ' . $e->getMessage();
	        }
    	}
    }

?>