<?php

    namespace Models;
    use Skeleton\Database\Database;

    class Messenger {

    	private $DB;

    	public function __construct($database) {
    		$this->DB = $database;
    	}

    	public function send_message($mes_info) {
    		/* Stopping empty message from being sent */
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
    		$this->DB->update($table,$clause,$map);
    	}

    	public function fetch_chat_messages($id_1,$id_2) {
    		$table = 'messages';
    		$fields = ['*'];
    		$clause = '(sender_id=:id_1 AND recipient_id=:id_2) OR (sender_id=:id_2 AND recipient_id=:id_1)';
    		$map = [':id1'=>$id_1, ':id2'=>$id_2];
    		$messages = $this->DB->select($table,$fields,$clause,$map);
    		return json_encode($messages);
    	}

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

    	public function fetch_received_messages($user_id,$message_id) {
    		$table = 'messages';
    		$fields = ['*'];
    		$clause = 'recipient_id=:user_id AND message_id>:message_id ORDER BY message_id';
    		$map = [':user_id'=>$user_id,':message_id'=>$message_id];
    		$messages = $this->DB->select($table,$fields,$clause,$map);
    		return json_encode($messages);
    	}

    	public function fetch_id_of_last_received_message($user_id) {
    		$query = "SELECT MAX(message_id) FROM messages WHERE recipient_id = $this->id";
    		$table = 'messages';
    		$fields = ['MAX(message_id)'];
    		$clause = 'recipient_id = :id';
    		$map = [':id'=>$user_id];
    		$result = $this->DB->select($table,$fields,$clause,$map);
    		$last_mes_id = $result[0]['message_id'];
    		return $last_mes_id;
    	}

    }

?>