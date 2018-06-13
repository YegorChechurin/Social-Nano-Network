<?php
    /* We are checking whether the user is logged in */
    session_start();
    if (empty($_SESSION['logged_in']) or $_SESSION['logged_in'] == 0):
       header('Location: log_in.php'); endif;

    require_once('api.php');

    /* Getting id of logged in user */
	$log_user_id = $_SESSION['user_id'];
    $user = new User($log_user_id);

    $rec_id = $_REQUEST['rec_id']; 
    $mes = $_REQUEST['mes'];

    $mes_inf = array('rec_id'=>$rec_id, 'message'=>$mes);
    $user->send_mes($mes_inf);

	/*$last_rec = $_REQUEST['last_rec'];
	echo $last_rec;*/

	/*$stat = "SELECT * FROM messages WHERE recipient_id=$log_user_id AND message_id>$last_rec";
	$impl = $conn->query($stat);
    $res = $impl->fetchObject();
    if ($res) {
        $mes = json_encode($res);
        echo $mes;
    } */    
    
	
?>



