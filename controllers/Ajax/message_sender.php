<?php 

    require_once '../models/db_con.php';
    require_once '../models/user_class.php';
    require_once '../models/request_class.php';
    require_once '../models/message_class.php';

    $request = new Request();
    $mes_info = array();
    $mes_info['sender_id'] = $request->uri[2];
    $mes_info['sender_name'] = $request->POST['user_name'];
    $mes_info['recipient_id'] = $request->POST['partner_id'];
    $mes_info['recipient_name'] = $request->POST['partner_name'];
    $mes_info['message'] = $request->POST['message'];
    Message::postMessage($conn,$mes_info);
?>