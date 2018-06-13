<?php
    /* We are checking whether the user is logged in */
    session_start();
    if (empty($_SESSION['logged_in']) or $_SESSION['logged_in'] == 0):
       header('Location: log_in.php'); endif;

    $dsn = "mysql:dbname=my_sn;host=localhost";
    $user="root";
    $password="";
    $conn = new PDO($dsn,$user,$password);
  
    /* Getting id of logged in user */
	$log_user_id = $_SESSION['user_id'];

	/* Getting user's name */
    $user = new User($log_user_id);
	$username = $user->showName(); 
    require_once('header.php');
?>

