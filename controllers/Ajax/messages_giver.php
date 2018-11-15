<?php 

    use Skeleton\RequestHandling\Request;
    use Skeleton\Database\Database;
    use Models\Messenger;

    function long_polling(Messenger $messenger, $user_id, $message_id, $timestamp) 
    {
        $start = $timestamp;
        $finish = $start + 20;
        $messages = $messenger->fetch_received_messages($user_id,$message_id);
        if ($messages) {
            echo $messages;
        } else {
            sleep(5);
            $current = time();
            if ($current > $finish) {
                exit;
            } else {
                long_polling($messenger,$user_id,$message_id,$start);
            }
        }
    }
    
    $request = new Request();
    $user_id = $request->uri[2]; 
    $message_id = $request->uri[4]; 
    $db = new Database();
    $messenger = new Messenger($db);
    $ts = time();
    long_polling($messenger,$user_id,$message_id,$ts);
      
?>