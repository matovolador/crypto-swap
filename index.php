<?php include("_config.php");
session_start();


//ROUTING-----
$routes = new Routes();
//echo $route->getCurrentUri();
$viewFile = $routes->getView($routes->getCurrentUri());
//------------------
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Getcryptonow - Cryptocurrency Web wallet for Beginners</title>
        <!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
		<link rel="stylesheet" href="<?php echo SITE_URL ?>css/bootstrap-flatly.min.css" >
		<link rel="stylesheet" href="<?php echo SITE_URL ?>css/style.css?v=1.2.10">
		<link href="<?php echo SITE_URL?>css/video-js.css" rel="stylesheet">
		<link href="<?php echo SITE_URL?>css/sweetalert.css" rel="stylesheet">
		<script type="text/javascript">var SITE_URL = "<?php echo SITE_URL;?>"</script>
		<!--JQuery minified -->
		<script type="text/javascript" src="<?php echo SITE_URL ?>js/jquery-3.1.1.min.js"></script>
		<script type="text/javascript" src="<?php echo SITE_URL ?>js/jquery-validation-1.15.0.js"></script>
		<!-- Latest compiled and minified JavaScript -->
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
		<script type="text/javascript" src="<?php echo SITE_URL ?>js/my-js.js"></script>
		<!-- If you'd like to support IE8 -->
		<script src="<?php echo SITE_URL?>js/videojs-ie8.min.js"></script>
		<script src="<?php echo SITE_URL?>js/video.js"></script>
		<script src="<?php echo SITE_URL?>js/sweetalert.min.js"></script>
		<script src='https://www.google.com/recaptcha/api.js'></script>
		<script>
		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

		  ga('create', 'UA-104729302-1', 'auto');
		  ga('send', 'pageview');

		</script>
    </head>
    <body>
    <header>
    
    	<nav class="navbar navbar-default">
    	<div class="myLogo"><a href="<?php echo SITE_URL ?>">www.getcryptonow.com</a></div>
    	<!--
		  	<div class="container-fluid">
			    
			    <div class="navbar-header">
			      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
			        <span class="sr-only">Toggle navigation</span>
			        <span class="icon-bar"></span>
			        <span class="icon-bar"></span>
			        <span class="icon-bar"></span>
			      </button>
			      <a class="navbar-brand" href="<?php echo SITE_URL ?>">GetCryptoNow.com</a>
			    </div>

			    
			    
			    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
				    <div id="mymenu">
				      <ul class="nav navbar-nav">
				        <li><a class="mylinks" href="<?php echo SITE_URL ?>crypto-links">Crypto Links</a></li>
				      </ul>
				     </div>
				</div>
		  	</div>-->
		</nav>

    </header>
    <!--Load views in this div -->
    <div id="main-content"></div>
    <!-- -->
    <footer>
		<div class="panel panel-default">
			<div class="panel-footer">
				&copy;getcryptonow 2017 - support@getcryptonow.com
			</div>
		</div>
		
	</footer>
		<script type="text/javascript">
		function setPageIndex(index,backIndex=null){
			link = "";
			<?php
			$link = "";
			if (isset($_GET['link']))
				$link = $_GET['link'];
			?>
			link = <?php echo json_encode($link); ?>;
			$.ajax({
				url: SITE_URL+"actions/set-page-index.php",
				data: {index: index,backIndex: backIndex, link:link},
				type: "post",
				success: function(txt){
					if (link=="") {
						window.location=SITE_URL;	
					}else{
						window.location=SITE_URL+"?link="+link;
					}
					
				}
			});
		}


		var viewFile = "<?php echo $viewFile ?>";
		var mainFile = "";
		$(document).ready(function(){

			$("#main-content").load(SITE_URL+"views/"+viewFile);
		});

		function signout() {
	      	$.ajax({
	           type: "POST",
	           url: SITE_URL+'actions/logout.php',
	           data:{action:'logout'},
	           success:function(txt) {
	           		alert(txt);
					window.location.reload();
	           }

	      	});
		}
		</script>
	</body>
</html>
