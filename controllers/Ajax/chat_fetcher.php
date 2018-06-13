<?php 
    
    require_once '../models/db_con.php';
    require_once '../models/request_class.php';
    require_once '../models/chat_class.php';
    
    $request = new Request();
    $user_id = $request->uri[2]; //$_REQUEST['user_id'];
    $partner_id = $request->uri[4]; // $_REQUEST['partner_id'];
    $messages=Chat::fetchMessages($conn,$user_id,$partner_id);
    $output = json_encode($messages);
    echo $output;

?>
