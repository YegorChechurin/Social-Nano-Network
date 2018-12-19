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

        public function make_friendship($id_1,$id_2,$name_1,$name_2) {
            // check whether the friendship already exists, if not, post a new friendship
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

        public function fetch_all_friends($user_id) {
            /*$fields = ['users.user_id','users.username'];
            $clause = 'INNER JOIN users ON users.user_id=friends.fr_id WHERE friends.friendship_id=array_containing_list_of_IDs';*/

            $fields = [
                    'friend1_id','friend2_id',
                    'friend1_name','friend2_name'
                ];
            $clause = 'friend1_id=:id OR friend2_id=:id';
            $map = [':id'=>$user_id];
            $all_info = $this->DB->select($this->table,$fields,$clause,$map);
            $friend_info = [];
            $n = 0;
            foreach ($all_info as $info_unit) {
                if ($info_unit['friend1_id'] == $user_id) {
                    $friend_info[$n]['friend_id'] = $info_unit['friend2_id'];
                    $friend_info[$n]['friend_name'] = $info_unit['friend2_name'];
                } else {
                    $friend_info[$n]['friend_id'] = $info_unit['friend1_id'];
                    $friend_info[$n]['friend_name'] = $info_unit['friend1_name'];
                }
                $n++;
            }
            return $friend_info;
        }

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