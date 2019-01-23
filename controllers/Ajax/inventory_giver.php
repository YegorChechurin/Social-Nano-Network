<?php 

    use Skeleton\RequestHandling\Request;
    use Skeleton\Database\Database;
    use Models\UserTracker;
    
    $request = new Request();
    $user_id = $request->uri[2];
    $db = new Database();
    $user_tracker = new UserTracker($db);
    $users = $user_tracker->fetch_all_registered_users($user_id);
    echo $users;
    
?>