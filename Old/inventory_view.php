<?php
  require_once 'api.php';
  require_once 'db_con.php';
  require_once 'basic.php';

	/* Getting friend IDs of the logged in user */
	$user = new User($log_user_id);
	$username = $user->showName();
	$fr_IDs = $user->get_fr_IDs();
	
	/* Retrieving all the other members from the database */
	$query = "SELECT user_id,username,email FROM users WHERE user_id<>:log_user_id";
    $prep = $conn->prepare($query);
	$prep->bindParam(':log_user_id', $log_user_id);
	$select = $prep->execute();
	$result = $prep->fetchAll();	
	foreach($result as $member){
		$members[] = $member;
	}
	
	/* Information about members which will be displayed */
	$parameters = ['username', 'email'];
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <title>Members</title>
</head>

<body>
	<div><a href="profile_page.php">My Profile</a></div>
	<div><h1>Complete list of all the memebers (except you):</h1></div>

<?php foreach($members as $member) { ?>
      <form method="post" action="add_remove_friends.php">
	    <?php foreach($parameters as $par): ?>
		<?php	echo $member[$par]."<br>"; ?>
        <?php endforeach; ?>
		<?php if(!in_array($member['user_id'],$fr_IDs)): ?>
		     <input type="hidden" name="member_id" value="<?php echo $member['user_id']; ?>">
		     <input type="hidden" name="member_name" value="<?php echo $member['username']; ?>">
		     <input type="hidden" name="action" value="add">
		     <input type="submit" value="Add as friend">
	    <?php else: ?>
		     <input type="hidden" name="member_id" value="<?php echo $member['user_id']; ?>">
		     <input type="hidden" name="action" value="remove">
		     <input type="submit" value="Remove from friends">
	    <?php endif; ?>
	  </form>	  
<?php } ?>
</body>

</html> 