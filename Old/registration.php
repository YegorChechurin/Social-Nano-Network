<?php
require_once 'db_con.php';
	
	/* Collecting all the data submitted by user */
	$username = $_POST['username'];
	$s_password = $_POST['password'];
	$email = $_POST['email'];
	
	/* Checking whether the submitted email address has been already used to sign up */
	$query = "SELECT user_id FROM users WHERE email=:email";
    $prep = $conn->prepare($query);
	$prep->bindParam(':email', $email);
	$select = $prep->execute();
	$result = $prep->fetch();
	if($result):
	    echo 'An account for this email address already exists.'."<br>";
		exit('Please use another email address to <a href="sign_up.php">sign up</a>'."<br>");
	endif;
	
	/* Hashing the submitted password */
	$h_password = password_hash($s_password,PASSWORD_DEFAULT);
	
	/* Making the actual user registration */
	$query = "INSERT INTO users (username,password,email) VALUES (:username, :h_password, :email)";
    $prep = $conn->prepare($query);
	$prep->bindParam(':username', $username);
	$prep->bindParam(':h_password', $h_password);
	$prep->bindParam(':email', $email);
	$insert = $prep->execute();
	if ($insert):
	  echo 'Congratulations - you have been successfully signed up to the Nano Social Network!'."<br>";
	  echo 'Now you can <a href="log_in.php">log in</a> or go <a href="home.php">Home</a>';
	endif;

?>