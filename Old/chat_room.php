<?php
    require_once('api.php');
    require_once('basic.php');
	
	$user = new User($log_user_id);
    $user_name = $user->showName();
	$chats = $user->fetch_chats();

    $chat_partners = array();
    foreach ($chats as $key => $value) {
        $chat_partner = new User($value['destination']);
        $chat_partners[] = array('id'=>$value['destination'], 'name'=>$chat_partner->showName());
    }
//echo "<br>"."<br>"."<br>"."<br>"; var_dump($chat_partners);  
	$last_rec = $user->getLast();

    $chat_fr_id = $_POST['fr_id'];
    $active_chat_key = $chat_fr_id + $user->showID();
    $chat_fr_name = $_POST['fr_name'];

    /*foreach ($chats as $key => $value) {
        if ($value['key'] == $active_chat_key) {
            $n = 0; // we initialize message counter
            foreach ($value['messages'] as $key => $value) {
                $n++;
            }
        }
    }*/
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
	<link rel="stylesheet" href="chat_room_style.css">
	<script>
        var user_name
		var rec_id;
		var last_rec;
		var active_chat_key;
        var n = 0; // message counter
        var mes_height;
        user_name = JSON.stringify(<?php echo json_encode($user_name); ?>);
        rec_id = <?=$chat_fr_id?>;
        active_chat_key = <?=$active_chat_key;?>;
        //last_rec = <?=$last_rec?>;
        <?php if ($last_rec): ?>
		     last_rec = <?=$last_rec?>;
        <?php else: ?>
             last_rec = 0;
        <?php endif; ?>
        

                function display(){
                    mes_height = document.getElementById("mes").offsetHeight;
                    <?php if(!empty($chats)): ?>
                    <?php foreach($chats as $key => $value): ?> 
                    <?php if($value['key'] == $active_chat_key): ?>
                    <?php foreach($value['messages'] as $message): ?>
                    n++;
                    var message = <?php echo json_encode($message['message']); ?>;
                        <?php if($message['direction']=='received'): ?>
                            document.getElementById("mes").innerHTML += '<div class="message_inlet" id="message'+n+'"><b>Your friend:</b> '+message+'</div><br>';
                        <?php else: ?>
                            document.getElementById("mes").innerHTML += '<div class="message_outlet" id="message'+n+'"><b>You:</b> '+message+'</div><br>';
                        <?php endif; ?>
                    <?php endforeach; ?>    
                    <?php endif; ?>
                    <?php endforeach; ?>
                    var last_mes_pos = document.getElementById("message"+n).offsetTop;
                    if (last_mes_pos > mes_height) {
                        document.getElementById("mes").scrollTop = last_mes_pos; // -correct_pos;
                    }
                    <?php endif; ?>
                }

    	function send(rec_id) {
            n++;
    		var message = document.getElementById("text").value;
    		var xhttp = new XMLHttpRequest();
            xhttp.open("GET", "test.php?rec_id="+rec_id+"&mes="+message, true);
            xhttp.send();
            document.getElementById("mes").innerHTML += '<div class="message_outlet" id="message'+n+'""><b>You:</b> '+message+'</div><br>';
            document.getElementById("text").value = "";
            var last_mes_pos = document.getElementById("message"+n).offsetTop;
            if (last_mes_pos > mes_height) {
                document.getElementById("mes").scrollTop = last_mes_pos; // - correct_pos;
            } 
    	}

    	function mes_test(){
    		var ajax = new XMLHttpRequest();
            ajax.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                	if (this.responseText) {
                		mes = JSON.parse(this.responseText);
                		last_rec = mes.message_id;
                		if (mes.mes_key == active_chat_key) {
                            n++;
                			document.getElementById("mes").innerHTML += '<div class="message_inlet" id="message'+n+'"><b>Your friend:</b> '+mes.message+'</div><br>';
                            var last_mes_pos = document.getElementById("message"+n).offsetTop;
                            var last_mes_height = document.getElementById("message"+n).offsetHeight;
                            if (last_mes_pos > mes_height) {
                                document.getElementById("mes").scrollTop = last_mes_pos; // - 200 + last_mes_height;
                            } 
                        } 
                        else {
                            alert('Congratulations!:) You have just received a new message from user '+mes.sender_name);
                            location.reload();
                        }
                	}
                }
            };
            ajax.open("GET", "rec_test.php?last_rec="+last_rec, true);
            ajax.send();
    	}
        setInterval(mes_test, 1000);
   
    </script>
</head>
<body onload="display()"> 
	<div class="container-fluid" style="padding-top:5%;">
	  <div class="row">
        <div class="col-sm-3">
            <div style="height: 15%; position: relative;">
               <div style="text-align: center; position: absolute; top: 10%; left: 0%; font-size: 175%; background-color: #ffffb3;">You are chatting with <b><?=$chat_fr_name?></b>
               </div>
            </div>
            <div id="chats_wrapper">
              <?php foreach($chat_partners as $key=>$value): ?>
                <?php if($value['id'] != $chat_fr_id): ?>
                  <form method="post" action="chat_room.php">
                     <div class="chat_header">
                       You have an active chat with <?=$value['name']?>
                     </div>
                     <input type="hidden" name="fr_id" value="<?=$value['id']?>">
                     <input type="hidden" name="fr_name" value="<?=$value['name']?>">
                     <input type="submit" value="Go and chat with <?=$value['name']?>">
                  </form>
                <?php endif; ?>
              <?php endforeach; ?>
            </div>
	   </div>
		<div class="col-sm-6" id="mes">      
        </div>
		<div class="col-sm-3" id="sidebar">
            <div style="text-align: center; padding-top: 5%;">
		       If you would like to chat to someone with whom you do not have an active chat yet, then please go to your profile page by clicking your username in the left uppermost corner of the page, and then select that person from your friend list.
		    </div>
	  </div>
	  <div class="row">
		<div class="col-sm-6 col-sm-offset-3">
			<form method="post" id="message" action="inbox.php">
				<textarea class="form-control" rows="4" id="text" form="message" name="message" placeholder="Type your message here:"></textarea>
                <button type="button" class="btn btn-info" onclick="send(rec_id)">Send</button>
                <!--<button onclick="mes_test()">test</button>-->
            </form>
		</div>
	  </div>
	</div>
</body>
</html>