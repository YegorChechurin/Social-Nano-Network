<?php 

    use Skeleton\RequestHandling\Request;
    use Models\Message;

    require_once '../skeleton/db_con.php';

    $request = new Request();
    $mes_info = array();
    $mes_info['sender_id'] = $request->uri[2];
    $mes_info['sender_name'] = $request->POST['user_name'];
    $mes_info['recipient_id'] = $request->POST['partner_id'];
    $mes_info['recipient_name'] = $request->POST['partner_name'];
    $mes_info['message'] = $request->POST['message'];
    Message::postMessage($conn,$mes_info);
    
?>