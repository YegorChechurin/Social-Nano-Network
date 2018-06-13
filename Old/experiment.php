<?php
    require_once('api.php');
    require_once('basic.php');
	
	/*$user = new User($log_user_id);
	$name = $user->showName();
	$chats = $user->fetch_chats();
	$last_rec = $user->getLast();*/

?>

<!DOCTYPE html>
<html> 
<head>
    <title>Experiment</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" charset="UTF-8">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
     <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
	<link rel="stylesheet" href="chats_style.css">
    <script>
    	function mes_test(){
    		var ajax = new XMLHttpRequest();
            ajax.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                	if (this.responseText) {	
                	     document.getElementsById("1").innerHTML = this.responseText;
                	}
                }
            };
            ajax.open("GET", "exp_feed.php", true);
            ajax.send();
    	}
        setInterval(mes_test, 3000);       
    </script>
</head>
<body>
<div id="1"></div>		    
</body>
</html>