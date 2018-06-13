<?php

   class User {
	   
	   protected $id;
	   
	   protected $conn;
	   
	   public function __construct($us_id) {
		   $this->id = $us_id;
		   /* Connecting to our database */
	       try{
		        $dsn = "mysql:dbname=my_sn;host=localhost";
		        $user="root";
		        $password="";
	            $this->conn = new PDO($dsn,$user,$password);
		   }
	       catch (PDOException $e){
		   echo 'Connection failed: ' . $e->getMessage();
	       }
	   }
	   
	   public function showID() {
		   return $this->id;
	   }
	   
	   public function showName() {
		   $query = "SELECT username FROM users WHERE user_id=$this->id";
		   $action = $this->conn->query($query);
		   $result = $action->fetch(PDO::FETCH_ASSOC);
		   $name = $result['username'];
		   return $name;
	   }

	   public function get_fr_IDs() {
           $stat = "SELECT fr2_id FROM friends WHERE fr1_id=$this->id";
           $impl = $this->conn->query($stat);
           $res = $impl->fetchAll(PDO::FETCH_ASSOC);
           if ($res) {
           	 foreach ($res as $key => $value) {
           	 	$fr_IDs[] = $value['fr2_id'];
           	 }
           }
           $stat = "SELECT fr1_id FROM friends WHERE fr2_id=$this->id";
           $impl = $this->conn->query($stat);
           $res = $impl->fetchAll(PDO::FETCH_ASSOC);
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
                $res = $impl->fetch(PDO::FETCH_ASSOC);
                $fr_info[] = array('id'=>$value,'name'=>$res['username']);
           	 }
           	return $fr_info;
           } else {
           	return array();
           }
	   }
	   
	   public function get_fr_names() {
           $stat = "SELECT username FROM users WHERE users.user_id IN (SELECT fr2_id FROM friends WHERE friends.fr1_id=$this->id)";
           $impl = $this->conn->query($stat);
           $res = $impl->fetchAll(PDO::FETCH_ASSOC);
           foreach ($res as $key => $value) {
           	  $fr_names[] = $value['username'];
           }
           $stat = "SELECT username FROM users WHERE users.user_id IN (SELECT fr1_id FROM friends WHERE friends.fr2_id=$this->id)";
           $impl = $this->conn->query($stat);
           $res = $impl->fetchAll(PDO::FETCH_ASSOC);
           foreach ($res as $key => $value) {
           	  $fr_names[] = $value['username'];
           }
           return $fr_names;
	   }

	   public function get_last_friendship_id() {
		   $query = "SELECT MAX(friendship_id) FROM friends WHERE fr1_id = $this->id OR fr2_id = $this->id";
		   $result = $this->conn->query($query);
		   $maximum = $result->fetch(PDO::FETCH_ASSOC);
		   $friendship_id = $maximum['MAX(friendship_id)'];
		   if ($friendship_id) {
		   	  return $friendship_id;
		   } else {
		   	  return 0;
		   }
		   
	   }
	   
	   public function send_mes($mes_inf){
		   if (!$mes_inf['message']) {
			   return ;
		   }
		   $key = $this->id + $mes_inf['rec_id'];
		   $query = "INSERT INTO messages (sender_id,sender_name,recipient_id,message,mes_key) VALUES (:sender_id,:sender_name,:recipient_id,:message,:key)";
		   $prep = $this->conn->prepare($query);
	       $prep->bindParam(':sender_id', $this->id);
	       $prep->bindParam(':sender_name', $this->showName());
		   $prep->bindParam(':recipient_id', $mes_inf['rec_id']);
		   $prep->bindParam(':message', $mes_inf['message']);
		   $prep->bindParam(':key', $key);
		   $prep->execute();
	   }
	   
	   public function disp_mes() {
		   $query = "SELECT * FROM messages WHERE recipient_id = $this->id ORDER BY sender_id";
		   $result = $this->conn->query($query);
		   $info = $result->fetchAll(PDO::FETCH_ASSOC);
		   return $info;
	   }
	   
	   // public function fetch_chats() {
		  //  $query = "SELECT sender_id FROM messages WHERE recipient_id = $this->id GROUP BY sender_id";
		  //  $result = $this->conn->query($query);
		  //  $senders = $result->fetchAll(PDO::FETCH_ASSOC);
		  //  foreach($senders as $sender) {
			 //   $sender = $sender['sender_id'];
			 //   $query = "SELECT * FROM messages WHERE recipient_id = $this->id AND sender_id = $sender ORDER BY mes_id DESC";
			 //   $result = $this->conn->query($query);
		  //      $chat = $result->fetchAll(PDO::FETCH_ASSOC);
			 //   $chats[] = $chat;
		  //  }
		  //  function compare($chat1,$chat2) {
			 //   return ($chat1[0]['mes_id'] > $chat2[0]['mes_id']) ? -1 : 1;
		  //  }
		  //  usort($chats,"compare");
		  //  return $chats;
	   // }

	   public function fetch_chats() {
	   	   /* Fetching all the messages the user have sent and/or received */
		   $stat = "SELECT * FROM messages WHERE sender_id=$this->id OR recipient_id=$this->id";
		   $result = $this->conn->query($stat);
		   $mes = $result->fetchAll(PDO::FETCH_ASSOC);
		   $chats = array();
		   $chats_keys = array();
		   /* If the user have any messages we group them into seperate chats */
           if ($mes) {
           	  /* First we go through all of the messages and extract all of distinct message keys and count their total amount - therefore we define how many chats we have and what are the chat keys */
	          $size = sizeof($mes);
	          $num_of_chats = 1; /* we are inside the if statement - it means we have at least 1 chat */
	          $chat_keys[0] = $mes[0]['mes_key'];
	          $chat_destinations[0] = $chat_keys[0] - $this->id;
	          for ($i=1; $i < $size; $i++) { 
		         if (!in_array($mes[$i]['mes_key'], $chat_keys)) {
			        $chat_keys[$num_of_chats] = $mes[$i]['mes_key'];
			        $chat_destinations[$num_of_chats] = $chat_keys[$num_of_chats] - $this->id;
			        $num_of_chats++;
		         }
	          }
	          /* After we defined how many chats we have and what are their keys, we assemble the chats - match every chat key with messages which have message key equal to that chat key */
	          for ($i=0; $i < $num_of_chats; $i++) { 
		      $chats[$i] = array('key'=>$chat_keys[$i],
		      	                 'destination'=>$chat_destinations[$i],
		      	                 'messages'=>array());
		         foreach ($mes as $key => $value) {
			       if ($value['mes_key']==$chat_keys[$i]) {
			       	    if ($value['sender_id']==$this->id) {
			       	    	$direction = 'sent';
			       	    } else {
			       	    	$direction = 'received';
			       	    }
			       	    $chats[$i]['messages'][] = array('message_id'=>$value['message_id'],
			       	    	                             'sender_id'=>$value['sender_id'],
			       	    	                            'recipient_id'=>$value['recipient_id'],
			       	    	                             'message'=>$value['message'],
			       	    	                             'ts'=>$value['ts'],
			       	    	                             'mes_key'=>$value['mes_key'],
			       	    	                             'direction'=>$direction);
			        } 
		         }
	          }
           } 
           return $chats;
           /*$js_chats = json_encode($chats);
           return $js_chats;*/
	   }
	   
	   public function getLast() {
		   $query = "SELECT MAX(message_id) FROM messages WHERE recipient_id = $this->id";
		   $result = $this->conn->query($query);
		   $maximum = $result->fetch(PDO::FETCH_ASSOC);
		   $mes_id = $maximum['MAX(message_id)'];
		   return $mes_id;
		   /*if ($maximum) {
		   	  $mes_id = $maximum['MAX(message_id)'];
		      return $mes_id;
		   } else {
		   	  return 0;
		   }*/
	   }
	   
	   public function get_fresh_messages($last_message_id) {
		   $query ="SELECT sender_id,message FROM messages WHERE recipient_id = $this->id AND mes_id > $last_message_id";
		   $result = $this->conn->query($query);
		   $fresh_messages = $result->fetchAll(PDO::FETCH_ASSOC);
		   return $fresh_messages;
	   }
	   
	   public function get_ordered_chats() {
		   $query = "SELECT sender_id FROM messages WHERE recipient_id = $this->id GROUP BY sender_id";
		   $result = $this->conn->query($query);
		   $senders = $result->fetchAll(PDO::FETCH_ASSOC);
		   foreach($senders as $sender) {
			   $sender = $sender['sender_id'];
			   $chat['chat_id'] = $sender;
			   $query = "SELECT MAX(mes_id) FROM messages WHERE recipient_id = $this->id AND sender_id = $sender";
			   $result = $this->conn->query($query);
			   $latest_mes_id = $result->fetch(PDO::FETCH_ASSOC);
			   $latest_mes_id = $latest_mes_id['MAX(mes_id)'];
			   $chat['latest_mes_id'] = $latest_mes_id;
			   
			   $query = "SELECT message FROM messages WHERE recipient_id = $this->id AND sender_id = $sender AND mes_id = $latest_mes_id";
			   $result = $this->conn->query($query);
			   $latest_mes_text = $result->fetch(PDO::FETCH_ASSOC);
			   $latest_mes_text = $latest_mes_text['message'];
			   $chat['latest_mes_text'] = $latest_mes_text;
			   
			   $query = "SELECT mes_id FROM messages WHERE recipient_id = $this->id AND sender_id = $sender";
			   $result = $this->conn->query($query);
			   while($message = $result->fetch(PDO::FETCH_ASSOC)){
				   $message = $message['mes_id'];
			       $messages[] = $message;
			   }
			   $chat['messages'] = $messages;
			   unset($messages);
			   $chats[] = $chat;
		   }
		   // foreach($chats as $chat){ 
		       // var_dump(json_encode($chat)); echo "<br>"; echo "<br>";
		   // }
		   // var_dump(json_encode($chats)); echo "<br>";
		   
		   return $chats;
	   }
	   
	   public function fetch_received_messages() {
		   $query = "SELECT sender_id FROM messages WHERE recipient_id = $this->id GROUP BY sender_id";
		   $result = $this->conn->query($query);
		   $senders = $result->fetchAll(PDO::FETCH_ASSOC);
		   foreach($senders as $sender) {
			   $sender = $sender['sender_id'];
			   $chat['chat_id'] = $sender;
			   $query = "SELECT message,mes_id FROM messages WHERE recipient_id = $this->id AND sender_id = $sender";
			   $result = $this->conn->query($query);
			   while($fetch=$result->fetch(PDO::FETCH_ASSOC)) {
				   $messages['mes_id'][] = $fetch['mes_id'];
				   $messages['mes_text'][] = $fetch['message'];
			   }
			   $chat[] = $messages;
			   unset($messages);
			   $chats[] = $chat;
			   unset($chat);
		   }
		   return $chats; 
	   }
	   
	   public function fetch_sent_messages() {
		   $query = "SELECT recipient_id FROM messages WHERE sender_id = $this->id GROUP BY recipient_id";
		   $result = $this->conn->query($query);
		   $recipients = $result->fetchAll(PDO::FETCH_ASSOC);
		   foreach($recipients as $recipient) {
			   $recipient = $recipient['recipient_id'];
			   $chat['chat_id'] = $recipient;
			   $query = "SELECT message,mes_id FROM messages WHERE recipient_id = $recipient AND sender_id = $this->id";
			   $result = $this->conn->query($query);
			   while($fetch=$result->fetch(PDO::FETCH_ASSOC)) {
				   $messages['mes_id'][] = $fetch['mes_id'];
				   $messages['mes_text'][] = $fetch['message'];
			   }
			   $chat[] = $messages;
			   unset($messages);
			   $chats[] = $chat;
			   unset($chat);
		   }
		   return $chats; 
	   }
	   
	   public function fetch_messages() {
		   $query = "SELECT sender_id FROM messages WHERE recipient_id = $this->id GROUP BY sender_id";
		   $result = $this->conn->query($query);
		   $senders = $result->fetchAll(PDO::FETCH_ASSOC);
		   foreach($senders as $sender) {
			   $chat_id[] = $sender['sender_id'];
		   }
		   
		   $query = "SELECT recipient_id FROM messages WHERE sender_id = $this->id GROUP BY recipient_id";
		   $result = $this->conn->query($query);
		   $recipients = $result->fetchAll(PDO::FETCH_ASSOC);
		   foreach($recipients as $recipient) {
			   if(!in_array($recipient['recipient_id'],$chat_id)) {
				   $chat_id[] = $recipient['recipient_id'];
			   }
		   }
		   
		   // foreach($chat_id as $key=>$value) {
			   // $query = "SELECT mes_id,message,sender_id FROM messages WHERE sender_id = $value OR recipient_id = $value";
			   // $result = $this->conn->query($query);
			   // $info = $result->fetchAll(PDO::FETCH_ASSOC);
			   // $chat['chat_id'] = $value;
			   // foreach($info as $key=>$value) {
				   // $messages['sender_id'] = $value['sender_id'];
				   // if ($value['sender_id'] == $chat['chat_id']) {
					   // $messages['direction'] = 'received';
				   // } else {
					   // $messages['direction'] = 'sent';
				   // }
				   // $messages['mes_id'] = $value['mes_id'];
				   // $messages['mes_text'] = $value['message'];
				   // $chat['messages'][] = $messages;
				   // unset($messages);
			   // } 
			   // $chats[] = $chat;
			   // unset($chat);
		   // }
		   foreach($chat_id as $key=>$value) {
			   $query = "SELECT mes_id,message,sender_id FROM messages WHERE sender_id = $value OR recipient_id = $value";
			   $result = $this->conn->query($query);
			   $info = $result->fetchAll(PDO::FETCH_ASSOC);
			   $chat_id = $value;
			   foreach($info as $key=>$value) {
				   $messages['sender_id'] = $value['sender_id'];
				   if ($value['sender_id'] == $chat_id) {
					   $messages['direction'] = 'received';
				   } else {
					   $messages['direction'] = 'sent';
				   }
				   $messages['mes_id'] = $value['mes_id'];
				   $messages['mes_text'] = $value['message'];
				   $chat[] = $messages;
				   unset($messages);
			   } 
			   $chats[$chat_id] = $chat;
			   unset($chat);
		   }
		   return $chats;
	   }
	   
   }

?>