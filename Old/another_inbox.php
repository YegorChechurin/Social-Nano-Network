<?php
    require_once('api.php');
    require_once('basic.php');
	
	$user = new User($log_user_id);
	$name = $user->showName();
	$chats = $user->fetch_chats();
	$last_rec = $user->getLast();

	$js_chats = json_encode($chats);

?>

<!DOCTYPE html>
<html> 
<head>
    <title>Chats</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" charset="UTF-8">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
     <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
	<link rel="stylesheet" href="chats_style.css">
	<script>
		var rec_id;
		var last_rec;
		var active_chat_key;
		last_rec = <?=$last_rec?>;
	    $(function(){
	    	chats = <?=$js_chats?>;
	    	n = chats.length;
	    	for (i = 0; i < chats.length; i++) { 
	    		 = chats[i].key;
                $("#chat"+chats[i].key).click(
                	
                );
            }
        });
	</script>
    <script>

    	function send(rec_id) {
    		var message = document.getElementById("text").value;
    		var xhttp = new XMLHttpRequest();
            xhttp.open("GET", "test.php?rec_id="+rec_id+"&mes="+message, true);
            xhttp.send();
            document.getElementById("mes").innerHTML += '<div class="message_outlet">'+message+'</div>';
            document.getElementById("text").value = "";
    	}

    	function mes_test(){
    		var ajax = new XMLHttpRequest();
            ajax.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                	if (this.responseText) {
                		mes = JSON.parse(this.responseText);
                		last_rec = mes.message_id;
                		if (mes.mes_key == active_chat_key) {
                			document.getElementById("mes").innerHTML += mes.message;
                		}
                	}
                }
            };
            ajax.open("GET", "rec_test.php?last_rec="+last_rec, true);
            ajax.send();
    	}
        setInterval(mes_test, 3000);
        
    </script>
</head>

<body> 
    <header>
	    <nav class="navbar navbar-default navbar-fixed-top" style="background: linear-gradient(to bottom, #33ccff 0%, #ff99cc 100%)">
		    <div class="container-fluid">
			    <div class="navbar-header">
				    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
					    <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span> 
					</button>
					<a class="navbar-brand" href="profile_page.php"><?=$name?></a>
				</div>
				<div class="collapse navbar-collapse" id="myNavbar">
				    <ul class="nav navbar-nav">
					    <li ><a href="#">Home</a></li>
                        <li><a href="#">Inbox <span class="badge">17</span></a></li>
                        <li><a href="#">Page 2</a></li> 
                        <li><a href="#">Page 3</a></li> 
					</ul>
					<ul class="nav navbar-nav navbar-right">
					    <li><a href="log_out.php"><span class="glyphicon glyphicon-log-out"></span> Log out</a></li>
					</ul>
				</div>
			</div>
		</nav>
	</header>
	<div class="container-fluid" style="margin-top:0px">
	  <div class="row">
        <div class="col-sm-3 col-sm-offset-0" id="chats_wrapper">
	    <?php foreach($chats as $key=>$value): ?>
		    <div class="yellow chat_header" id="chat<?=$value['key']?>">Chat id=<?='chat'.$value['key']?> You and User with ID=<?=$value['destination']?></div>
		<?php endforeach; ?>
		    <div class="yellow chat_header" id="chat">1</div>
			<div class="yellow chat_header" id="chat">2</div>
			<div class="yellow chat_header" id="chat">3</div>
			<div class="yellow chat_header" id="chat">4</div>
			<div class="yellow chat_header" id="chat">5</div>
			<div class="yellow chat_header" id="chat">6</div>
			<div class="yellow chat_header" id="chat">7</div>
			<div class="yellow chat_header" id="chat">8</div>
			<div class="yellow chat_header" id="chat">9</div>
			<div class="yellow chat_header" id="chat">10</div>
	    </div>
		<div class="col-sm-6" id="mes"></div>
		<nav class="col-sm-3" id="sidebar">
		    This is a sidebar
		    <p><a href="#">This is the 1st link</a></p>
			<p><a href="#">This is the 2nd link</a></p>
			<p><a href="#">This is the 3rd link</a></p>
		</nav>
	  </div>
	  <div class="row">
		<div class="col-sm-6 col-sm-offset-3">
			<form method="post" id="message" action="inbox.php">
				<textarea class="form-control" id="text" form="message" name="message" placeholder="Type your message here:"></textarea>
                <!-- <input type="submit" class="btn btn-info" onclick="send(rec_id)" value="Send"> -->
                <button type="button" class="btn btn-info" onclick="send(rec_id)">Send</button>
            </form>
		</div>
	  </div>
	</div>
</body>
</html>