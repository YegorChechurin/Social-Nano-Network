<?php 

    require_once '../models/db_con.php';
    require_once '../models/user_class.php';
    require_once '../models/request_class.php';
    
    $request = new Request();
    $user_id = $request->uri[2];
    $user = new User($user_id,$conn);
    $chats = $user->fetch_chats();
    echo $chats;
    
?>