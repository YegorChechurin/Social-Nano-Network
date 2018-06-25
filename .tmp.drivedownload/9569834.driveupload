<?php

	namespace Models;

    require_once 'chat_class.php';

    class Message {

    	public static function postMessage($conn,$mes_info) {
    	   /* Stopping empty message from being sent */
		   if (!$mes_info['message']) {
			   return ;
		   }
		   /* Assinging all the necessary values */	
		   $conn = $conn;
    	   $sender_id = $mes_info['sender_id'];
    	   $sender_name = $mes_info['sender_name'];
    	   $recipient_id = $mes_info['recipient_id'];
    	   $recipient_name = $mes_info['recipient_name'];
           $message = $mes_info['message'];
           /* Posting the message to the database into messages table */
		   $query = "INSERT INTO messages (sender_id,sender_name,recipient_id,recipient_name,message) VALUES (:sender_id,:sender_name,:recipient_id,:recipient_name,:message)";
		   $prep = $conn->prepare($query);
	       $prep->bindParam(':sender_id', $sender_id);
	       $prep->bindParam(':sender_name', $sender_name);
		   $prep->bindParam(':recipient_id', $recipient_id);
		   $prep->bindParam(':recipient_name', $recipient_name);
		   $prep->bindParam(':message', $message);
		   $prep->execute();
		   /* Checking whether the message begins a new chat */
		   $query = "SELECT * FROM chats WHERE (participant1_id=$sender_id AND participant2_id=$recipient_id) OR (participant1_id=$recipient_id AND participant2_id=$sender_id)";
		   $action = $conn->query($query);
		   $result = $action->fetch();
		   if (!$result) {
		   	  // create a new chat
		   	  Chat::createChat($conn,$mes_info);
		   } else {
		   	  // update existing chat information about last message
		   	  Chat::updateChat($conn,$mes_info);
		   }
 	    }

    }
?>