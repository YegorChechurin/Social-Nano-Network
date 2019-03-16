<?php 

    use Skeleton\RequestHandling\Request;
    use Skeleton\Database\Database;
    use Models\FriendBroker;

    function long_polling(FriendBroker $friend_broker, $user_id, $friendship_IDs, $timestamp) 
    {
        $start = $timestamp;
        $finish = $start + 40;
        $friend_data = $friend_broker->fetch_friend_data($user_id,$friendship_IDs);
        if ($friend_data) {
            echo $friend_data;
        } else {
            sleep(15);
            $current = time();
            if ($current > $finish) {
                exit;
            } else {
                long_polling($friend_broker, $user_id, $friendship_IDs,$start);
            }
        }
    }

    $request = new Request();
    $user_id = $request->uri[2];
    parse_str($request->query_string,$query_string);
    $friendship_IDs = json_decode($query_string['IDs']);
    $db = new Database();
    $friend_broker = new FriendBroker($db);
    $ts = time();
    long_polling($friend_broker,$user_id,$friendship_IDs,$ts);