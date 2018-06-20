<?php 
    require_once 'view_map.php';
    //require_once 'PublicController_class.php';
    require_once 'ChatRoom_class.php';
    /*require_once '../models/db_con.php';
    require_once '../models/user_class.php';
    require_once '../models/request_class.php';*/
    
    /*$request = new Request();
    $user_id = $request->uri[2];
    $user = new User($user_id,$conn);
    $user_name = $user->showName();
    $last_rec_mes_id = $user->getLast();

    if ($request->POST) {
        $active_id = $request->POST['fr_id'];
        $active_name = $request->POST['fr_name'];
    } else {
        $active_id = 0;
        $active_name = '';
    }

    require '../views/chat_room_view.php';*/

    use Controllers\Pub\ChatRoom;

    $controller = new ChatRoom();
    $data = $controller->get_view_data($conn);
    $controller->load_view($view_map,$data);

?>

