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

    private $fixture_time;

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
        // $conn = $this->getConnection();
        // $ds = new QueryDataSet($conn);
        // $ds->addTable('messages');
        // $ds->addTable('chats');
        // return $ds;
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
                'last_mes_ts'=>$fixture_time
            ],
            [
                'partner_id'=>4, 'partner_name'=>"Test1", 
                'last_mes_auth_id'=>"1", 'last_mes_auth_name'=>"User1", 
                'last_mes_text'=>"Let's test something",
                'last_mes_ts'=>$fixture_time
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
        $fixture_time = date("Y-m-d H:i:s");
        $user_id = 2;
        $outcome = $this->M->fetch_id_of_last_received_message($user_id);
        $expected = 3;
        $this->assertTrue($expected==$outcome);
    }

    // public function testCreateChat() {
    // 	$table = 'chats';
    // 	$conn = $this->getConnection();
    // 	$amount_before = $conn->getRowCount($table);
    // 	$db = new Database();
    // 	$M = new Messenger($db);
    // 	$mes_info['sender_id'] = 4;
    // 	$mes_info['sender_name'] = 'Test1';
    // 	$mes_info['recipient_id'] = 5;
    // 	$mes_info['recipient_name'] = 'Test2';
    // 	$mes_info['message'] = 'Hello!';
    // 	$M->create_chat($mes_info);
    // 	$amount_after = $conn->getRowCount($table);
    // 	$this->assertTrue($amount_after-$amount_before==1);
    // 	$expected_row = [
    // 		'chat_id'=>$amount_after,'chat_key'=>9, 'participant1_id'=>4, 
    // 		'participant2_id'=>5, 'participant1_name'=>'Test1', 
    // 		'participant2_name'=>'Test2', 'last_mes_auth_id'=>4, 
    // 		'last_mes_auth_name'=>'Test1', 'last_mes_text'=>'Hello!', 
    // 		'last_mes_ts'=>date("Y-m-d H:i:s",time()+7200)
    // 	];
    // 	$fields = ['*'];
    // 	$clause = 'chat_id=:id';
    // 	$map = [':id'=>$amount_after];
    // 	$result = $db->select($table,$fields,$clause,$map);
    // 	$row = $result[0];
    // 	$this->assertTrue($row==$expected_row);
    // }

    // public function testUpdateChat() {
    // 	$table = 'chats';
    // 	$conn = $this->getConnection();
    // 	$amount_of_rows = $conn->getRowCount($table);
    // 	$db = new Database();
    // 	$M = new Messenger($db);
    // 	$mes_info['sender_id'] = 5;
    // 	$mes_info['sender_name'] = 'Test2';
    // 	$mes_info['recipient_id'] = 4;
    // 	$mes_info['recipient_name'] = 'Test1';
    // 	$mes_info['message'] = 'Hi';
    // 	$M->update_chat($mes_info);
    // 	$expected_row = [
    // 		'chat_id'=>$amount_of_rows,'chat_key'=>9, 'participant1_id'=>4, 
    // 		'participant2_id'=>5, 'participant1_name'=>'Test1', 
    // 		'participant2_name'=>'Test2', 'last_mes_auth_id'=>5, 
    // 		'last_mes_auth_name'=>'Test2', 'last_mes_text'=>'Hi', 
    // 		'last_mes_ts'=>date("Y-m-d H:i:s",time()+7200)
    // 	];
    // 	$fields = ['*'];
    // 	$clause = 'chat_id=:id';
    // 	$map = [':id'=>$amount_of_rows];
    // 	$result = $db->select($table,$fields,$clause,$map);
    // 	$row = $result[0];
    // 	$this->assertTrue($row==$expected_row);
    // 	// Now we need to delete the chat between users Test1 and Test2. This is required for setting correct fixtures when messenger testsuite is run next time
    // 	$clause = 'chat_id=:id';
    // 	$map = [':id'=>$amount_of_rows];
    // 	$db->delete($table,$clause,$map);
    // }

   /*public function testSendMessage() {
    	// Sending a message which will create a new chat between users Test3 and Test4 and checking whether it was sent
    	$table = 'messages';
    	$conn = $this->getConnection();
    	$amount_before = $conn->getRowCount($table);
    	$db = new Database();
    	$M = new Messenger($db);
    	$mes_info['sender_id'] = 6;
    	$mes_info['sender_name'] = 'Test3';
    	$mes_info['recipient_id'] = 7;
    	$mes_info['recipient_name'] = 'Test4';
    	$mes_info['message'] = 'Hello!';
    	$M->send_message($mes_info);
    	$expected_row = [
    		'message_id'=>$amount_before+1, 'recipient_id'=>7, 
    		'recipient_name'=>'Test4', 'sender_id'=>6, 'sender_name'=>'Test3',
    		'message'=>'Hello!', 'ts'=>date("Y-m-d H:i:s",time()+7200)
    	];
    	$fields = ['*'];
    	$clause = 'message_id=:id';
    	$map = [':id'=>$amount_before+1];
    	$result = $db->select($table,$fields,$clause,$map);
    	$row = $result[0];
    	$this->assertTrue($row==$expected_row);

    	// Checking whether a new chat between users Test3 and Test4 was created
    	$table = 'chats';
    	$amount_of_rows = $conn->getRowCount($table);
    	$expected_row = [
    		'chat_id'=>$amount_of_rows,'chat_key'=>13, 'participant1_id'=>6, 
    		'participant2_id'=>7, 'participant1_name'=>'Test3', 
    		'participant2_name'=>'Test4', 'last_mes_auth_id'=>6, 
    		'last_mes_auth_name'=>'Test3', 'last_mes_text'=>'Hello!', 
    		'last_mes_ts'=>date("Y-m-d H:i:s",time()+7200)
    	];
    	$fields = ['*'];
    	$clause = 'chat_id=:id';
    	$map = [':id'=>$amount_of_rows];
    	$result = $db->select($table,$fields,$clause,$map);
    	$row = $result[0];
    	$this->assertTrue($row==$expected_row);
        
        // Sending a message in a newly created chat between users Test3 and Test4 and checking whether it was sent. This message will update the chat between users Test3 and Test4
    	$table = 'messages';
    	$amount_before = $conn->getRowCount($table);
    	$mes_info['sender_id'] = 7;
    	$mes_info['sender_name'] = 'Test4';
    	$mes_info['recipient_id'] = 6;
    	$mes_info['recipient_name'] = 'Test3';
    	$mes_info['message'] = 'Hi';
    	$M->send_message($mes_info);
    	$expected_row = [
    		'message_id'=>$amount_before+1, 'recipient_id'=>6, 
    		'recipient_name'=>'Test3', 'sender_id'=>7, 'sender_name'=>'Test4',
    		'message'=>'Hi', 'ts'=>date("Y-m-d H:i:s",time()+7200)
    	];
    	$fields = ['*'];
    	$clause = 'message_id=:id';
    	$map = [':id'=>$amount_before+1];
    	$result = $db->select($table,$fields,$clause,$map);
    	$row = $result[0];
    	$this->assertTrue($row==$expected_row);

    	// Checking whether the chat between users Test3 and Test4 was updated
    	$table = 'chats';
    	$amount_of_rows = $conn->getRowCount($table);
    	$expected_row = [
    		'chat_id'=>$amount_of_rows,'chat_key'=>13, 'participant1_id'=>6, 
    		'participant2_id'=>7, 'participant1_name'=>'Test3', 
    		'participant2_name'=>'Test4', 'last_mes_auth_id'=>7, 
    		'last_mes_auth_name'=>'Test4', 'last_mes_text'=>'Hi', 
    		'last_mes_ts'=>date("Y-m-d H:i:s",time()+7200)
    	];
    	$fields = ['*'];
    	$clause = 'chat_id=:id';
    	$map = [':id'=>$amount_of_rows];
    	$result = $db->select($table,$fields,$clause,$map);
    	$row = $result[0];
    	$this->assertTrue($row==$expected_row);

    	// Now we need to delete the chat between users Test1 and Test2. This is required for setting correct fixtures when messenger testsuite is run next time
    	$clause = 'chat_id=:id';
    	$map = [':id'=>$amount_of_rows];
    	$db->delete($table,$clause,$map);
    }*/ 

}

?>