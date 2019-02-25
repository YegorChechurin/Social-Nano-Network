<?php 

    use Skeleton\RequestHandling\Request;
    use Skeleton\Database\Database;
    use Models\FriendBroker;
    
    $request = new Request();
    $user_id = $request->uri[2];
    $friend_id = $request->uri[4];
    $names = [];
    parse_str($request->query_string,$names);
    $db = new Database();
    $friend_broker = new FriendBroker($db);
    $friend_broker->make_friendship($user_id,$friend_id,$names['name1'],
    	$names['name2']);
    $friendship_id = $friend_broker->fetch_friendship_id($user_id,$friend_id);
    echo $friendship_id;
    
?>