<?php 

    use Skeleton\RequestHandling\Request;
    use Skeleton\Database\Database;
    use Models\Messenger;

    $request = new Request();
    $mes_info = array();
    $mes_info['sender_id'] = $request->uri[2];
    $mes_info['sender_name'] = $request->POST['user_name'];
    $mes_info['recipient_id'] = $request->POST['partner_id'];
    $mes_info['recipient_name'] = $request->POST['partner_name'];
    $mes_info['message'] = $request->POST['message'];
    $db = new Database();
    $messenger = new Messenger($db);
    $messenger->send_message($mes_info);