<?php

    class Chat {

    	public static function createChat($conn,$mes_info) {
    	   /* Assinging all the necessary values */	
		       $conn = $conn;
    	     $sender_id = $mes_info['sender_id'];
    	     $sender_name = $mes_info['sender_name'];
    	     $recipient_id = $mes_info['recipient_id'];
    	     $recipient_name = $mes_info['recipient_name'];
           $message = $mes_info['message'];
           $key = $sender_id + $recipient_id;
           /* Posting a chat into database into chats table */
           $query = "INSERT INTO chats 
           (participant1_id,participant1_name,participant2_id,participant2_name,last_mes_auth_id,last_mes_auth_name,last_mes_text,chat_key) 
           VALUES 
           (:sender_id,:sender_name,:recipient_id,:recipient_name,:sender_id,:sender_name,:message,:key)";
           $prep = $conn->prepare($query);
           $prep->bindParam(':sender_id', $sender_id);
           $prep->bindParam(':sender_name', $sender_name);
           $prep->bindParam(':recipient_id', $recipient_id);
           $prep->bindParam(':recipient_name', $recipient_name);
           $prep->bindParam(':message', $message);
           $prep->bindParam(':key', $key);
           $prep->execute();
    	}

    	public static function updateChat($conn,$mes_info) {
    	   /* Assinging all the necessary values */	
		     $conn = $conn;
    	   $sender_id = $mes_info['sender_id'];
    	   $sender_name = $mes_info['sender_name'];
    	   $recipient_id = $mes_info['recipient_id'];
         $message = $mes_info['message'];
         /* Updating last message infromation for corresponding chat in database in chats table */
         $query = "
              UPDATE chats SET 
		   	  last_mes_auth_id=:sender_id, 
		   	  last_mes_auth_name=:sender_name,
		   	  last_mes_text=:message
		   	  WHERE (participant1_id=:sender_id AND participant2_id=:recipient_id) OR (participant1_id=:recipient_id AND participant2_id=:sender_id)
		   ";
		     $prep = $conn->prepare($query);
		     $prep->bindParam(':sender_id', $sender_id);
	       $prep->bindParam(':sender_name', $sender_name);
	       $prep->bindParam(':recipient_id', $recipient_id);
		     $prep->bindParam(':message', $message);
		     $prep->execute();
    	}

    	public static function fetchMessages($conn,$id_1,$id_2) {
          $conn = $conn;
          $query = "SELECT * FROM messages WHERE (sender_id=$id_1 AND recipient_id=$id_2) OR (sender_id=$id_2 AND recipient_id=$id_1)";
          $result = $conn->query($query);
	   	    $messages = $result->fetchAll(PDO::FETCH_ASSOC);
	   	    return $messages;
    	}

    }
?>