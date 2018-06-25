<?php 

    /* Connecting to our database */
	       try{
		        $dsn = "mysql:dbname=snn;host=localhost";
		        $user="root";
		        $password="";
	            $conn = new PDO($dsn,$user,$password);
		   }
	       catch (PDOException $e){
		   echo 'Connection failed: ' . $e->getMessage();
	       }
?>