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
		<div class="col-sm-6 d-none d-sm-block" id="mes">    
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
    /**
     * User id of a chat partner with whom the chat is displayed on screen now
     * @type {number}
     */
    var active_id = <?=$data['active_id']?>; 

    /**
     * Name of the chat partner with whom the chat is displayed on screen now
     * @type {string}
     */ 
    var active_name = <?=json_encode($data['active_name'])?>;

    /**
     * User id of the user
     * @type {number}
     */ 
    var user_id = <?=$data['user_id']?>;

    /**
     * User name of the user
     * @type {string}
     */
    var user_name = <?=json_encode($data['user_name'])?>;

    /**
     * Message id of the most recently (last) received message by the user
     * @type {number}
     */
    var last_rec_mes_id = <?=$data['last_rec_mes_id']?>;

    /** 
     * Height of the HTML element with id "mes". It is the element where all the messages of a particular chat are displayed. We need to know its height in order to dispay messages in such a way so that "mes" scrolls when it becomes full and the last message is always displayed in the right place 
     * @type {number}
     */
    var mes_height = document.getElementById("mes").offsetHeight;

    /** 
     * This is a counter needed to count how many messages is displayed in "mes". It is used as a part of each message HTML id in order to track position of the last displayed message and to scroll "mes" element accordingly 
     * @type {number}
     */
    var n; 

    /** 
     * Array of chat objects. Each chat object contains all the necessary metadata about a specific chat 
     * @type {array}
     */
    var chats;

    /** 
     * Programming "send_button" so that it can send messages. Class "MessagesListener" is located in js/chat_room/MessagesListener.js 
     */
    $("#send_button").click(
        function(){
            var l = new MessagesListener();
            l.listen_sent_messages();
        }
    );

    $(document).ready(
        function(){
            /** 
             * Assembling chats bar located in "chats_wrapper" HTML element. Class "ChatsBarHandler" is located in js/chat_room/ChatsBarHandler.js 
             */
            var h = new ChatsBarHandler();
            h.form_chats_bar();

            /** 
             * Starting a listener which will be sending AJAX requests for new incoming messages on a regular basis 
             */
            var l = new MessagesListener();
            l.listen_incoming_messages();
        }
    );
</script>




