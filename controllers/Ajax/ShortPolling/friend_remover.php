<?php 

    use Skeleton\RequestHandling\Request;
    use Skeleton\Database\Database;
    use Models\FriendBroker;
    use Models\Messenger;
    
    $request = new Request();
    $user_id = $request->uri[2];
    $friend_id = $request->uri[4];
    $db = new Database();
    $friend_broker = new FriendBroker($db);
    $messenger = new Messenger($db);
    $friend_broker->attach_observer($messenger,'friendship_deleted');
    $friend_broker->delete_friendship($user_id,$friend_id);