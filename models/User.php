<?php

	namespace Models;

    class User {
	   
	   protected $DB;
	   protected $id;	   
	   protected $name;
	   protected $fr_IDs;
	   protected $fr_info;
       protected $last_friendship_id;
       protected $chats;
	   
	   public function __construct($us_id,$database) {
		   $this->id = $us_id;
		   $this->DB = $database;
           $this->name = $this->fetchName();
 	   }
	   
	   public function showID() {
		   return $this->id;
	   }

	   public function fetchName() {
		   $table = 'users';
		   $fields = ['username'];
		   $clause = 'user_id=:id';
		   $map = [':id'=>$this->id];
		   $result = $this->DB->select($table,$fields,$clause,$map);
		   $this->name = $result[0]['username'];
	   }
	   
	   public function showName() {
		   return $this->name;
	   }

	   /* public function get_fr_IDs() {
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
		   
	   } */
	   
    }
?>