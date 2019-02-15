<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;
use Skeleton\Database\Database;
use Models\FriendBroker;
use const Skeleton\Database\DSN;
use const Skeleton\Database\USER;
use const Skeleton\Database\PASSWORD;
use const Skeleton\Database\SCHEMA;

class FriendBrokerTest extends TestCase {

    use TestCaseTrait;

    private $conn;

    private $FB;

    private $tables;

    public function getConnection() {  
        $db = new Database();
        $this->FB = new FriendBroker($db); 
        $this->tables = ['friends'];
        $pdo = new PDO(DSN,USER,PASSWORD);
        $this->conn = $this->createDefaultDBConnection($pdo, SCHEMA);
        return $this->conn;
    }

    public function getDataSet() {
        $fixture_dataset = $this->createFlatXMLDataSet(dirname(__FILE__).'/FriendBrokerFixture.xml');
        return $fixture_dataset;
    }

    public function testMakeFriendship() {
    	$id_1 = 1;
    	$id_2 = 2;
    	$name_1 = 'User1';
    	$name_2 = 'User2';
    	$this->FB->make_friendship($id_1,$id_2,$name_1,$name_2);
        $actual_dataset = $this->conn->createDataset($this->tables);
        $expected_dataset = $this->createFlatXMLDataSet(dirname(__FILE__).'/expectedMakeFriendship_1.xml');
        $this->assertDataSetsEqual($expected_dataset,$actual_dataset);
        $id_1 = 1;
    	$id_2 = 3;
    	$name_1 = 'User1';
    	$name_2 = 'User3';
    	$this->FB->make_friendship($id_1,$id_2,$name_1,$name_2);
        $actual_dataset = $this->conn->createDataset($this->tables);
        $expected_dataset = $this->createFlatXMLDataSet(dirname(__FILE__).'/expectedMakeFriendship_2.xml');
        $this->assertDataSetsEqual($expected_dataset,$actual_dataset);
    }

    public function testDeleteFriendship() {
    	$id_1 = 1;
    	$id_2 = 5;
    	$this->FB->delete_friendship($id_1,$id_2);
        $actual_dataset = $this->conn->createDataset($this->tables);
        $expected_dataset = $this->createFlatXMLDataSet(dirname(__FILE__).'/expectedDeleteFriendship.xml');
        $this->assertDataSetsEqual($expected_dataset,$actual_dataset);
    }

    public function testFetchAllFriends() {
    	$user_id = 1;
    	$friend_info = $this->FB->fetch_all_friends($user_id);
    	$expected_result = json_encode(
            [
                ['friendship_id'=>'1','friend_id'=>'2','friend_name'=>'User2'],
                ['friendship_id'=>'2','friend_id'=>'4','friend_name'=>'Test1'],
                ['friendship_id'=>'3','friend_id'=>'5','friend_name'=>'Test2']
            ]
        );
    	$this->assertEquals($friend_info,$expected_result);
    }

    public function testFetchNewFriends() {
    	$id_1 = 1;
    	$id_2 = 3;
    	$name_1 = 'User1';
    	$name_2 = 'User3';
    	$this->FB->make_friendship($id_1,$id_2,$name_1,$name_2);
    	$id_1 = 1;
    	$id_2 = 6;
    	$name_1 = 'User1';
    	$name_2 = 'Test3';
    	$this->FB->make_friendship($id_1,$id_2,$name_1,$name_2);
    	$user_id = 1;
    	$friendship_id = 3;
    	$outcome = $this->FB->fetch_new_friends($user_id,$friendship_id);
        $expected_raw = [
            [
            	'friendship_id'=>'4','friend_id'=>'3','friend_name'=>'User3'
            ],
            [
            	'friendship_id'=>'5','friend_id'=>'6','friend_name'=>'Test3'
            ]
        ];
        $expected = json_encode($expected_raw);
        $this->assertEquals($expected,$outcome);
    }

    public function testFetchLastFriendshipID() {
    	$user_id = 1;
        $outcome = $this->FB->fetch_last_friendship_id($user_id);
        $expected = 3;
        $this->assertTrue($expected==$outcome);
    }

}