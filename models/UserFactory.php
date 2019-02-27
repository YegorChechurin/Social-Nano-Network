<?php

    namespace Models;
    use Skeleton\Database\Database;

    class UserFactory {

        private $database;

        public function __construct(Database $database) {
        	$this->database = $database;
        }

        public function make_user($user_id) {
        	return new User($user_id,$this->database);
        }

    }