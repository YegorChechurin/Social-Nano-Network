<!DOCTYPE html>
<html> 

<head>
  <meta charset="UTF-8">
  <title>Sign up</title>
</head>

<body>

  <div>Already signed up? Go to <a href="log_in.php">log in</a><br><br></div>
  
  <form method="post" action="registration.php">      
	<table>
		<tr>
			<td>Username:</td>
			<td><input type="text" name="username"></td>
		</tr>
		<tr>
			<td>Password:</td>
			<td><input type="password" name="password"></td>
		</tr>
		<tr>
			<td>Email:</td>
			<td><input type="text" name="email"></td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" value="Sign up"></td>
	    </tr>
	</table>
  </form>
  
</body>

</html>



