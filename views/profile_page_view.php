<!DOCTYPE html>
<html> 
<head>
    <title>Profile</title>
    <?php require '../views/setup.php' ?>
    <script type="text/javascript" src="http://localhost/SNN/js/profile_page/MessagesListener.js"></script>
    <script type="text/javascript" src="http://localhost/SNN/js/profile_page/FriendsListener.js"></script>
    <script type="text/javascript" src="http://localhost/SNN/js/profile_page/FriendsHandler.js"></script>
    <script type="text/javascript" src="http://localhost/SNN/js/profile_page/LostFriendsHandler.js"></script>
    <script type="text/javascript" src="http://localhost/SNN/js/profile_page/NewFriendsHandler.js"></script>
    <script type="text/javascript" src="http://localhost/SNN/js/profile_page/FriendsBarHandler.js"></script>
    <script type="text/javascript" src="http://localhost/SNN/js/profile_page/UsersHandler.js"></script>
    <script type="text/javascript" src="http://localhost/SNN/js/profile_page/UsersBarHandler.js"></script>
    <script type="text/javascript" src="http://localhost/SNN/js/profile_page/Mediator.js"></script>
    <script type="text/javascript" src="http://localhost/SNN/js/profile_page/PageLoadMediator.js"></script>
    <script type="text/javascript" src="http://localhost/SNN/js/profile_page/FriendsMediator.js"></script>
    <script type="text/javascript" src="http://localhost/SNN/js/profile_page/ChatsMediator.js"></script>
    <script type="text/javascript" src="http://localhost/SNN/js/profile_page/Event.js"></script>
    <!--<script type="text/javascript" src="http://localhost/SNN/js/profile_page/.js"></script>
    <script type="text/javascript" src="http://localhost/SNN/js/profile_page/.js"></script>
    <script type="text/javascript" src="http://localhost/SNN/js/profile_page/.js"></script>-->
    <link rel="stylesheet" type="text/css" href="http://localhost/SNN/css/profile_page_style.css">
</head>
<body>
	<div class="container-fluid" style="padding-top:0%; height: 100vh;">
		<div class="row" id="navbar">
        	<div id="messenger_link" class="text"></div>
    	</div>
    	<div class="row" style="height: 90%;">
        	<div class="col-sm-6" id="friends_bar">
                <div id="friend_caption">
                    <h3 class="text">Your friends</h3>
                </div>  
                <div id="friends"></div> 
            </div>
        	<div class="col-sm-6" id="inventory">
                <div id="user_caption">
                    <h3 class="text">Other users registered in Social Nano Network</h3>
                </div>
            </div>
    	</div>
	</div>
</body>
<script type="text/javascript">
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

    var users;

    var friends;

    var friends_IDs = [];

    var friendship_IDs = <?=json_encode($data['friendship_IDs'])?>; 

    var friends_bar_height = document.getElementById("friends_bar").offsetHeight;

    var last_rec_mes_id = <?=$data['last_rec_mes_id']?>; 

    $(document).ready(
        function(){
            /**
             * Creating a link to user's messenger page in the left corner of 
             * navigation bar
             */
            var href = 'http://localhost/SNN/public/'+user_id+'/messenger';
            var profile_link = '<a href="'+href+'">MESSENGER</a>';
            $('#messenger_link').html(profile_link);

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