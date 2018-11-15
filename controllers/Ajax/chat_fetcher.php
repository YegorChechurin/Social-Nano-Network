<?php 
    
	use Skeleton\RequestHandling\Request;
    use Skeleton\Database\Database;
    use Models\Messenger;
    
    $request = new Request();
    $user_id = $request->uri[2]; 
    $partner_id = $request->uri[4]; 
    $db = new Database();
    $messenger = new Messenger($db);
    $messages = $messenger->fetch_chat_messages($user_id,$partner_id);
    echo $messages;

?>
