<?php

	namespace Models;
    use Skeleton\Database\Database;

    class UserTracker {

    	/**
    	 * @var Skeleton\Database\Database - Points to an instance of Database class
    	 */
    	private $DB;

        /**
         * @var string - Holds name of the database table which stores all the
         * information about users registered in Social Nano Network
         */
        private $table;

    	/**
    	 * A Database object is assigned to $this->DB variable
    	 *
    	 * @param Skeleton\Database\Database $database - Database object
    	 */
    	public function __construct(Database $database) {
    		$this->DB = $database;
            $this->table = 'users';
    	}

    	public function register_new_user() {}

    	public function log_user_in() {}

    	public function fetch_id_of_last_registered_user() {}

    	public function fetch_all_registered_users($user_id) {
    		$fields = ['user_id','username'];
            $clause = 'user_id!=:id';
            $map = [':id'=>$user_id];
            $users = $this->DB->select($this->table,$fields,$clause,$map);
            if ($users) {
            	return json_encode($users);
            } else {
            	return 0;
            } 
    	}

    }