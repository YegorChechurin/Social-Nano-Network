<?php

    namespace Models;
    use Skeleton\Database\Database;

    /**
     * A class which holds all the messaging and chatting functionality of the 
     * Social Nano Network.
     */
    class Messenger {

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

    	/**
    	 * Sends a message
    	 *
    	 * Sends a message from one user to another. Information about sender and
    	 * recipient and message text are contained in $mes_info. If the message
    	 * sent is the first message exchanged between the users, a new chat is 
    	 * created. Alternatively an existing chat is updated.
    	 *
    	 * @param mixed[] $mes_info - Associative array containing message text   
    	 * and data about message sender and recipient.
    	 */
    	public function send_message($mes_info) {
    	   // Stopping empty message from being sent 
		   if (!$mes_info['message']) {
			   return ;
		   }
		   $table = 'messages';
		   $fields = [
		   	  'sender_id','sender_name','recipient_id','recipient_name','message'
		   ];
		   $values = [
		   	   $mes_info['sender_id'],$mes_info['sender_name'],
		   	   $mes_info['recipient_id'],$mes_info['recipient_name'],
		   	   $mes_info['message']
		   ];
		   $this->DB->insert($table,$fields,$values);
		   $table = 'chats';
		   $fields = ['chat_id'];
		   $clause = '(participant1_id=:id_1 AND participant2_id=:id_2) OR (participant1_id=:id_2 AND participant2_id=:id_1)';
		   $map = [
		   	   ':id_1'=>$mes_info['sender_id'],
		       ':id_2'=>$mes_info['recipient_id']
		   ];
		   $result = $this->DB->select($table,$fields,$clause,$map);
		   if (!$result) {
		   	  $this->create_chat($mes_info);
		   } else {
		   	  $this->update_chat($mes_info);
		   }
    	}

    	/**
    	 * Creates a new chat
    	 *
    	 * Creates a new record in chats table in snn database according to 
    	 * information stored in $mes_info.
    	 *
    	 * @param mixed[] $mes_info - Associative array containing message text   
    	 * and data about message sender and recipient.
    	 */
    	public function create_chat($mes_info) {
    		$table = 'chats';
    		$fields = [
    			'participant1_id','participant1_name','participant2_id',
    			'participant2_name','last_mes_auth_id','last_mes_auth_name',
    			'last_mes_text','chat_key'
    		];
    		$sender_id = $mes_info['sender_id'];
    	    $sender_name = $mes_info['sender_name'];
    	    $recipient_id = $mes_info['recipient_id'];
    	    $recipient_name = $mes_info['recipient_name'];
            $message = $mes_info['message'];
            $key = $sender_id + $recipient_id;
            $values = [
            	$sender_id,$sender_name,$recipient_id,$recipient_name,
            	$sender_id,$sender_name,$message,$key
            ];
            $this->DB->insert($table,$fields,$values);
    	}

    	/**
    	 * Updates a chat
    	 *
    	 * Updates an existing record in chats table in snn database according to 
    	 * information stored in $mes_info.
    	 *
    	 * @param mixed[] $mes_info - Associative array containing message text   
    	 * and data about message sender and recipient.
    	 */
    	public function update_chat($mes_info) {
    		$table = 'chats';
    		$fields = ['last_mes_auth_id','last_mes_auth_name','last_mes_text'];
    		$clause = '(participant1_id=:sender_id AND participant2_id=:recipient_id) OR (participant1_id=:recipient_id AND participant2_id=:sender_id)';
    		$map = [
    			':last_mes_auth_id' => $mes_info['sender_id'],
    			':last_mes_auth_name' => $mes_info['sender_name'],
    			':last_mes_text' => $mes_info['message'],
    			':sender_id' => $mes_info['sender_id'],
    			':recipient_id' => $mes_info['recipient_id']
    		];
    		$this->DB->update($table,$fields,$clause,$map);
    	}

    	/**
    	 * Fetches all messages belonging to a specific chat
    	 *
    	 * @param integer $id_1 - User id of first chat participant.
    	 * @param integer $id_2 - User id of second chat participant.
    	 *
    	 * @return string - JSON encoded array of messages which 
    	 * have been fetched.
    	 */
    	public function fetch_chat_messages($id_1,$id_2) {
    		$table = 'messages';
    		$fields = ['*'];
    		$clause = '(sender_id=:id_1 AND recipient_id=:id_2) OR (sender_id=:id_2 AND recipient_id=:id_1)';
    		$map = [':id_1'=>$id_1, ':id_2'=>$id_2];
    		$messages = $this->DB->select($table,$fields,$clause,$map);
    		return json_encode($messages);
    	}

    	/**
    	 * Fetches all chats where a specific user participates at the moment
    	 *
    	 * @param integer $user_id - User id of the user whose chats are being
    	 * fetched.
    	 *
    	 * @return string|integer - JSON encoded array of chats which 
    	 * have been fetched, or zero if no chats have been fetched.
    	 */
    	public function fetch_user_chats($user_id) {
    		$table = 'chats';
    		$fields = ['*'];
    		$clause = 'participant1_id=:id OR participant2_id=:id ORDER BY	last_mes_ts DESC';
    		$map = [':id'=>$user_id];
    		$chats_raw = $this->DB->select($table,$fields,$clause,$map);
    		if ($chats_raw) {
	   	   	  foreach ($chats_raw as $key => $value) {
	   	   	  	$chat['partner_id'] = $value['chat_key'] - $user_id;
	   	   	  	if ($value['participant1_id']==$user_id) {
	   	   	  		$chat['partner_name'] = $value['participant2_name'];
	   	   	  	} else {
	   	   	  		$chat['partner_name'] = $value['participant1_name'];
	   	   	  	}
	   	   	  	$chat['last_mes_auth_id'] = $value['last_mes_auth_id'];
	   	   	  	$chat['last_mes_auth_name'] = $value['last_mes_auth_name'];
	   	   	  	$chat['last_mes_text'] = $value['last_mes_text'];
	   	   	  	$chat['last_mes_ts'] = $value['last_mes_ts'];
	   	   	  	$chats[] = $chat;
	   	   	  }
	   	   	  return json_encode($chats);
	   	   } else {
	   	   	  return 0;
	   	   }
    	}

    	/**
    	 * Fetches messages with a message_id greater than $message_id and
    	 * whose recipient is user whose user id is $user_id
    	 *
    	 * @param integer $user_id - User id of the user who is the recipient of
    	 * the messages being fetched.
    	 * @param integer $message_id - Value of a message id greater which 
    	 * messages are fetched.
    	 *
    	 * @return string - JSON encoded array of messages which 
    	 * have been fetched.
    	 */
    	public function fetch_received_messages($user_id,$message_id) {
    		$table = 'messages';
    		$fields = ['*'];
    		$clause = 'recipient_id=:user_id AND message_id>:message_id ORDER BY message_id';
    		$map = [':user_id'=>$user_id,':message_id'=>$message_id];
    		$messages = $this->DB->select($table,$fields,$clause,$map);
    		return json_encode($messages);
    	}

    	/**
    	 * Fetches message id of the latest received message by a specific user
    	 * 
    	 * @param integer $user_id - User id of the user for whom message id of 
    	 * the latest received message is being fetched.
    	 *
    	 * @return string $last_mes_id - Message id of the latest received message
    	 * by user whose user id is $user_id.
    	 */
    	public function fetch_id_of_last_received_message($user_id) {
    		$table = 'messages';
    		$fields = ['MAX(message_id)'];
    		$clause = 'recipient_id = :id';
    		$map = [':id'=>$user_id];
    		$result = $this->DB->select($table,$fields,$clause,$map);
    		$last_mes_id = $result[0]['MAX(message_id)'];
    		return $last_mes_id;
    	}

    }

?>