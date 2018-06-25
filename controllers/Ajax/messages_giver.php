<?php 

    use Skeleton\RequestHandling\Request;
    use Models\User;
    
    require_once '../skeleton/db_con.php';
    require_once '../skeleton/Request_class.php';
    require_once '../models/User_class.php';

    function long_polling(User $user, $message_id, $timestamp) {
        $start = $timestamp;
        $finish = $start + 20;
        $messages_raw = $user->fetch_received_messages($message_id);
        if ($messages_raw) {
            $messages = json_encode($messages_raw);
            echo $messages;
        } else {
            sleep(5);
            $current = time();
            if ($current > $finish) {
                exit;
            } else {
                long_polling($user,$message_id,$start);
            }
        }
    }
    
    $request = new Request();
    $user_id = $request->uri[2]; 
    $message_id = $request->uri[4]; 
    $user = new User($user_id,$conn);
    
    $ts = time();
    long_polling($user,$message_id,$ts);
      
?>