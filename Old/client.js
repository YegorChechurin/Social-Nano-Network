        var rec_id;
		var last_rec;
		var active_chat_key;
        var n = 0; // message counter
        var correct_pos = 400; // correct last message offsetTop in div with id="mes"
        var mes_height;
        rec_id = <?=$chat_fr_id?>;
        active_chat_key = <?=$active_chat_key;?>;
        //last_rec = <?=$last_rec?>;
        <?php if ($last_rec): ?>
		     last_rec = <?=$last_rec?>;
        <?php else: ?>
             last_rec = 0;
        <?php endif; ?>

                function display(){
                    //mes_height = document.getElementById("mes").offsetHeight;
                    //document.getElementById("mes").innerHTML += mes_height;
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
                    if (last_mes_pos > correct_pos) {
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
            if (last_mes_pos > correct_pos) {
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
                            if (last_mes_pos > correct_pos) {
                                document.getElementById("mes").scrollTop = last_mes_pos; // - 200 + last_mes_height;
                            } 
                        } 
                        else {
                            alert('Congratulations!:) You have just received a new message from user with id'+mes.sender_id);
                            location.reload();
                        }
                	}
                }
            };
            ajax.open("GET", "rec_test.php?last_rec="+last_rec, true);
            ajax.send();
    	}
        setInterval(mes_test, 1000);