<?php

    namespace Models;
    use Models\iObservable;
    use Models\iChangeFetcher;
    use Skeleton\Database\Database;

    /**
     * A class which provides means for establishing and maintaining   
     * friendship connections in Social Nano Network. 
     */
    class FriendBroker implements iObservable, iChangeFetcher {

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
         * @var Object[] - Contains subscribed observers
         */
        private $observers;

    	/**
    	 * A Database object is assigned to $this->DB variable
    	 *
    	 * @param Skeleton\Database\Database $database - Database object
    	 */
    	public function __construct(Database $database) {
    		$this->DB = $database;
            $this->table = 'friends';
            $this->observers = [];
    	}

        public function attach_observer(iObserver $observer, $event) {
            if (array_key_exists($event, $this->observers)) {
                if (!in_array($observer,$this->observers[$event])) {
                    $this->observers[$event][] = $observer;
                }
            } else {
                $this->observers[$event] = [];
                $this->observers[$event][] = $observer;
            }
        }

        public function detach_observer(iObserver $observer, $event) {
            if (in_array($observer,$this->observers[$event])) {
                $key = array_search($observer,$this->observers[$event]);
                array_splice($this->observers[$event], $key, 1);
            } 
        }

        public function fetch_event_observers($event) {
            if (array_key_exists($event, $this->observers)) {
                return $this->observers[$event];
            } else {
                throw new \Exception('No observers are attached for event called '."'".$event."'");
            }
                
        }

        public function fire_event($event, $data) {
            try {
                $event_observers = $this->fetch_event_observers($event);
                foreach ($event_observers as $observer) {
                    $observer->process_event($event,$data);
                }
            } catch (\Exception $e) {
                echo 'Exception caught: '.$e->getMessage();
                echo "<br>";
            }
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
                $event = 'friendship_made';
                $data = [$id_1,$id_2];
                $this->fire_event($event,$data);
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
            try {
                $friendship_id = $this->fetch_friendship_id($id_1,$id_2);
                $clause = 'friendship_id=:id';
                $map = [':id'=>$friendship_id];
                $this->DB->delete($this->table,$clause,$map);
                try {
                    $event = 'friendship_deleted';
                    $data = [$id_1,$id_2];
                    $this->fire_event($event,$data);
                } catch (\Exception $e) {
                    echo 'Exception caught: '.$e->getMessage()."<br>";
                }
            } catch (\Exception $e) {
                echo "Exception caught: friendship between users with user IDs $id_1 and $id_2 cannot be deleted as it does not exist"."<br>";
            }
        }

        /**
         * Fetches friendship id of friendship connection established between 
         * specific users
         *
         * @param integer $id_1 - User id of the 1st user involved in the
         * friendship.
         *
         * @param integer $id_2 - User id of the 2nd user involved in the
         * friendship.
         *
         * @return string - Friendship id of the friendship between users with
         * IDs $id_1 and $id_2.
         */
        public function fetch_friendship_id($id_1,$id_2) {
            $fields = ['friendship_id'];
            $clause = '(friend1_id=:id_1 AND friend2_id=:id_2) OR (friend1_id=:id_2 AND friend2_id=:id_1)';
            $map = [
               ':id_1'=>$id_1,
               ':id_2'=>$id_2
            ];
            $outcome = $this->DB->select($this->table,$fields,$clause,$map);
            if ($outcome) {
                $friendship_id = $outcome[0]['friendship_id'];
                return $friendship_id;
            } else {
                throw new \Exception("No friendship id can be fetched, as no friendship between users with user IDs $id_1 and $id_2 exist");
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
                return $friend_info;
            } else {
                return 0;
            }
        }

        public function fetch_specific_friends($user_id,$friendship_IDs) {
            if ($friendship_IDs) {
                $fields = ['*'];
                $placeholders = [];
                foreach ($friendship_IDs as $key => $value) {
                    $placeholders[] = ':parameter'.$key;
                }
                $placeholders_string = implode(',',$placeholders);
                $clause = 'friendship_id IN ('.$placeholders_string.')';
                $map = [];
                $n = 0;
                foreach ($placeholders as $placeholder) {
                    $map[$placeholder] = $friendship_IDs[$n];
                    $n++;
                }
                $result = $this->DB->select($this->table,$fields,$clause,$map);
                if ($result) {
                    $friends = [];
                    $n = 0;
                    foreach ($result as $record) {
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
                    return $friends;
                } else {
                    throw new \Exception("No friendship records can be fetched for provided friendship IDs");     
                }
            } else {
                throw new \Exception("No friendship_IDs provided");           
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
         * @return integer|array - Zero if user has no friendship connections
         * with friendship id greater than the provided friendship id, otherwise 
         * array of arrays which contains information about all of these 
         * friendship connections, namely friendship id, friend user id and name. 
         */
        public function fetch_new_friends($user_id,$friendship_id) {
            $fields = ['*'];
            $clause = '(friend1_id=:u_id OR friend2_id=:u_id) AND friendship_id>:f_id';
            $map = [':u_id'=>$user_id,':f_id'=>$friendship_id];
            $new_friends_raw = $this->DB->select($this->table,$fields,$clause,$map);
            if ($new_friends_raw) {
                $new_friends = [];
                $n = 0;
                foreach ($new_friends_raw as $record) {
                    $new_friends[$n]['friendship_id'] = $record['friendship_id'];
                    if ($record['friend1_id']==$user_id) {
                        $new_friends[$n]['friend_id'] = $record['friend2_id'];
                        $new_friends[$n]['friend_name'] = $record['friend2_name'];
                    } else {
                        $new_friends[$n]['friend_id'] = $record['friend1_id'];
                        $new_friends[$n]['friend_name'] = $record['friend1_name'];
                    }
                    $n++; 
                }
                return $new_friends;
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

        public function fetch_friendship_IDs($user_id) {
            $fields = ['friendship_id'];
            $clause = 'friend1_id=:id OR friend2_id=:id';
            $map = [':id'=>$user_id];
            $result = $this->DB->select($this->table,$fields,$clause,$map);
            if ($result) {
                $friendship_IDs = [];
                foreach ($result as $key => $value) {
                      $friendship_IDs[] = $value['friendship_id'];
                }  
                return $friendship_IDs;
            } else {
                return 0;
            }
        }

        public function fetch_changes($user_id,$friendship_IDs) {
            $real_friendship_IDs = $this->fetch_friendship_IDs($user_id);
            if ($real_friendship_IDs && $friendship_IDs) {
                $friend_data = $this->process_friend_change($user_id,$friendship_IDs,$real_friendship_IDs);
                return $friend_data;
            } else {
                $friend_data = $this->process_extreme_friend_change($user_id,$friendship_IDs,$real_friendship_IDs);
                return $friend_data;
            }
        }

        public function process_friend_change($user_id,$friendship_IDs,$real_friendship_IDs) {
            $lost_friendship_IDs = array_values(array_diff($friendship_IDs,$real_friendship_IDs));
            $obtained_friendship_IDs = array_values(array_diff($real_friendship_IDs,$friendship_IDs));
            if ($lost_friendship_IDs && $obtained_friendship_IDs && $lost_friendship_IDs!=$friendship_IDs) {
                try {
                    $new_friends = $this->fetch_specific_friends($user_id,$obtained_friendship_IDs);
                } catch (\Exception $e) {
                    echo 'Exception caught: '.$e->getMessage();
                    echo "<br>";
                }
                $friend_data = [
                    'friends_obtained'=>'yes',
                    'friends_lost'=>'yes',
                    'lost_friendship_IDs'=>$lost_friendship_IDs,
                    'new_friends'=>$new_friends,
                    'friendship_IDs'=>$real_friendship_IDs
                ];
                return json_encode($friend_data);
            } elseif ($lost_friendship_IDs && $obtained_friendship_IDs && $lost_friendship_IDs==$friendship_IDs) {
                $new_friends = $this->fetch_all_friends($user_id);
                $friend_data = [
                    'friends_obtained'=>'all_new',
                    'friends_lost'=>'all_lost',
                    'lost_friendship_IDs'=>$lost_friendship_IDs,
                    'new_friends'=>$new_friends,
                    'friendship_IDs'=>$real_friendship_IDs
                ];
                return json_encode($friend_data);
            } elseif (!$lost_friendship_IDs && $obtained_friendship_IDs) {
                $new_friends = $this->fetch_specific_friends($user_id,$obtained_friendship_IDs);
                $friend_data = [
                    'friends_obtained'=>'yes',
                    'friends_lost'=>'no',
                    'lost_friendship_IDs'=>0,
                    'new_friends'=>$new_friends,
                    'friendship_IDs'=>$real_friendship_IDs
                ];
                return json_encode($friend_data);
            } elseif ($lost_friendship_IDs && !$obtained_friendship_IDs) {
                $friend_data = [
                    'friends_obtained'=>'no',
                    'friends_lost'=>'yes',
                    'lost_friendship_IDs'=>$lost_friendship_IDs,
                    'new_friends'=>0,
                    'friendship_IDs'=>$real_friendship_IDs
                ];
                return json_encode($friend_data);
            }
        }

        public function process_extreme_friend_change($user_id,$friendship_IDs,$real_friendship_IDs) {
            if (!$real_friendship_IDs && $friendship_IDs) {
                $friend_data = [
                    'friends_obtained'=>'no',
                    'friends_lost'=>'all_lost',
                    'lost_friendship_IDs'=>$friendship_IDs,
                    'new_friends'=>0,
                    'friendship_IDs'=>0
                ];
                return json_encode($friend_data);
            } elseif ($real_friendship_IDs && !$friendship_IDs) {
                $last_friendship_id = 0;
                $new_friends = $this->fetch_new_friends($user_id,$last_friendship_id);
                $friend_data = [
                    'friends_obtained'=>'all_new',
                    'friends_lost'=>'no',
                    'lost_friendship_IDs'=>0,
                    'new_friends'=>$new_friends,
                    'friendship_IDs'=>$real_friendship_IDs
                ];
                return json_encode($friend_data);
            }
        }
    	
    }