<?php 
    
	use Skeleton\RequestHandling\Request;
    use Models\Chat;

    require_once '../skeleton/db_con.php';
    require_once '../skeleton/Request_class.php';
    require_once '../models/Chat_class.php';
    
    $request = new Request();
    $user_id = $request->uri[2]; //$_REQUEST['user_id'];
    $partner_id = $request->uri[4]; // $_REQUEST['partner_id'];
    $messages=Chat::fetchMessages($conn,$user_id,$partner_id);
    $output = json_encode($messages);
    echo $output;

?>
