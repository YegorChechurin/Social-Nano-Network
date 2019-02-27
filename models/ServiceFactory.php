<?php

    namespace Models;
    use Skeleton\Database\Database;

    class ServiceFactory {

        private $database;

        public function __construct(Database $database) {
        	$this->database = $database;
        }

        public function make_service_instance($service_name) {
        	if ($service_name=='Messenger') {
        		return new Messenger($this->database);
        	} elseif ($service_name=='FriendBroker') {
        		return new FriendBroker($this->database);
        	}
        }

    }