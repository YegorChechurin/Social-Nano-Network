<?php 

    use Skeleton\RequestHandling\Request;
    use Models\User;

    require_once '../skeleton/db_con.php';
    
    $request = new Request();
    $user_id = $request->uri[2];
    $user = new User($user_id,$conn);
    $chats = $user->fetch_chats();
    echo $chats;
    
?>