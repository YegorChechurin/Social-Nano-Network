<!DOCTYPE html>
<html> 
<head>
    <title>Chats</title>
    <?php require '../views/setup.php' ?>
    <script type="text/javascript" src="http://localhost/SNN/js/chat_room/events_map.js"></script>
    <script type="text/javascript" src="http://localhost/SNN/js/chat_room/MessagesListener.js"></script>
    <script type="text/javascript" src="http://localhost/SNN/js/chat_room/ChatsBarHandler.js"></script>
    <script type="text/javascript" src="http://localhost/SNN/js/chat_room/MessagesHandler.js"></script>
    <script type="text/javascript" src="http://localhost/SNN/js/chat_room/Broker.js"></script>
	<link rel="stylesheet" type="text/css" href="http://localhost/SNN/css/chat_room/chat_room_style.css">
</head>
<body>
<div class="container-fluid" style="padding-top:5%; height: 100vh;">
    <div class="row" style="height: 10%;">
        <div>Navbar</div>
    </div>
	<div class="row" style="height: 60%;">
        <div class="col-sm-3">
            <div id="chats_wrapper">
            </div>
	   </div>
		<div class="col-sm-6 d-none d-sm-block" id="mes" >      
        </div>
		<!--<div class="col-sm-3 d-none d-sm-block" id="sidebar">-->
        <div class="col-sm-3" id="sidebar">
            <div style="text-align: center; padding-top:5%">
		       If you would like to chat to someone with whom you do not have an active chat yet, then please go to your profile page by clicking your username in the left uppermost corner of the page, and then select that person from your friend list.
		    </div>
	    </div>
    </div>
	  <div class="row justify-content-center" style="height: 30%;">
		<div class="col-sm-6 d-none d-sm-block">
				<textarea class="form-control" rows="4" id="text" name="message" placeholder="Type your message here:"></textarea>
                <button type="button" id="send_button" class="btn btn-info">Send</button>
		</div>
	  </div>
</div>
</body>
<script type="text/javascript">
    var active_id = <?=$data['active_id']?>; // id of partner with whom the chat is displayed on screen now
    var active_name = <?=json_encode($data['active_name'])?>; // name of partner with whom the chat is displayed on screen now
    var user_id = <?=$data['user_id']?>;
    var user_name = <?=json_encode($data['user_name'])?>;
    var last_rec_mes_id = <?=$data['last_rec_mes_id']?>;
    var mes_height = document.getElementById("mes").offsetHeight;
    var n; // message counter reflected in message html id
    var chats;

    $("#send_button").click(
        function(){
            var l = new MessagesListener();
            l.listen_sent_messages();
        }
    );

    $(document).ready(
        function(){
            var h = new ChatsBarHandler();
            h.form_chats_bar();
            var l = new MessagesListener();
            l.listen_incoming_messages();
        }
    );
</script>




