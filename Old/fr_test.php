<?php
    /* We are checking whether the user is logged in */
    session_start();
    if (empty($_SESSION['logged_in']) or $_SESSION['logged_in'] == 0):
       header('Location: log_in.php'); endif;
    /* Getting id of logged in user */
    $log_user_id = $_SESSION['user_id'];
    /* Making a user object */
    require_once('api.php');
    $user = new User($log_user_id);
    /* Getting parameter from client ajax request */
    $last_friendship_id_js = $_REQUEST['last_friendship_id'];
    /* Getting the most recent friendship ID of the logged in user */
    $last_friendship_id = $user->get_last_friendship_id();
    /* If we see that user has got/lost friends we sent back to the client the up-to-date last friendship ID */
    if ($last_friendship_id != $last_friendship_id_js) {
      echo $last_friendship_id;
    } 
?>