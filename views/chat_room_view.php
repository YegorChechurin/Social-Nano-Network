<!DOCTYPE html>
<html> 
<head>
    <title>Chats</title>
    <?php require '../views/setup.php' ?>
    <script type="text/javascript" src="http://localhost/SNN/js/chat_room/MessagesListener.js"></script>
    <script type="text/javascript" src="http://localhost/SNN/js/chat_room/MessagesBarHandler.js"></script>
    <script type="text/javascript" src="http://localhost/SNN/js/chat_room/FriendsListener.js"></script>
    <script type="text/javascript" src="http://localhost/SNN/js/chat_room/FriendsHandler.js"></script>
    <script type="text/javascript" src="http://localhost/SNN/js/chat_room/LostFriendsHandler.js"></script>
    <script type="text/javascript" src="http://localhost/SNN/js/chat_room/NewFriendsHandler.js"></script>
    <script type="text/javascript" src="http://localhost/SNN/js/chat_room/FriendsBarHandler.js"></script>
    <script type="text/javascript" src="http://localhost/SNN/js/chat_room/ChatsHandler.js"></script>
    <script type="text/javascript" src="http://localhost/SNN/js/chat_room/ChatsBarHandler.js"></script>
    <script type="text/javascript" src="http://localhost/SNN/js/chat_room/Mediator.js"></script>
    <script type="text/javascript" src="http://localhost/SNN/js/chat_room/ChatsMediator.js"></script>
    <script type="text/javascript" src="http://localhost/SNN/js/chat_room/FriendsMediator.js"></script>
    <script type="text/javascript" src="http://localhost/SNN/js/chat_room/PageLoadMediator.js"></script>
    <script type="text/javascript" src="http://localhost/SNN/js/chat_room/Event.js"></script>
	<link rel="stylesheet" type="text/css" href="http://localhost/SNN/css/chat_room_style.css">
</head>
<body>
<div class="container-fluid" style="padding-top:0%; height: 100vh;">
    <div class="row" id="navbar">
        <div id="profile_link" class="text"></div>
    </div>
	<div class="row" style="height: 60%;">
        <div id="chats_wrapper" class="col-sm-3"></div>
		<div id="mes" class="col-sm-6 d-none d-sm-block"></div>
		<div id="sidebar" class="col-sm-3 d-none d-sm-block">
        <!--<div class="col-sm-3" id="sidebar">-->
            <div id="friend_caption" class="text" style="text-align: center; padding-top:5%">
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
    "use strict";

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

    var chat_partner_IDs = [];
    
    /** 
     * Array of user friends. Each friend is represented by an object which
     * contains friend id and friend name.
     * @type {Object[]}
     */
    var friends;

    var friendship_IDs = <?=json_encode($data['friendship_IDs'])?>; 

    $(document).ready(
        function(){
            /**
             * Creating a link to user's profile page in the left corner of 
             * navigation bar
             */
            var href = 'http://localhost/SNN/public/'+user_id+'/profile';
            var profile_link = '<a href="'+href+'">My profile page</a>';
            $('#profile_link').html(profile_link);

            var mediator = new Mediator();
            var chats_mediator = new ChatsMediator(mediator);
            mediator.attach_topic_mediator_pair('chats',chats_mediator);
            var friends_mediator = new FriendsMediator(mediator);
            mediator.attach_topic_mediator_pair('friends',friends_mediator);
            var page_load_mediator = new PageLoadMediator(mediator);
            mediator.attach_topic_mediator_pair('page_load',page_load_mediator);

            var event = new Event('page_load','page_loaded');
            mediator.process_event(event);
        }
    );
</script>




