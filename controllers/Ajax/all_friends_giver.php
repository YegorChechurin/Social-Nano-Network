<?php 

    use Skeleton\RequestHandling\Request;
    use Skeleton\Database\Database;
    use Models\FriendBroker;
    
    $request = new Request();
    $user_id = $request->uri[2];
    $db = new Database();
    $friend_broker = new FriendBroker($db);
    $friends = $friend_broker->fetch_all_friends($user_id);
    echo $friends;
    
?>