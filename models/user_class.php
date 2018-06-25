<?php

	namespace Models;

    class User {
	   
	   protected $conn;
	   protected $id;	   
	   protected $name;
	   protected $fr_IDs;
	   protected $fr_info;
       protected $last_friendship_id;
       protected $chats;
	   
	   public function __construct($us_id,$conn) {
		   $this->id = $us_id;
		   $this->conn = $conn;
           $this->name = $this->fetchName();
 	   }
	   
	   public function showID() {
		   return $this->id;
	   }

	   public function fetchName() {
	   	   $query = "SELECT username FROM users WHERE user_id=$this->id";
		   $action = $this->conn->query($query);
		   $result = $action->fetch(\PDO::FETCH_ASSOC);
		   $this->name = $result['username'];
		   return $this->name;
	   }
	   
	   public function showName() {
		   return $this->name;
	   }

	   public function get_fr_IDs() {
           $stat = "SELECT fr2_id FROM friends WHERE fr1_id=$this->id";
           $impl = $this->conn->query($stat);
           $res = $impl->fetchAll(\PDO::FETCH_ASSOC);
           if ($res) {
           	 foreach ($res as $key => $value) {
           	 	$fr_IDs[] = $value['fr2_id'];
           	 }
           }
           $stat = "SELECT fr1_id FROM friends WHERE fr2_id=$this->id";
           $impl = $this->conn->query($stat);
           $res = $impl->fetchAll(\PDO::FETCH_ASSOC);
           if ($res) {
           	 foreach ($res as $key => $value) {
           	 	$fr_IDs[] = $value['fr1_id'];
           	 }
           }
           if (!empty($fr_IDs)) {
           	 return $fr_IDs;
           } else {
           	 return array();
           }
	   }

	   public function get_fr_info() {
           $fr_IDs = $this->get_fr_IDs();
           if (!empty($fr_IDs)) {
           	 foreach ($fr_IDs as $key => $value) {
           		$stat = "SELECT username FROM users WHERE user_id=$value";
           		$impl = $this->conn->query($stat);
                $res = $impl->fetch(\PDO::FETCH_ASSOC);
                $fr_info[] = array('id'=>$value,'name'=>$res['username']);
           	 }
           	return $fr_info;
           } else {
           	return array();
           }
	   }

	   public function get_last_friendship_id() {
		   $query = "SELECT MAX(friendship_id) FROM friends WHERE fr1_id = $this->id OR fr2_id = $this->id";
		   $result = $this->conn->query($query);
		   $maximum = $result->fetch(\PDO::FETCH_ASSOC);
		   $last_friendship_id = $maximum['MAX(friendship_id)'];
		   if ($last_friendship_id) {
		   	  return $last_friendship_id;
		   } else {
		   	  return 0;
		   }
		   
	   }

	   public function fetch_chats() {
	   	   $query = "SELECT * FROM chats WHERE participant1_id=$this->id OR participant2_id=$this->id ORDER BY	last_mes_ts DESC";
	   	   $result = $this->conn->query($query);
	   	   $chats_raw = $result->fetchAll(\PDO::FETCH_ASSOC);
	   	   if ($chats_raw) {
	   	   	  foreach ($chats_raw as $key => $value) {
	   	   	  	$chat['partner_id'] = $value['chat_key'] - $this->id;
	   	   	  	if ($value['participant1_id']==$this->id) {
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

	   public function getLast() {
		   $query = "SELECT MAX(message_id) FROM messages WHERE recipient_id = $this->id";
		   $result = $this->conn->query($query);
		   $maximum = $result->fetch(\PDO::FETCH_ASSOC);
		   $mes_id = $maximum['MAX(message_id)'];
		   if ($maximum) {
		   	  $mes_id = $maximum['MAX(message_id)'];
		      return $mes_id;
		   } else {
		   	  return 0;
		   }
	   }

	   public function fetch_received_messages($message_id) {
	       $test = $message_id;
	       $query = "SELECT * FROM messages WHERE recipient_id = $this->id AND message_id > $test ORDER BY message_id";
	       $result = $this->conn->query($query);
		   $messages = $result->fetchAll(\PDO::FETCH_ASSOC);	
		   return $messages;
	   }
	   
    }
?>