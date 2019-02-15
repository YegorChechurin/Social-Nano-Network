<?php

    namespace Models;
    use Skeleton\Database\Database;

    /**
     * A class which provides means for establishing and maintaining   
     * friendship connections in Social Nano Network. 
     */
    class FriendBroker {

    	/**
    	 * @var Skeleton\Database\Database - Points to an instance of Database class
    	 */
    	private $DB;

        /**
         * @var string - Holds name of the database table which stores all the
         * information about friendship connections in Social Nano Network
         */
        private $table;

    	/**
    	 * A Database object is assigned to $this->DB variable
    	 *
    	 * @param Skeleton\Database\Database $database - Database object
    	 */
    	public function __construct(Database $database) {
    		$this->DB = $database;
            $this->table = 'friends';
    	}

        /**
         * Establishes new friendship connection
         *
         * First checks whether friendship between the users has been already 
         * established. If not posts user IDs and names to the appropriate 
         * database table, thus recording that the users are friends.
         *
         * @param integer $id_1 - User id of the user who is initiating the 
         * friendship (clicking the add to friends button).
         * @param integer $id_2 - User id of the user who has been added to 
         * friends.
         * @param string $name_1 - User name of the user who is initiating the 
         * friendship (clicking the add to friends button).
         * @param string $name_2 - User name of the user who has been added to 
         * friends.
         */
        public function make_friendship($id_1,$id_2,$name_1,$name_2) {
            $fields = ['friendship_id'];
            $clause = '(friend1_id=:id_1 AND friend2_id=:id_2) OR (friend1_id=:id_2 AND friend2_id=:id_1)';
            $map = [
               ':id_1'=>$id_1,
               ':id_2'=>$id_2
            ];
            $friendship_id = $this->DB->select($this->table,$fields,$clause,$map); 
            if (!$friendship_id) {
                $fields = [
                    'friend1_id','friend2_id',
                    'friend1_name','friend2_name'
                ];
                $values = [$id_1,$id_2,$name_1,$name_2];
                $this->DB->insert($this->table,$fields,$values);
            } 
        }

        /**
         * Deletes friendship between users
         *
         * First checks whether friendship between the users already exists. If
         * it does, deletes corresponding record from the appropriate table, thus
         * removing the friendship.
         *
         * @param integer $id_1 - User id of the user who is intitating the end
         * of the friendship.
         * @param integer $id_2 - User id of the user who has been removed from 
         * friends by user with user id $id_1.
         */
        public function delete_friendship($id_1,$id_2) {
            $fields = ['friendship_id'];
            $clause = '(friend1_id=:id_1 AND friend2_id=:id_2) OR (friend1_id=:id_2 AND friend2_id=:id_1)';
            $map = [
               ':id_1'=>$id_1,
               ':id_2'=>$id_2
            ];
            $friendship_id = $this->DB->select($this->table,$fields,$clause,$map); 
            if ($friendship_id) {
                $this->DB->delete($this->table,$clause,$map);
            } 
        }

        /**
         * Fetches information about all the friends that a specific user has
         *
         * If a user has friends, fetches information about every friendship 
         * connection, namely friendship id, friend user id and name.
         *
         * @param integer $user_id - User id of the user for whom information about
         * friendship connections is fetched.
         *
         * @return integer|string[] - Zero if user has no friends, otherwise array 
         * of strings which contains information about every friendship connection
         * the user has, namely friendship id, friend user id and name. 
         */
        public function fetch_all_friends($user_id) {
            $fields = ['*'];
            $clause = 'friend1_id=:id OR friend2_id=:id';
            $map = [':id'=>$user_id];
            $all_info = $this->DB->select($this->table,$fields,$clause,$map);
            if ($all_info) {
                $friend_info = [];
                $n = 0;
                foreach ($all_info as $info_unit) {
                    $friend_info[$n]['friendship_id'] = $info_unit['friendship_id'];
                    if ($info_unit['friend1_id'] == $user_id) {
                        $friend_info[$n]['friend_id'] = $info_unit['friend2_id'];
                        $friend_info[$n]['friend_name'] = $info_unit['friend2_name'];
                    } else {
                        $friend_info[$n]['friend_id'] = $info_unit['friend1_id'];
                        $friend_info[$n]['friend_name'] = $info_unit['friend1_name'];
                    }
                    $n++;
                }
                return json_encode($friend_info);
            } else {
                return 0;
            }
        }

        /**
         * Fetches information about user friendship connections whose 
         * friendship id is greater than the provided friendship id
         *
         * If user has friendship connections with friendship id greater
         * than the provided friendship id, fetches information about all of
         * these friendship connections, namely friendship id, friend user id 
         * and name.
         *
         * @param integer $user_id - User id of the user for whom information about
         * friendship connections is fetched.
         * @param integer $friendship_id - Friendship id value greater which 
         * friendship connections are fetched. 
         *
         * @return integer|string[] - Zero if user has no friendship connections
         * with friendship id greater than the provided friendship id, otherwise 
         * array of strings which contains information about all of these 
         * friendship connections, namely friendship id, friend user id and name. 
         */
        public function fetch_new_friends($user_id,$friendship_id) {
            $fields = ['*'];
            $clause = '(friend1_id=:u_id OR friend2_id=:u_id) AND friendship_id>:f_id';
            $map = [':u_id'=>$user_id,':f_id'=>$friendship_id];
            $friends_raw = $this->DB->select($this->table,$fields,$clause,$map);
            if ($friends_raw) {
                $friends = [];
                $n = 0;
                foreach ($friends_raw as $record) {
                    $friends[$n]['friendship_id'] = $record['friendship_id'];
                    if ($record['friend1_id']==$user_id) {
                        $friends[$n]['friend_id'] = $record['friend2_id'];
                        $friends[$n]['friend_name'] = $record['friend2_name'];
                    } else {
                        $friends[$n]['friend_id'] = $record['friend1_id'];
                        $friends[$n]['friend_name'] = $record['friend1_name'];
                    }
                    $n++; 
                }
                return json_encode($friends);
            } else {
                return 0;
            }
        }

        /**
         * Fetches friendship id of the most recently established friendship 
         * connection by a particular user
         *
         * @param integer $user_id - User id of the user for whom friendship id
         * of the most recently established friendship connection is fetched.
         *
         * @return integer - Zero if user has no friendship connections, otherwise 
         * friendship id of the most recently established friendship connection.
         */
        public function fetch_last_friendship_id($user_id) {
            $fields = ['MAX(friendship_id)'];
            $clause = 'friend1_id=:id OR friend2_id=:id';
            $map = [':id'=>$user_id];
            $result = $this->DB->select($this->table,$fields,$clause,$map);
            $last_friendship_id = $result[0]['MAX(friendship_id)'];
            if ($last_friendship_id) {  
                return $last_friendship_id;
            } else {
                return 0;
            }
        }
    	
    }

?>