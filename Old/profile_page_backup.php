<?php
  require_once 'api.php';
  require_once 'basic.php';

  /* Getting friend IDs of the logged in user */
  $user = new User($log_user_id);
  $username = $user->showName();
  $fr_IDs = $user->get_fr_IDs();
 
  /* Getting friend info of the logged in user */
  $fr_info = $user->get_fr_info();
  
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

  /* Getting the most recent friendship ID of the logged in user */
  $last_friendship_id = $user->get_last_friendship_id();
?>

<!DOCTYPE html>
<html> 

<head>
  <meta charset="UTF-8">
  <title>My profile</title>
  <title>Chats</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" charset="UTF-8">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
     <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
     <link rel="stylesheet" href="prof_p_style.css">
     <script>
       var last_friendship_id 
       last_friendship_id = <?=$last_friendship_id?>;
       function fr_test(){
        var ajax = new XMLHttpRequest();
            ajax.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                  if (this.responseText) {
                    if (this.responseText > last_friendship_id) {
                      last_friendship_id = this.responseText;
                      alert('Congratulations! Someone has just added you to there friend list!');
                      location.reload();
                    } else {
                      last_friendship_id = this.responseText;
                      alert('So sad... Someone has just removed you from there friend list...');
                      location.reload();
                    }
                  }
                }
            };
            ajax.open("GET", "fr_test.php?last_friendship_id="+last_friendship_id, true);
            ajax.send();
      }
        setInterval(fr_test, 1000);
     </script>
</head>

<body>
  <div class="container-fluid" style="margin-top:50px">
    <div class="row">
        <div class="col-sm-4">
          <div><h1>Your friends</h1></div>
          <div id="chat" style="text-align: center;">
             <?php if (!$fr_info): ?>
               <?php echo 'You have no friends! In Social Nano Network you can send messages only to friends. So first add some friends.'; ?>
          <?php else: ?>
             <?php foreach ($fr_info as $key => $value): ?>
                 <form method="post" action="chat_room.php">
                   <div><?=$value['name']?></div>
                   <input type="hidden" name="fr_id" value="<?=$value['id']?>">
                   <input type="hidden" name="fr_name" value="<?=$value['name']?>">
                   <input type="submit" value="Go and chat with <?=$value['name']?>">
                 </form>
             <?php endforeach; ?>
          <?php endif; ?>
          </div>
        </div>
        <div class="col-sm-5">
          <div><h1>List of network members</h1></div>
          <div id="inventory" style="text-align: center;">
             <?php foreach($members as $member) { ?>
               <form method="post" action="add_remove_friends.php">
                <?php foreach($parameters as $par): ?>
                   <?php echo $member[$par]."<br>"; ?>
                <?php endforeach; ?>
                <?php if(!in_array($member['user_id'],$fr_IDs)): ?>
                  <input type="hidden" name="member_id" value="<?php echo $member['user_id']; ?>">
                  <input type="hidden" name="member_name" value="<?php echo $member['username']; ?>">
                  <input type="hidden" name="action" value="add">
                  <input type="submit" value="Add to friends">
                <?php else: ?>
                  <input type="hidden" name="member_id" value="<?php echo $member['user_id']; ?>">
                  <input type="hidden" name="action" value="remove">
                  <input type="submit" value="Remove from friends">
                <?php endif; ?>
               </form>   
             <?php } ?>
          </div>
        </div>
        <div class="col-sm-3">
          <div><h1>Advertisments</h1></div>
          <div id="money" style="text-align: center;">
            Your advertisment could be here. Hurry up, because soon Social Nano Network will become so popular! 
          </div>
        </div> 
    </div>
</div>
</body>

</html>