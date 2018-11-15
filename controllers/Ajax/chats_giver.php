<?php 

    use Skeleton\RequestHandling\Request;
    use Skeleton\Database\Database;
    use Models\Messenger;
    
    $request = new Request();
    $user_id = $request->uri[2];
    $db = new Database();
    $messenger = new Messenger($db);
    $chats = $messenger->fetch_user_chats($user_id);
    echo $chats;
    
?>