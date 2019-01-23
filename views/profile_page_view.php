<!DOCTYPE html>
<html> 
<head>
    <title>Profile</title>
    <?php require '../views/setup.php' ?>
    <script type="text/javascript" src="http://localhost/SNN/js/profile_page/ProfilePageHandler.js"></script>
</head>
<body>
	<div class="container-fluid" style="padding-top:0%; height: 100vh;">
		<div class="row" style="height: 10%; background-color: blue">
        	<div>Navbar</div>
    	</div>
    	<div class="row" style="height: 90%;">
        	<div class="col-sm-6" id="friends" style="background-color: green">
               <h2>Your friends</h2>    
            </div>
        	<div class="col-sm-6" id="inventory" style="background-color: gray">
               <h2>Other users registered in Social Nano Network</h2>    
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
            var h = new ProfilePageHandler();
            h.build_bars();
        }
    );
</script>