<?php

use PHPUnit\Framework\TestCase;
use Skeleton\Database\Database;
use Models\User;
use Models\Messenger;
use Models\FriendBroker;
use Models\UserFactory;
use Models\ServiceFactory;
use const Skeleton\Database\DSN;
use const Skeleton\Database\USER;
use const Skeleton\Database\PASSWORD;
use const Skeleton\Database\SCHEMA;

class FactoriesTest extends TestCase {

	public function testUserFactory() {
		$db = new Database();
		$factory = new UserFactory($db);
		$user_id = 1;
		$user = $factory->make_user($user_id);
		$this->assertInstanceOf('Models\User',$user);
	}

    public function testServiceFactory() {
    	$db = new Database();
    	$factory = new ServiceFactory($db);
    	$service = 'Messenger';
    	$messenger = $factory->make_service_instance($service);
    	$this->assertInstanceOf('Models\Messenger',$messenger);
    	$service = 'FriendBroker';
    	$messenger = $factory->make_service_instance($service);
    	$this->assertInstanceOf('Models\FriendBroker',$messenger);
    }

}