<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;
use Skeleton\Database\Database;
use Models\FriendBroker;
use Models\iObserver;
use const Skeleton\Database\DSN;
use const Skeleton\Database\USER;
use const Skeleton\Database\PASSWORD;
use const Skeleton\Database\SCHEMA;

class FriendBrokerTest extends TestCase {

    use TestCaseTrait;

    private $conn;

    private $FB;

    private $FB_mock;

    private $tables;

    public function getConnection() {  
        $db = new Database();
        $this->FB = new FriendBroker($db); 
        $this->FB_mock = $this->getMockBuilder(FriendBroker::class)
                              ->setConstructorArgs([$db])
                              ->setMethods(['fire_event'])
                              ->getMock();
        $this->tables = ['friends'];
        $pdo = new PDO(DSN,USER,PASSWORD);
        $this->conn = $this->createDefaultDBConnection($pdo, SCHEMA);
        return $this->conn;
    }

    public function getDataSet() {
        $fixture_dataset = $this->createFlatXMLDataSet(dirname(__FILE__).'/FriendBrokerFixture.xml');
        return $fixture_dataset;
    }

    public function testNoAttachedObservers() {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("No observers are attached for event called 'some_event'");
        $event = 'some_event';
        $this->FB->fetch_event_observers($event);
    }

    public function testObservable() {
        $data = 'some_data';
        $event1 = 'event1';
        $event2 = 'event2';
        $observer1 = $this->createMock(iObserver::class);
        $observer1->expects($this->exactly(2))
                         ->method('process_event')
                         ->withConsecutive(
                            [$this->equalTo($event1),$this->equalTo($data)],
                            [$this->equalTo($event2),$this->equalTo($data)]
                        );
        $observer2 = $this->getMockBuilder(iObserver::class)
                              ->setMethods(['process_event'])
                              ->getMock();
        $observer2->expects($this->once())
                         ->method('process_event')
                         ->with($this->equalTo($event1),$this->equalTo($data));
        $this->FB->attach_observer($observer1,$event1);
        $this->FB->attach_observer($observer2,$event1);
        $this->FB->attach_observer($observer1,$event2);
        $this->FB->fire_event($event1,$data);
        $this->FB->fire_event($event2,$data);
        $observer3 = $this->createMock(iObserver::class);
        $observer3->expects($this->once())
                         ->method('process_event')
                         ->with($this->equalTo($event2),$this->equalTo($data));
        $this->FB->detach_observer($observer1,$event2);
        $this->FB->attach_observer($observer3,$event2);
        $this->FB->fire_event($event2,$data);
    }

    public function testMakeFriendship() {
    	$id_1 = 1;
    	$id_2 = 2;
    	$name_1 = 'User1';
    	$name_2 = 'User2';
        $this->FB_mock->make_friendship($id_1,$id_2,$name_1,$name_2);
        $actual_dataset = $this->conn->createDataset($this->tables);
        $expected_dataset = $this->createFlatXMLDataSet(dirname(__FILE__).'/expectedMakeFriendship_1.xml');
        $this->assertDataSetsEqual($expected_dataset,$actual_dataset);
        $id_1 = 1;
    	$id_2 = 3;
    	$name_1 = 'User1';
    	$name_2 = 'User3';
    	$this->FB_mock->make_friendship($id_1,$id_2,$name_1,$name_2);
        $actual_dataset = $this->conn->createDataset($this->tables);
        $expected_dataset = $this->createFlatXMLDataSet(dirname(__FILE__).'/expectedMakeFriendship_2.xml');
        $this->assertDataSetsEqual($expected_dataset,$actual_dataset);
    }

    public function testDeleteFriendship() {
    	$id_1 = 1;
    	$id_2 = 5;
    	$this->FB_mock->delete_friendship($id_1,$id_2);
        $actual_dataset = $this->conn->createDataset($this->tables);
        $expected_dataset = $this->createFlatXMLDataSet(dirname(__FILE__).'/expectedDeleteFriendship.xml');
        $this->assertDataSetsEqual($expected_dataset,$actual_dataset);
    }

    public function testFetchFriendshipID() {
        $id_1 = 1;
        $id_2 = 5;
        $expected_friendship_id = '3';
        $friendship_id = $this->FB->fetch_friendship_id($id_1,$id_2);
        $this->assertEquals($friendship_id,$expected_friendship_id); 
        // Testing exception
        $id_1 = 100;
        $id_2 = 500;
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("No friendship id can be fetched, as no friendship between users with user IDs 100 and 500 exist");
        $friendship_id = $this->FB->fetch_friendship_id($id_1,$id_2);
    }

    public function testFetchAllFriends() {
    	$user_id = 1;
    	$friend_info = $this->FB->fetch_all_friends($user_id);
    	$expected_result =
            [
                ['friendship_id'=>'1','friend_id'=>'2','friend_name'=>'User2'],
                ['friendship_id'=>'2','friend_id'=>'4','friend_name'=>'Test1'],
                ['friendship_id'=>'3','friend_id'=>'5','friend_name'=>'Test2']
            ];
    	$this->assertEquals($friend_info,$expected_result);
    }

    public function testFetchSpecificFriends() {
        $user_id = 1;
        $friendship_IDs = ['2','3'];
        $friend_records = $this->FB->fetch_specific_friends($user_id,$friendship_IDs);
        $expected_result = [
            ['friendship_id'=>'2','friend_id'=>'4','friend_name'=>'Test1'],
            ['friendship_id'=>'3','friend_id'=>'5','friend_name'=>'Test2']
        ];
        $this->assertEquals($friend_records,$expected_result);
        $friendship_IDs = ['2'];
        $friend_records = $this->FB->fetch_specific_friends($user_id,$friendship_IDs);
        $expected_result = [
            ['friendship_id'=>'2','friend_id'=>'4','friend_name'=>'Test1']
        ];
        $this->assertEquals($friend_records,$expected_result);
        // Testing exceptions
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("No friendship_IDs provided");
        $friendship_IDs = [];
        $friend_records = $this->FB->fetch_specific_friends($user_id,$friendship_IDs);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("No friendship records can be fetched for provided friendship IDs");
        $friendship_IDs = ['4','5','6'];
        $friend_records = $this->FB->fetch_specific_friends($user_id,$friendship_IDs);
    }

    public function testFetchNewFriends() {
    	$id_1 = 1;
    	$id_2 = 3;
    	$name_1 = 'User1';
    	$name_2 = 'User3';
    	$this->FB_mock->make_friendship($id_1,$id_2,$name_1,$name_2);
    	$id_1 = 1;
    	$id_2 = 6;
    	$name_1 = 'User1';
    	$name_2 = 'Test3';
    	$this->FB_mock->make_friendship($id_1,$id_2,$name_1,$name_2);
    	$user_id = 1;
    	$friendship_id = 3;
    	$outcome = $this->FB->fetch_new_friends($user_id,$friendship_id);
        $expected = [
            [
            	'friendship_id'=>'4','friend_id'=>'3','friend_name'=>'User3'
            ],
            [
            	'friendship_id'=>'5','friend_id'=>'6','friend_name'=>'Test3'
            ]
        ];
        $this->assertEquals($expected,$outcome);
    }

    public function testFetchLastFriendshipID() {
    	$user_id = 1;
        $outcome = $this->FB->fetch_last_friendship_id($user_id);
        $expected = 3;
        $this->assertTrue($expected==$outcome);
    }

    public function testFetchFriendshipIDs() {
        $user_id = 1;
        $outcome = $this->FB->fetch_friendship_IDs($user_id);
        $expected = ['1','2','3'];
        $this->assertEquals($expected,$outcome);
    }

    public function testProcessFriendChange() {
        $user_id = 1;
        $real_friendship_IDs = $this->FB->fetch_friendship_IDs($user_id);

        // Scenario: no lost friends, one new friend obtained
        $friendship_IDs = ['1','2'];
        $outcome = $this->FB->process_friend_change($user_id,$friendship_IDs,$real_friendship_IDs);
        $expected_raw = [
                    'friends_obtained'=>'yes',
                    'friends_lost'=>'no',
                    'lost_friendship_IDs'=>0,
                    'new_friends'=>[
                        ['friendship_id'=>'3',
                        'friend_id'=>'5',
                        'friend_name'=>'Test2']
                    ],
                    'friendship_IDs'=>$real_friendship_IDs
                ];
        $expected = json_encode($expected_raw);
        $this->assertEquals($expected,$outcome);

        // Scenario: no lost friends, two new friend obtained
        $friendship_IDs = ['2'];
        $outcome = $this->FB->process_friend_change($user_id,$friendship_IDs,$real_friendship_IDs);
        $expected_raw = [
                    'friends_obtained'=>'yes',
                    'friends_lost'=>'no',
                    'lost_friendship_IDs'=>0,
                    'new_friends'=>[
                        ['friendship_id'=>'1',
                        'friend_id'=>'2',
                        'friend_name'=>'User2'],
                        ['friendship_id'=>'3',
                        'friend_id'=>'5',
                        'friend_name'=>'Test2']
                    ],
                    'friendship_IDs'=>$real_friendship_IDs
                ];
        $expected = json_encode($expected_raw);
        $this->assertEquals($expected,$outcome);

        // Scenario: friend list has been totally renewed - all friends are new, all previous friends are lost
        $friendship_IDs = ['10','15','-47'];
        $outcome = $this->FB->process_friend_change($user_id,$friendship_IDs,$real_friendship_IDs);
        $expected_raw = [
                    'friends_obtained'=>'all_new',
                    'friends_lost'=>'all_lost',
                    'lost_friendship_IDs'=>$friendship_IDs,
                    'new_friends'=>[
                        ['friendship_id'=>'1',
                        'friend_id'=>'2',
                        'friend_name'=>'User2'],
                        ['friendship_id'=>'2',
                        'friend_id'=>'4',
                        'friend_name'=>'Test1'],
                        ['friendship_id'=>'3',
                        'friend_id'=>'5',
                        'friend_name'=>'Test2']
                    ],
                    'friendship_IDs'=>$real_friendship_IDs
                ];
        $expected = json_encode($expected_raw);
        $this->assertEquals($expected,$outcome);

        // Scenario: no friends were obtained, one friend was lost
        $friendship_IDs = ['1','2','3','4'];
        $outcome = $this->FB->process_friend_change($user_id,$friendship_IDs,$real_friendship_IDs);
        $expected_raw = [
                    'friends_obtained'=>'no',
                    'friends_lost'=>'yes',
                    'lost_friendship_IDs'=>['4'],
                    'new_friends'=>0,
                    'friendship_IDs'=>$real_friendship_IDs
                ];
        $expected = json_encode($expected_raw);
        $this->assertEquals($expected,$outcome);

        // Scenario: one friend was obtained, one friend was lost
        $friendship_IDs = ['2','3','4'];
        $outcome = $this->FB->process_friend_change($user_id,$friendship_IDs,$real_friendship_IDs);
        $expected_raw = [
                    'friends_obtained'=>'yes',
                    'friends_lost'=>'yes',
                    'lost_friendship_IDs'=>['4'],
                    'new_friends'=>[
                        ['friendship_id'=>'1',
                        'friend_id'=>'2',
                        'friend_name'=>'User2']
                    ],
                    'friendship_IDs'=>$real_friendship_IDs
                ];
        $expected = json_encode($expected_raw);
        $this->assertEquals($expected,$outcome);
    }

    public function testProcessExtremeFriendChange() {
        $user_id = 1;

        // Scenario: friend list has been totally renewed - no new friends, all previous friends are lost
        $real_friendship_IDs = false;
        $friendship_IDs = ['1','2','3'];
        $outcome = $this->FB->process_extreme_friend_change($user_id,$friendship_IDs,$real_friendship_IDs);
        $expected_raw = [
                    'friends_obtained'=>'no',
                    'friends_lost'=>'all_lost',
                    'lost_friendship_IDs'=>$friendship_IDs,
                    'new_friends'=>0,
                    'friendship_IDs'=>0
                ];
        $expected = json_encode($expected_raw);
        $this->assertEquals($expected,$outcome);

        // Scenario: friend list has been totally renewed - all friends are new, because previously user had no friends at all
        $real_friendship_IDs = ['1','2','3'];
        $friendship_IDs = 0;
        $outcome = $this->FB->process_extreme_friend_change($user_id,$friendship_IDs,$real_friendship_IDs);
        $expected_raw = [
                    'friends_obtained'=>'all_new',
                    'friends_lost'=>'no',
                    'lost_friendship_IDs'=>$friendship_IDs,
                    'new_friends'=>[
                        ['friendship_id'=>'1',
                        'friend_id'=>'2',
                        'friend_name'=>'User2'],
                        ['friendship_id'=>'2',
                        'friend_id'=>'4',
                        'friend_name'=>'Test1'],
                        ['friendship_id'=>'3',
                        'friend_id'=>'5',
                        'friend_name'=>'Test2']
                    ],
                    'friendship_IDs'=>$real_friendship_IDs
                ];
        $expected = json_encode($expected_raw);
        $this->assertEquals($expected,$outcome);
    }

}