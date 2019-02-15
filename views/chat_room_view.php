<!DOCTYPE html>
<html> 
<head>
    <title>Chats</title>
    <?php require '../views/setup.php' ?>
    <script type="text/javascript" src="http://localhost/SNN/js/chat_room/events_map.js"></script>
    <script type="text/javascript" src="http://localhost/SNN/js/chat_room/MessagesListener.js"></script>
    <script type="text/javascript" src="http://localhost/SNN/js/chat_room/FriendsListener.js"></script>
    <script type="text/javascript" src="http://localhost/SNN/js/chat_room/ChatsBarHandler.js"></script>
    <script type="text/javascript" src="http://localhost/SNN/js/chat_room/MessagesHandler.js"></script>
    <script type="text/javascript" src="http://localhost/SNN/js/chat_room/FriendsHandler.js"></script>
    <script type="text/javascript" src="http://localhost/SNN/js/chat_room/Broker.js"></script>
	<link rel="stylesheet" type="text/css" href="http://localhost/SNN/css/chat_room_style.css">
</head>
<body>
<div class="container-fluid" style="padding-top:0%; height: 100vh;">
    <div class="row" id="navbar">
        <div id="profile_link" class="text"></div>
    </div>
	<div class="row" style="height: 60%;">
        <div id="chats_wrapper" class="col-sm-3"></div>
		<div class="col-sm-6 d-none d-sm-block" id="mes"></div>
		<div class="col-sm-3 d-none d-sm-block" id="sidebar">
        <!--<div class="col-sm-3" id="sidebar">-->
            <div class="text" style="text-align: center; padding-top:5%">
		       Friends you have no chats with:
		    </div>
            <div id="friends"></div>
	    </div>
    </div>
	  <div class="row justify-content-center" style="height: 30%;">
		<div class="col-sm-6 d-none d-sm-block">
				<textarea class="form-control" rows="4" id="text" name="message" placeholder="Type your message here:" style="width:100%;height:75%"></textarea>
                <button type="button" id="send_button" class="btn btn-info" style="height:25%">Send</button>
		</div>
	  </div>
</div>
</body>
<script type="text/javascript">
    /**
     * User id of a chat partner with whom the chat 
     * is displayed on screen now.
     * @type {number}
     */
    var active_id = <?=$data['active_id']?>; 

    /**
     * Name of the chat partner with whom the chat 
     * is displayed on screen now.
     * @type {string}
     */ 
    var active_name = <?=json_encode($data['active_name'])?>;

    /**
     * User id of the user.
     * @type {number}
     */ 
    var user_id = <?=$data['user_id']?>;

    /**
     * User name of the user.
     * @type {string}
     */
    var user_name = <?=json_encode($data['user_name'])?>;

    /**
     * Message id of the most recently (last) 
     * received message by the user.
     * @type {number}
     */
    var last_rec_mes_id = <?=$data['last_rec_mes_id']?>; 

    /**
     * Friendship id of the most recently (last) 
     * established user's friendship connection.
     * @type {number}
     */
    var last_friendship_id = <?=$data['last_friendship_id']?>;

    /** 
     * Height of the HTML element with id "mes". It is the element where all the messages of a particular chat are displayed. We need to know its height in order to dispay messages in such a way so that "mes" scrolls when it becomes full and the last message is always displayed in the right place. 
     * @type {number}
     */
    var mes_height = document.getElementById("mes").offsetHeight;

    /** 
     * This is a counter needed to count how many messages is displayed in "mes". It is used as a part of each message HTML id in order to track position of the last displayed message and to scroll "mes" element accordingly. 
     * @type {number}
     */
    var number_of_messages_displayed; 

    /** 
     * Array of chat objects. Each chat object contains 
     * all the necessary metadata about a specific chat.
     * @type {Object[]}
     */
    var chats;
    
    /** 
     * Array of user friends. Each friend is represented by an object which
     * contains friend id and friend name.
     * @type {Object[]}
     */
    var friends;

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
             * Creating a link to user's profile page in the left corner of 
             * navigation bar
             */
            var href = 'http://localhost/SNN/public/'+user_id+'/profile';
            var profile_link = '<a href="'+href+'">My profile page</a>';
            $('#profile_link').html(profile_link);

            /** 
             * Assembling chats bar located in "chats_wrapper" HTML element. Class "ChatsBarHandler" is located in js/chat_room/ChatsBarHandler.js 
             */
            var h = new ChatsBarHandler();
            h.build_chats_bar();

            /** 
             * Starting a listener which will be sending AJAX requests for new incoming messages on a regular basis. 
             */
            var l = new MessagesListener();
            l.listen_incoming_messages();

            /** 
             * Starting a listener which will be sending AJAX requests for new friends on a regular basis. 
             */
            var l = new FriendsListener();
            l.listen_new_friends();
        }
    );
</script>




