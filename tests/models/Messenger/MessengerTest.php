<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;
use PHPUnit\DbUnit\DataSet\QueryDataSet;
use PHPUnit\DbUnit\DataSet\ReplacementDataSet;
use Skeleton\Database\Database;
use Models\Messenger;
use const Skeleton\Database\DSN;
use const Skeleton\Database\USER;
use const Skeleton\Database\PASSWORD;
use const Skeleton\Database\SCHEMA;

class MessengerTest extends TestCase {

    use TestCaseTrait;

    private $conn;

    private $M;

    public function getConnection() {  
        $db = new Database();
        $this->M = new Messenger($db); 
        $pdo = new PDO(DSN,USER,PASSWORD);
        $this->conn = $this->createDefaultDBConnection($pdo, SCHEMA);
        return $this->conn;
    }

    public function getDataSet() {
        $fixture_dataset = $this->createFlatXMLDataSet(dirname(__FILE__).'/MessengerFixture.xml');
        return $fixture_dataset;
    }

    public function testCreateChat() {
        $fixture_time = date("Y-m-d H:i:s");
    	$mes_info['sender_id'] = 4;
    	$mes_info['sender_name'] = 'Test1';
    	$mes_info['recipient_id'] = 5;
    	$mes_info['recipient_name'] = 'Test2';
    	$mes_info['message'] = 'Hello!';
    	$this->M->create_chat($mes_info);
        $time = date("Y-m-d H:i:s");
        $tables = ['chats'];
        $actual_dataset = $this->conn->createDataset($tables);
        $flat_dataset = $this->createFlatXMLDataSet(dirname(__FILE__).'/expectedCreateChat.xml');
        $expected_dataset = new ReplacementDataSet($flat_dataset);
        $expected_dataset->addFullReplacement("##fixture_time##",$fixture_time);
        $expected_dataset->addFullReplacement("##time##",$time);
        $this->assertDataSetsEqual($expected_dataset,$actual_dataset);
    }

    public function testUpdateChat() {
        $fixture_time = date("Y-m-d H:i:s");
        $mes_info['sender_id'] = 4;
        $mes_info['sender_name'] = 'Test1';
        $mes_info['recipient_id'] = 1;
        $mes_info['recipient_name'] = 'User1';
        $mes_info['message'] = "Let's do it";
        $this->M->update_chat($mes_info);
        $time = date("Y-m-d H:i:s");
        $tables = ['chats'];
        $actual_dataset = $this->conn->createDataset($tables);
        $flat_dataset = $this->createFlatXMLDataSet(dirname(__FILE__).'/expectedUpdateChat.xml');
        $expected_dataset = new ReplacementDataSet($flat_dataset);
        $expected_dataset->addFullReplacement("##fixture_time##",$fixture_time);
        $expected_dataset->addFullReplacement("##time##",$time);
        $this->assertDataSetsEqual($expected_dataset,$actual_dataset);
    }

    public function testSendMessage() {
        $fixture_time = date("Y-m-d H:i:s");
        // Sending a message which will create a new chat between users Test3 and Test4
        $mes_info['sender_id'] = 6;
        $mes_info['sender_name'] = 'Test3';
        $mes_info['recipient_id'] = 7;
        $mes_info['recipient_name'] = 'Test4';
        $mes_info['message'] = 'Hello!';
        $this->M->send_message($mes_info);
        $time_1 = date("Y-m-d H:i:s");
        $tables = ['messages','chats'];
        $actual_dataset = $this->conn->createDataset($tables);
        $flat_dataset = $this->createFlatXMLDataSet(dirname(__FILE__).'/expectedSendMessage_1.xml');
        $expected_dataset = new ReplacementDataSet($flat_dataset);
        $expected_dataset->addFullReplacement("##fixture_time##",$fixture_time);
        $expected_dataset->addFullReplacement("##time##",$time_1);
        $this->assertDataSetsEqual($expected_dataset,$actual_dataset);

        // Sending another message in a newly created chat between users Test3 and Test4
        $mes_info['sender_id'] = 7;
        $mes_info['sender_name'] = 'Test4';
        $mes_info['recipient_id'] = 6;
        $mes_info['recipient_name'] = 'Test3';
        $mes_info['message'] = 'Hi';
        $this->M->send_message($mes_info);
        $time_2 = date("Y-m-d H:i:s");
        $tables = ['messages','chats'];
        $actual_dataset = $this->conn->createDataset($tables);
        $flat_dataset = $this->createFlatXMLDataSet(dirname(__FILE__).'/expectedSendMessage_2.xml');
        $expected_dataset = new ReplacementDataSet($flat_dataset);
        $expected_dataset->addFullReplacement("##fixture_time##",$fixture_time);
        $expected_dataset->addFullReplacement("##time_1##",$time_1);
        $expected_dataset->addFullReplacement("##time##",$time_2);
        $this->assertDataSetsEqual($expected_dataset,$actual_dataset);
    }

    public function testFetchChatMessages() {
        $fixture_time = date("Y-m-d H:i:s");
        $participant1_id = 1;
        $participant2_id = 2;
        $outcome = $this->M->fetch_chat_messages($participant1_id,$participant2_id);
        $expected_raw = [
            [
                'message_id'=>'1','recipient_id'=>'2', 'recipient_name'=>'User2', 
                'sender_id'=>'1', 'sender_name'=>'User1', 'message'=>'Moi:)', 
                'ts'=>$fixture_time
            ],
            [
                'message_id'=>'2','recipient_id'=>'1', 'recipient_name'=>'User1', 
                'sender_id'=>'2', 'sender_name'=>'User2', 'message'=>'Terveee:)', 
                'ts'=>$fixture_time
            ],
            [
                'message_id'=>'3','recipient_id'=>'2', 'recipient_name'=>'User2', 
                'sender_id'=>'1', 'sender_name'=>'User1', 
                'message'=>'Miten menee???', 'ts'=>$fixture_time
            ]
        ];
        $expected = json_encode($expected_raw);
        $this->assertEquals($expected,$outcome);
    }

    public function testFetchUserChats() {
        $fixture_time = date("Y-m-d H:i:s");
        $user_id = 1;
        $outcome = $this->M->fetch_user_chats($user_id);
        $expected_raw = [
            [
                'partner_id'=>2, 'partner_name'=>"User2", 
                'last_mes_auth_id'=>"1", 'last_mes_auth_name'=>"User1", 
                'last_mes_text'=>"Miten menee???",
                'last_mes_ts'=>$fixture_time, 'blocked'=>'no'
            ],
            [
                'partner_id'=>4, 'partner_name'=>"Test1", 
                'last_mes_auth_id'=>"1", 'last_mes_auth_name'=>"User1", 
                'last_mes_text'=>"Let's test something",
                'last_mes_ts'=>$fixture_time, 'blocked'=>'no'
            ]
        ];
        $expected = json_encode($expected_raw);
        $this->assertEquals($expected,$outcome);
        $user_id = 5;
        $outcome = $this->M->fetch_user_chats($user_id);
        $expected = 0;
        $this->assertEquals($expected,$outcome);
    }

    public function testFetchReceivedMessages() {
        $fixture_time = date("Y-m-d H:i:s");
        $user_id = 2;
        $message_id = 1;
        $outcome = $this->M->fetch_received_messages($user_id,$message_id);
        $expected_raw = [
            [
                'message_id'=>"3", 'recipient_id'=>"2", 
                'recipient_name'=>"User2", 'sender_id'=>"1", 
                'sender_name'=>"User1", 'message'=>"Miten menee???", 
                'ts'=>$fixture_time
            ]
        ];
        $expected = json_encode($expected_raw);
        $this->assertEquals($expected,$outcome);
    }

    public function testFetchIDofLastReceivedMessage() {
        $user_id = 2;
        $outcome = $this->M->fetch_id_of_last_received_message($user_id);
        $expected = 3;
        $this->assertTrue($expected==$outcome);
        $user_id = 3;
        $outcome = $this->M->fetch_id_of_last_received_message($user_id);
        $expected = 0;
        $this->assertTrue($expected==$outcome);
    }

    public function testProcessEvent() {
        $fixture_time = date("Y-m-d H:i:s");
        $tables = ['chats'];
        $user_id_1 = 1;
        $user_id_2 = 2;
        $data = [$user_id_1,$user_id_2];

        // Mocking 'friendship_deleted' event
        $event1 = 'friendship_deleted';
        $this->M->process_event($event1,$data);
        $actual_dataset = $this->conn->createDataset($tables);
        $flat_dataset = $this->createFlatXMLDataSet(dirname(__FILE__).'/expectedProcessEvent1.xml');
        $expected_dataset = new ReplacementDataSet($flat_dataset);
        $expected_dataset->addFullReplacement("##fixture_time##",$fixture_time);
        $this->assertDataSetsEqual($expected_dataset,$actual_dataset);
        
        // Mocking 'friendship_made' event
        $event2 = 'friendship_made';
        $this->M->process_event($event2,$data);
        $actual_dataset = $this->conn->createDataset($tables);
        $flat_dataset = $this->createFlatXMLDataSet(dirname(__FILE__).'/expectedProcessEvent2.xml');
        $expected_dataset = new ReplacementDataSet($flat_dataset);
        $expected_dataset->addFullReplacement("##fixture_time##",$fixture_time);
        $this->assertDataSetsEqual($expected_dataset,$actual_dataset);
    }

}