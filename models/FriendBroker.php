<?php

    namespace Models;
    use Skeleton\Database\Database;

    /**
     * A class which provides means for establishing and maintaining friendship  
     * connections in Social Nano Network. 
     */
    class FriendBroker {

    	/**
    	 * @var Skeleton\Database\Database - Points to an instance of Database class
    	 */
    	private $DB;

    	/**
    	 * A Database object is assigned to $this->DB variable
    	 *
    	 * @param Skeleton\Database\Database $database - Database object
    	 */
    	public function __construct($database) {
    		$this->DB = $database;
    	}

    	
    }

?>