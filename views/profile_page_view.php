<!DOCTYPE html>
<html> 
<head>
    <title>Profile</title>
    <?php require '../views/setup.php' ?>
    <script type="text/javascript" src="http://localhost/SNN/js/profile_page/ProfilePageHandler.js"></script>
    <link rel="stylesheet" type="text/css" href="http://localhost/SNN/css/profile_page_style.css">
</head>
<body>
	<div class="container-fluid" style="padding-top:0%; height: 100vh;">
		<div class="row" id="navbar">
        	<div id="messenger_link" class="text"></div>
    	</div>
    	<div class="row" style="height: 90%;">
        	<div class="col-sm-6" id="friends_bar">
                <h3 class="text">Your friends</h3>  
                <div id="friends"></div> 
            </div>
        	<div class="col-sm-6" id="inventory">
               <h3 class="text">Other users registered in Social Nano Network</h3>
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

    var friends;

    var users;

    $(document).ready(
        function(){
            /**
             * Creating a link to user's messenger page in the left corner of 
             * navigation bar
             */
            var href = 'http://localhost/SNN/public/'+user_id+'/messenger';
            var profile_link = '<a href="'+href+'">MESSENGER</a>';
            $('#messenger_link').html(profile_link);

            var h = new ProfilePageHandler();
            h.build_bars();
        }
    );
</script>