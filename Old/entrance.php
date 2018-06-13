<?php

    /* Starting cookie-based session in order to then use superglobal $_SESSION 
	for storing there a logged_in/not_logged_in indicator */
    session_start();
	
    /* Connecting to our database */
	try{
		$dsn = "mysql:dbname=my_sn;host=localhost";
		$user="root";
		$password="";
	    $conn = new PDO($dsn,$user,$password);}
	catch (PDOException $e){
		echo 'Connection failed: ' . $e->getMessage();
	}	
	
	/* Collecting all the data submitted by user */
	$s_password = $_POST['password'];
	$email = $_POST['email'];
	
	/* Checking whether the user has already signed up and can proceed logging in */
	$query = "SELECT user_id, password FROM users WHERE email=:email";
    $prep = $conn->prepare($query);
	$prep->bindParam(':email', $email);
	$select = $prep->execute();
	$result = $prep->fetchObject();
	if(!$result):
	    echo 'You have not signed up.'."<br>";
		exit('Please <a href="sign_up.php">sign up</a>');
	/* If the user has already signed up, 
	we check whether the submitted password is equal to the one stored during the registration */
	else:
	    $h_password = $result->password;
		$v_password = password_verify($s_password,$h_password);
		if($v_password):
			$_SESSION['logged_in'] = 1;
			$_SESSION['user_id'] = $result->user_id;
			header('Location: profile_page_backup.php');
		else:
		    $_SESSION['logged_in'] = 0;
		    header('Location: log_in.php?credentials=incorrect');
			exit();
		endif;
	endif;
	
?>