<?php 

    use Skeleton\RequestHandling\Request;
    use Models\User;
    
    require_once '../skeleton/db_con.php';
    require_once '../skeleton/Request_class.php';
    require_once '../models/User_class.php';
    
    $request = new Request();
    $user_id = $request->uri[2];
    $user = new User($user_id,$conn);
    $chats = $user->fetch_chats();
    echo $chats;
    
?>