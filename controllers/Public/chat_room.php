<?php 

    use Controllers\Pub\ChatRoom;
    use Skeleton\Database\Database;
    use Models\UserFactory;
    use Models\ServiceFactory;

    require_once 'view_map.php';

    $db = new Database();
    $user_factory = new UserFactory($db);
    $service_factory = new ServiceFactory($db);
    $controller = new ChatRoom();
    $data = $controller->get_view_data($user_factory,$service_factory);
    $controller->load_view($view_map,$data);

