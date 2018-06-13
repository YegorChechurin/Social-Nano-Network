<?php
    require_once('api.php');
    require_once('basic.php');
	
	/* Getting ids and names of friends of the logged in user */
	$user = new User($log_user_id); 
  // $fr_ids = $user->get_fr_IDs();
  // $fr_names = $user->get_fr_names(); 
  // $n = sizeof($fr_ids);
  //      for($i=0; $i<=$n-1; $i++){
  //        $frs[$i] = array('id' => $fr_ids[$i],
  //                       'name' => $fr_names[$i]);
  //      }
  $frs = $user->get_fr_info();
	if (empty($frs)) {
		echo 'You have no friends! In NSN you can send messages only to frineds. 
			         So first go and get some friends.';
	}
	if (!empty($_POST)) {
		$user->send_mes($_POST);
	}
	
?>

<!DOCTYPE html>
<html> 

<head>
  <meta charset="UTF-8">
  <title>Messages</title>
</head>

<body>

  <p>Go back to <a href="profile_page.php">my profile page</a></p>

  <p>
    <label for="rec">Choose recipient</label>
    <select form="message" name="rec_id" id="rec">
	  <?php foreach($frs as $fr) { ?>
              <option value="<?php echo $fr['id']; ?>"><?php echo $fr['name']; ?></option>
	  <?php } ?>
    </select>
  </p>
  
  <p>
    <label for="sub">Subject</label>
	  <input form="message" type="text" name="sub" id="sub" placeholder="Type message subject here"></input>
  </p>

  <textarea rows="10" cols="60" name="message" form="message" placeholder="Type your message here:"></textarea>
  <br>
  <form method="post" id="message">
    <input type="submit" value="Send message">
  </form>

</body>

</html>