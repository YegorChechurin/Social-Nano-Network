<?php
  /* We are checking whether the user has already tried to log in */
  $qs = $_SERVER['QUERY_STRING'];
  if ($qs == 'credentials=incorrect'):
      echo 'You have entered wrong credentials, please try again:'."<br>"."<br>";
  endif;
?>

<!DOCTYPE html>
<html> 

<head>
  <meta charset="UTF-8">
  <title>Log in</title>
</head>

<body>

  <form method="post" action="entrance.php">     
	<table>
		<tr>
			<td>Email:</td>
			<td><input type="text" name="email"></td>
		</tr>
		<tr>
			<td>Password:</td>
			<td><input type="password" name="password"></td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" value="Log in"></td>
	    </tr>
	</table>
  </form>
  
</body>

</html>


