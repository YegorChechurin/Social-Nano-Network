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

    public function getConnection() {   
        $pdo = new PDO(DSN,USER,PASSWORD);
        $conn = $this->createDefaultDBConnection($pdo, SCHEMA);
        return $conn;
    }

    public function getDataSet() {
        //return $this->createFlatXMLDataSet(dirname(__FILE__).'/MessengerFixture.xml');
        $conn = $this->getConnection();
        $ds = new QueryDataSet($conn);
        $ds->addTable('messages');
        $ds->addTable('chats');
        return $ds;
    }

    public function testCreateChat() {
    	$table = 'chats';
    	$conn = $this->getConnection();
    	$amount_before = $conn->getRowCount($table);
    	$db = new Database();
    	$M = new Messenger($db);
    	$mes_info['sender_id'] = 4;
    	$mes_info['sender_name'] = 'Test1';
    	$mes_info['recipient_id'] = 5;
    	$mes_info['recipient_name'] = 'Test2';
    	$mes_info['message'] = 'Hello!';
    	$M->create_chat($mes_info);
    	$amount_after = $conn->getRowCount($table);
    	$this->assertTrue($amount_after-$amount_before==1);
    	$expected_row = [
    		'chat_id'=>$amount_after,'chat_key'=>9, 'participant1_id'=>4, 
    		'participant2_id'=>5, 'participant1_name'=>'Test1', 
    		'participant2_name'=>'Test2', 'last_mes_auth_id'=>4, 
    		'last_mes_auth_name'=>'Test1', 'last_mes_text'=>'Hello!', 
    		'last_mes_ts'=>date("Y-m-d H:i:s",time()+7200)
    	];
    	$fields = ['*'];
    	$clause = 'chat_id=:id';
    	$map = [':id'=>$amount_after];
    	$result = $db->select($table,$fields,$clause,$map);
    	$row = $result[0];
    	$this->assertTrue($row==$expected_row);
    }

    public function testUpdateChat() {
    	$table = 'chats';
    	$conn = $this->getConnection();
    	$amount_of_rows = $conn->getRowCount($table);
    	$db = new Database();
    	$M = new Messenger($db);
    	$mes_info['sender_id'] = 5;
    	$mes_info['sender_name'] = 'Test2';
    	$mes_info['recipient_id'] = 4;
    	$mes_info['recipient_name'] = 'Test1';
    	$mes_info['message'] = 'Hi';
    	$M->update_chat($mes_info);
    	$expected_row = [
    		'chat_id'=>$amount_of_rows,'chat_key'=>9, 'participant1_id'=>4, 
    		'participant2_id'=>5, 'participant1_name'=>'Test1', 
    		'participant2_name'=>'Test2', 'last_mes_auth_id'=>5, 
    		'last_mes_auth_name'=>'Test2', 'last_mes_text'=>'Hi', 
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
    }

    public function testSendMessage() {
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
    	// $tables = ['messages','chats'];
    	// $dataset = $this->getConnection()->createDataset($tables);
    	// $flat_dataset = $this->createFlatXMLDataSet(dirname(__FILE__).'/expectedSendMessage.xml');
    	// $expected_dataset = new ReplacementDataSet($flat_dataset);
    	// $expected_dataset->addFullReplacement("##time##",date("Y-m-d H:i:s"));
    	// $this->assertDataSetsEqual($expected_dataset,$dataset);
    }

}

?>