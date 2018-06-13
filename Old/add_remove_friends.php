<?php
    require_once 'api.php';
    require_once 'db_con.php';

    /* We are checking whether the user is logged in */
    session_start();
    if (empty($_SESSION['logged_in']) or $_SESSION['logged_in'] == 0):
       header('Location: log_in.php'); endif;
	   
    /* Getting id of logged in user */
	$log_user_id = $_SESSION['user_id'];

    /* Getting user's name */
    $user = new User($log_user_id);
	$username = $user->showName(); 

	//echo gettype($log_user_id)."<br>"."<br>";   

	//echo gettype($username)."<br>"."<br>";  

    /* Adding/removing a friend */
	if (!empty($_POST) and $_POST['action']=='add') {
		$member_id = $_POST['member_id'];
        $stat_1 = "SELECT friendship_id FROM friends WHERE fr1_id=$log_user_id AND fr2_id=$member_id";
        $impl_1 = $conn->query($stat_1);
        $check_1 = $impl_1->fetch();
        $stat_2 = "SELECT friendship_id FROM friends WHERE fr1_id=$member_id AND fr2_id=$log_user_id";
        $impl_2 = $conn->query($stat_2);
        $check_2 = $impl_2->fetch();
        if (!$check_1 && !$check_2) {
        	$stat = "INSERT INTO friends (fr1_id,fr2_id) VALUES ($log_user_id,$member_id)";
		    $impl = $conn->query($stat);
        }
	} elseif (!empty($_POST) and $_POST['action']=='remove') {
		$member_id = $_POST['member_id'];
		$stat_1 = "DELETE FROM friends WHERE fr1_id=$log_user_id AND fr2_id=$member_id";
		$impl_1 = $conn->query($stat_1);
		$stat_2 = "DELETE FROM friends WHERE fr2_id=$log_user_id AND fr1_id=$member_id";
		$impl_2 = $conn->query($stat_2);
	}

    header('Location: profile_page_backup.php');
?>