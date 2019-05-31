<?php 

    use Controllers\Ajax\LongPolling\FriendsPoll;
    use Skeleton\RequestHandling\Request;
    use Skeleton\Database\Database;
    use Models\FriendBroker;

    $request = new Request();
    $db = new Database();
    $friend_broker = new FriendBroker($db);

    $friends_poll = new FriendsPoll($request,$friend_broker);
    $friends_poll->get_query_parameter();
    $start = time();
    $finish = $start + 40;
    $sleeping_interval = 15;
    $friends_poll->poll($start,$finish,$sleeping_interval);