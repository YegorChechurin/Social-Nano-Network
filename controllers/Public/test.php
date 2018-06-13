<?php 
    require_once '../models/db_con.php';
    require_once '../models/user_class.php';
    require_once '../models/chat_class.php';
    require_once '../models/message_class.php';

    $user = new User(1,$conn);
    $user_id = $user->showID();
    $user_name = $user->showName();
    $chats_raw = $user->fetch_chats();
    $chats = json_decode($chats_raw,true);
    $last_rec_mes_id = $user->getLast();

    // foreach ($chats as $key => $value) {
    // 	var_dump($value); echo "<br>"."<br>"."<br>";
    // 	$messages=Chat::fetchMessages($conn,$user_id,$value['partner_id']);
    // 	echo $messages[0]['message']."<br>";
    // 	var_dump($messages); echo "<br>"."<br>"."<br>";
    // } 
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
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body onload="form_chats()">
	<div class="container-fluid" style="padding-top:5%;">
	  <div class="row">
        <div class="col-sm-3">
            <div id="chats_wrapper">
            </div>
	   </div>
		<div class="col-sm-6" id="mes" style="height: 370px;">      
        </div>
		<div class="col-sm-3" id="sidebar">
            <div style="text-align: center; padding-top:5%">
		       If you would like to chat to someone with whom you do not have an active chat yet, then please go to your profile page by clicking your username in the left uppermost corner of the page, and then select that person from your friend list.
		    </div>
	  </div>
	  <div class="row">
		<div class="col-sm-6 col-sm-offset-3">
				<textarea class="form-control" rows="4" id="text" form="message" name="message" placeholder="Type your message here:"></textarea>
                <button type="button" class="btn btn-info" onclick="send_message()">Send</button>
		</div>
	  </div>
	</div>
</body>
<script type="text/javascript">
	var test;
	var mes_height = document.getElementById("mes").offsetHeight;
	var n; // message counter reflected in message html id
	var id;
	var name;
	var user_id = <?=json_encode($user_id)?>;
	var user_name = <?=json_encode($user_name)?>;
	var chats = <?=$chats_raw?>;
	var last_rec_mes_id = <?=$last_rec_mes_id?>;

	function form_chat_header(chat) {
		if (chat.partner_id==id) {
			document.getElementById("chats_wrapper").innerHTML += '<div class="active_chat_header" id="c'+chat.partner_id+'" onclick="display_chat('+chat.partner_id+')">'+'<b>'+chat.partner_name+'</b><br>'+chat.last_mes_auth_name+': '+chat.last_mes_text+'</div>';
		} else {
			document.getElementById("chats_wrapper").innerHTML += '<div class="chat_header" id="c'+chat.partner_id+'" onclick="display_chat('+chat.partner_id+')">'+'<b>'+chat.partner_name+'</b><br>'+chat.last_mes_auth_name+': '+chat.last_mes_text+'</div>';
		}
	}

	function form_chats() {
		var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
        	if (this.readyState == 4 && this.status == 200) {
        		if (this.responseText) {
                    chats = JSON.parse(this.responseText); 
                    document.getElementById("chats_wrapper").innerHTML = '';
                    chats.forEach(form_chat_header);
        		}
        	}
        }
        xhttp.open("GET", "chats_giver.php?user_id="+user_id, true);
        xhttp.send();
	}

	setInterval(form_chats, 3000);

	function display_message(message) {
		if (message.sender_id==id || message.recipient_id==id) {
			n++;
		    if (message.sender_id == user_id) {
			    document.getElementById("mes").innerHTML += '<div class="message_outlet" id="m'+n+'"><b>You:</b> '+message.message+'</div><br>';
		    } else {
                document.getElementById("mes").innerHTML += '<div class="message_inlet" id="m'+n+'"><b>'+message.sender_name+':</b> '+message.message+'</div><br>';
		    }
		    var last_mes_pos = document.getElementById("m"+n).offsetTop;
            if (last_mes_pos > mes_height) {
                document.getElementById("mes").scrollTop = last_mes_pos; 
            }
		} else {
			alert("You received new message from "+message.sender_name);
		}
	} 

	function display_chat(partner_id) { 
		n = 0;
	    id = partner_id;  
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
        	if (this.readyState == 4 && this.status == 200) {
        		if (this.responseText) {
                    document.getElementById("mes").innerHTML = '';
                    var messages = JSON.parse(this.responseText); 
                    messages.forEach(display_message);
                    var last_mes_pos = document.getElementById("m"+n).offsetTop;
                    if (last_mes_pos > mes_height) {
                        document.getElementById("mes").scrollTop = last_mes_pos; 
                    }
        		}
        	}
        }
        xhttp.open("GET", "chat_fetcher.php?user_id="+user_id+"&partner_id="+partner_id, true);
        xhttp.send();
        chats.forEach(function(item){
        	if (item.partner_id==id) {
        		document.getElementById("c"+id).className = "active_chat_header";
        	} else {
        		document.getElementById("c"+item.partner_id).className = "chat_header";
        	}
        });
    }

    function send_message() {
    	recipient_id = id;
        var message = document.getElementById("text").value;
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
        	if (this.readyState == 4 && this.status == 200) {
        		n++;
        		document.getElementById("mes").innerHTML += '<div class="message_outlet" id="m'+n+'"><b>You:</b> '+message+'</div><br>';
        		var last_mes_pos = document.getElementById("m"+n).offsetTop;
                if (last_mes_pos > mes_height) {
                    document.getElementById("mes").scrollTop = last_mes_pos; 
                }
                document.getElementById("text").value = '';
        	}
        }
        xhttp.open("GET", "message_sender.php?user_id="+user_id+"&user_name="+user_name+"&partner_id="+id+"&message="+message, true);
        xhttp.send();
    }

    function messages_listener() {
    	var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
        	if (this.readyState == 4 && this.status == 200) {
        		if (this.responseText) {
        			test = JSON.parse(this.responseText);
        			var last_index = test.length;
        			last_rec_mes_id = test[last_index-1].message_id;
        			test.forEach(display_message);
        		}
        	}
        }
        xhttp.open("GET", "messages_giver.php?user_id="+user_id+"&mes_id="+last_rec_mes_id, true);
        xhttp.send();
    }

    setInterval(messages_listener, 3000);
</script>