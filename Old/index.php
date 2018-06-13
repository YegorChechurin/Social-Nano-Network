<!DOCTYPE html>
<html> 
<head>
    <title>Home</title>
	<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0" charset="UTF-8">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
     <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>
     <link rel="stylesheet" href="index.css">
</head>
<body>
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-6" style="background-color: #ffffb3; height: 100%">
				<div class="half">
					<div class="wrapper">
					    <div class="box">
						    Welcome to Social Nano Network - a super small scale social network created and developed by Yegor Chechurin!
					    </div> 
					    <div class="box">
						    Social Nano Network is a place full of fun and joy - here you can add friends and chat with them in a real time chat room! <span class="glyphicon glyphicon-thumbs-up"></span>
					    </div> 
					</div>
			    </div>
			</div>
			<div class="col-sm-6" style="background-color: #e6ff99; height: 100%">
				<div class="half">
					<div class="wrapper"> 
						<div class="box">
							<div style="margin-bottom: 5%;">
								If you woluld like to enjoy Social Nano Network, you need to create an account:
							</div>
							<form method="post" action="registration.php">
								<div class="form-group">
									<label for="us">Username:</label>
									<input type="text" name="username" id="us">
								</div>
								<div class="form-group">
									<label for="e">Email:</label>
									<input type="text" name="email" id="e">
								</div>
								<div class="form-group">
									<label for="p">Password:</label>
									<input type="password" name="password" id="p">
								</div>
								<button type="submit" class="btn btn-primary">Sign up</button>
							</form>
						</div>
						<div class="box">
							<div style="margin-bottom: 5%;">
								Log in if you already have an account:
							</div>
							<form method="post" action="entrance.php">
								<div class="form-group">
									<label for="e">Email:</label>
									<input type="text" name="email" id="e">
								</div>
								<div class="form-group">
									<label for="p">Password:</label>
									<input type="password" name="password" id="p">
								</div>
								<button type="submit" class="btn btn-primary">Log in</button>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
<script type="text/javascript">
	function windowH() {
       var wH = $(window).height();
       $('.half').css({height: wH});
    }
    windowH();
</script>
</html>