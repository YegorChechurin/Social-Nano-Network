<?php 

    use Skeleton\RequestHandling\Request;
    use Skeleton\Database\Database;
    use Models\FriendBroker;
    
    $request = new Request();
    $user_id = $request->uri[2];
    $last_friendship_id = $request->uri[4];
    $db = new Database();
    $friend_broker = new FriendBroker($db);
    $real_last_friendship_id = $friend_broker->fetch_last_friendship_id($user_id);
    if ($real_last_friendship_id > $last_friendship_id) {
    	$new_friends = $friend_broker->fetch_new_friends($user_id,$last_friendship_id);
    	echo $new_friends;
    } elseif ($real_last_friendship_id < $last_friendship_id) {
        echo json_encode($real_last_friendship_id);
    }