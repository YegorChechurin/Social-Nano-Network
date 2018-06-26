<?php 

    use Controllers\Pub\ChatRoom;

    require_once 'view_map.php';
    require_once 'ChatRoom_class.php';

    $controller = new ChatRoom();
    $data = $controller->get_view_data($conn);
    $controller->load_view($view_map,$data);

?>

