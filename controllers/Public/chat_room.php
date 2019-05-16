<?php 

    use Controllers\Pub\ChatRoom;
    use Skeleton\Database\Database;
    use Models\UserTracker;
    use Models\Messenger;
    use Models\FriendBroker;

    require_once 'view_map.php';

    $db = new Database();
    $user_tracker = new UserTracker($db);
    $messenger = new Messenger($db);
    $friend_broker = new FriendBroker($db);
    $controller = new ChatRoom($user_tracker,$messenger,$friend_broker);
    $data = $controller->get_view_data();
    $controller->load_view($view_map,$data);

