<?php include("../_config.php"); 
session_start();


if (isset($_GET['ref']) && !isset($_SESSION['ref'])){
	$Refs = new Refs();
	$_SESSION['ref']=$_GET['ref'];
	$Refs->registerRef($_SESSION['ref']);
}


$Links = new Links();
if (isset($_GET['link'])){
	$Links = new Links();
	$tempLink = $Links->getLink($_GET['link']);
	if (!$tempLink) {
		session_unset();
		header("Location:". SITE_URL);
	}
	if (isset($_SESSION['link']['link']) && $_GET['link']!=$_SESSION['link']['link']){
		unset($_SESSION['script_message']);
		unset($_SESSION['script_status']);
	}
	$link = $Links->getLink($_GET['link']);
	if ($link) $_SESSION['link'] = $link;

	if ($_GET['link'] == null ) unset($_SESSION['link']);
	if (!isset($_SESSION['pin']) && $_SESSION['link']['pin']!=null) unset($_SESSION['link']);
}

if (isset($_SESSION['link'])) {
	if ($_SESSION['link']['link']=="" || $_SESSION['link']==null ) unset($_SESSION['link']);
	if (!isset($_GET['link'])) header("Location: ".SITE_URL."?link=".$_SESSION['link']['link']);
}else{
	unset($_SESSION['script_message']);
	unset($_SESSION['script_status']);
}

//ENTER PIN:
if (isset($_GET['link'])&&!isset($_SESSION['link'])){ 

	?>
<div class="panel-body">
<?php
if (isset($_SESSION['script_message'])){
	if (isset($_SESSION['script_status'])){
		echo "<div class='". $_SESSION['script_status']."'>".$_SESSION['script_message']."</div>";
	}
}


?>

<h4 class="page-title">Enter password</h4>
<p>That link is locked with a password. Please enter it below.</p>
<div class="col-sm-0 col-md-4"></div>
<form id="form" method="post" class="col-sm-12 col-md-4" action="<?php echo SITE_URL ?>actions/links-handler-m.php">
	<div class="form-group">
		<label for="pin">Password</label>
		<input type="password" class="form-control" id="pin" name="pin" placeholder="Enter password" required>
	</div>
	<input type="hidden" name="link" value="<?php echo $_GET['link']?>" required>
	<input type="hidden" name="action" value="submit-pin" required>
	<?php
	if (SITE_URL=="http://localhost/BitCoin/"){
		echo "<button type='submit' class='btn btn-primary  my-btn'>Submit</button>";	
	}else{
		echo "<button type='submit' class='btn btn-primary  my-btn g-recaptcha' data-sitekey='6LcPOikUAAAAAJICol1EaqFTeMEVZ2dnQa-OxrSS' data-callback='onSubmit'>Submit</button>";
	}
	?>
</form>
<div class="col-sm-0 col-md-4"></div>
<script type="text/javascript">
$(document).ready(function(){
	$("#form").validate();
	function onSubmit(token) {
		document.getElementById("form").submit()
   	}
});

</script>
</div>
<?php

}else{





	if (!isset($_GET['link'])&&!isset($_SESSION['link']['link'])){ ?>
		<div class="panel-body">
			<div class="row">
			<div class="col-sm-12 col-md-3"></div>
			<div class="col-sm-12 col-md-6">
				<video class="video-js" id="player"  playsinline>
					<source src="<?php echo SITE_URL?>vid/intro3.MP4" type="video/mp4">
					Your browser doesn't support HTML5.
				</video>
			</div>
			<div class="col-sm-12 col-md-3"></div>
			</div>
			<div class="ghost" id="state"></div>
			<div class="error ghost" id="alerts"></div>
			<a href="#" class="btn btn-primary  my-btn" id="link-creator" onclick="createLink(); return false;">Let's get started!</a>
		<script type="text/javascript">
		$(document).ready(function(){
			videojs("player", {
			  controls: true,
			  autoplay: false,
			  preload: 'auto',
			  aspectRatio: '16:9'
			});
			videojs("player2", {
			  controls: true,
			  autoplay: false,
			  preload: 'auto',
			  aspectRatio: '16:9'
			});

		});

		function createLink(){
			$("#alerts").hide();
			$("#link-creator").hide();
			$("#state").html("Working, please wait...");
			$("#state").show();
			$.ajax({
				url: SITE_URL+"actions/create-link.php",
				dataType: "json",
				type: "post",
				success: function(json){
					if (json['message'] != "ok"){
						$("#alerts").html(json['message']);
						$("#alerts").show();
						$("#state").hide();
						return;
					}else{
						window.location=SITE_URL+"?link="+json['link'];
						/*
						$("#link-creator").hide();
						$("#alerts").html("<a href='"+SITE_URL+"?link="+json['link']+"'>"+SITE_URL+"?link="+json['link']+"</a>");
						$("#alerts").show();
						$("#state").hide();
						*/
					}
				},
				error: function (error) {
					//alert(JSON.stringify(error));
					$("#alerts").html("There was an error creating your link. Please try again.");
					$("#alerts").show();
					$("#link-creator").show();
					$("#state").hide();
		            
		        }
			});
		}

		</script>

		</div>
		<?php
	}else{  //$_SESSION['link'] isset , and doesnt require pin to access page:

		if (!isset($_SESSION[$_GET['link']]['page_index'])) $_SESSION[$_GET['link']]['page_index'] = 1;
		?>
		<div class="panel-body">
		<div class="ghost" id="state"></div>
		<?php
		if (isset($_SESSION['script_message'])){
			if (isset($_SESSION['script_status'])){
				echo "<div class='". $_SESSION['script_status']."'>".$_SESSION['script_message']."</div>";
			}
		}
		switch ($_SESSION[$_GET['link']]['page_index']){
			case 1:
				$link = $_SESSION['link']['link'];
				?>
				<!-- The Modal -->
				<div id="myModal" class="modal">
				  <!-- Modal content -->
				  <div class="modal-content">
				 	<div class="modal-frame">
				    	<span class="close">&times;</span>
				    </div>
				    <p id="myModalContent"></p>
				  </div>
				</div>
				<div class="col-sm-0 col-md-4"></div>
				<div class=" my-text-box col-sm-12 col-md-4">
				<div class="link-holder"><?php echo SITE_URL."?link=".$_SESSION['link']['link'] ?></div>
				<div><p>This is your private unique link, only you and anyone else you give this link to can see it, please bookmark and keep it safe, if you lose this link you lose your funds.</p></div></div>
				<div class="col-sm-0 col-md-4"></div>
				<br />
				<div class="my-menu col-sm-12">
				<a href="#" class="btn btn-primary my-btn " onclick="watchIntro(); return false;"><img src="<?php echo SITE_URL?>img/Watch-Intro-Video.png" class="btn-icon" /><span class="btn-text">Watch intro video</span><span onclick="showModal('<h4>Watch intro video</h4><p>Click here to view the introduction video that initially played when you first arrived here.</p>','text');return false;" class="help-link"><img class="help-icon" src="<?php echo SITE_URL?>img/Question-Mark-Icon.png" /></span></a>
				<div class="clear-fix"></div>
				<a href="#" onclick="setPageIndex(3); return false;" class="btn btn-primary my-btn "><img src="<?php echo SITE_URL?>img/Access-Your-Cryptocurrency.png" class="btn-icon" /><span class="btn-text">Access cryptocurrency</span><span href="#" onclick="showModal('<h4>Access cryptocurrency</h4><p>Click this button to access your cryptocurrency payments, balance, deposit and exchange features.</p>','text');return false;" class="help-link"><img class="help-icon" src="<?php echo SITE_URL?>img/Question-Mark-Icon.png" /></span></a>
				<div class="clear-fix"></div>
				<a href="#" onclick="return false;" class="btn btn-primary my-btn text-smaller greyed-out"><img src="<?php echo SITE_URL?>img/Buy-Bitcoin-and-Cryptocurrency.png" class="btn-icon" /><span class="btn-text">Buy Bitcoin and cryptocurrency</span><span href="#" onclick="showModal('<h4>Buy Bitcoin and cryptocurrency</h4><p>Over time we will start adding here various methods for you to buy various cryptocurrencies, for now the best solution is to email us on support@gertcryptonow.com</p>','text');return false;" class="help-link"><img class="help-icon" src="<?php echo SITE_URL?>img/Question-Mark-Icon.png" /></span></a>
				<div class="clear-fix"></div>
				<a href="#" class="btn btn-primary my-btn" onclick="setPageIndex(18); return false;"><img src="<?php echo SITE_URL?>img/Glossary.png" class="btn-icon" /><span class="btn-text">Glossary</span><span href="#" onclick="showModal('<h4>Glossary</h4><p>Simple, clear, friendly glossary of bitcoin and cryptocurrency terminology coming soon.</p>','text');return false;" class="help-link"><img class="help-icon" src="<?php echo SITE_URL?>img/Question-Mark-Icon.png" /></span></a>
				<div class="clear-fix"></div>
				<a href="#" onclick="setPageIndex(10); return false" class="btn btn-primary my-btn  text-smaller"><img src="<?php echo SITE_URL?>img/Make-Money-Promoting-This-App.png" class="btn-icon" /><span class="btn-text">Make money promoting this app</span><span href="#" onclick="showModal('<h4>Make money promoting this app</h4><p>Soon we will add detailed instructions explaining how you can make money promoting this app and it’s message.</p>','text');return false;" class="help-link"><img class="help-icon" src="<?php echo SITE_URL?>img/Question-Mark-Icon.png" /></span></a>
				<div class="clear-fix"></div>
				<a href="#" class="btn btn-primary my-btn " onclick="setPageIndex(11);"><img src="<?php echo SITE_URL?>img/Extra-Features.png" class="btn-icon" /><span class="btn-text">Extra features</span><span href="#" onclick="showModal('<h4>Extra features</h4><p>We’ve put all extra features here for now to keep the main menus clean and simple.</p>','text');return false;" class="help-link"><img class="help-icon" src="<?php echo SITE_URL?>img/Question-Mark-Icon.png" /></span></a>
				<div class="clear-fix"></div>
				</div>
				<?php
				break;
			case 2:
				?>
				<!-- The Modal -->
				<div id="myModal" class="modal">
				  <!-- Modal content -->
				  <div class="modal-content">
				 	<div class="modal-frame">
				    	<span class="close">&times;</span>
				    </div>
				    <p id="myModalContent"></p>
				  </div>
				</div>
				<h4 class="page-title">Password lock link</h4>
				<div class="col-sm-0 col-md-4"></div>
				<form id="form" method="post" class="col-sm-12 col-md-4" action="<?php echo SITE_URL ?>actions/links-handler-m.php">
					<div class="form-group">
						<label for="pin">Password</label>
						<input type="password" class="form-control" id="pin" name="pin" placeholder="Enter password" required>
					</div>
					<div class="form-group">
						<label for="pin-repeat">Repeat password</label>
						<input type="password" class="form-control" id="pin-repeat" name="pin-repeat" placeholder="Enter password again" required>
					</div>
					<input type="hidden" name="action" value="lock" required>
					<button type="submit" class="btn btn-primary my-btn">Submit</button>
				</form>
				<div class="col-sm-0 col-md-4"></div>
				<br />
				<div class="my-menu col-sm-12">
				<a href="#" onclick="setPageIndex(1); return false;" class="btn btn-warning my-btn">Back</a> 
				</div>
				<script type="text/javascript">
					$("#form").validate();
				</script>
				<?php 
				break;
			case 3:
				?>
				<!-- The Modal -->
				<div id="myModal" class="modal">
				  <!-- Modal content -->
				  <div class="modal-content">
				 	<div class="modal-frame">
				    	<span class="close">&times;</span>
				    </div>
				    <p id="myModalContent"></p>
				  </div>
				</div>
				<div class="col-sm-0 col-md-4"></div>
				<div class=" my-text-box col-sm-12 col-md-4">
				<div class="link-holder"><?php echo $_SESSION['link']['assigned_address'] ?></div>
				<p>This is your Litecoin deposit address. Send Litecoin to this address to add credit or ask someone who knows to do it for you.</p>
				</div>
				<div class="col-sm-0 col-md-4"></div>
				<br />
				<div class="col-sm-12">
				<div class="btn btn-primary balance my-btn"><img src="<?php echo SITE_URL?>img/Litecoin-Icon.png" class="btn-icon" /><span class="btn-text" id="balanceLTC"></span><span href="#" onclick="showModal('<p>Your Litecoin balance (cleared and pending) is shown here, we\'ve used Litcoin because the network is fast and cheap compared with Bitcoin and functions almost identically to Bitcoin\'s network</p>','text');return false;" class="help-link"><img class="help-icon" src="<?php echo SITE_URL?>img/Question-Mark-Icon.png" /></span></div>
				<div class="clear-fix"></div>
				<div class="btn btn-primary balance my-btn"><img src="<?php echo SITE_URL?>img/USD-Dollar-Sign.png" class="btn-icon" /><span class="btn-text" id="balanceUSD"></span><span href="#" onclick="showModal('<p>Your $ balance is show here, there is a delay changing and spending $ via getcryptonow.com because we rely on a third party exchange for these $ exchange</p>','text');return false;" class="help-link"><img class="help-icon" src="<?php echo SITE_URL?>img/Question-Mark-Icon.png" /></span></div>
				<div class="clear-fix"></div>
				<a href='#' onclick='setPageIndex(8);return false;' class='btn btn-primary my-btn'><img src="<?php echo SITE_URL?>img/Convert-to-USD.png" class="btn-icon" /><span class="btn-text">Convert to USD</span><span href="#" onclick="showModal('<h4>Convert to USD</h4><p>Converts a spcified amount to $USD, via a thrid party exchange service that getcryptonow.com connects to</p>','text');return false;" class="help-link"><img class="help-icon" src="<?php echo SITE_URL?>img/Question-Mark-Icon.png" /></span></a>
				<div class="clear-fix"></div>
				<a href="#" onclick="setPageIndex(4); return false;" class="btn btn-primary my-btn"><img src="<?php echo SITE_URL?>img/Spend-some-crypto.png" class="btn-icon" /><span class="btn-text">Spend some crypto</span><span href="#" onclick="showModal('<h4>Spend some crypto</h4><p>Go to the spending area to spend your cryptocurrency</p>','text');return false;" class="help-link"><img class="help-icon" src="<?php echo SITE_URL?>img/Question-Mark-Icon.png" /></span></a>
				<div class="clear-fix"></div>	
				<a href="#" onclick="setPageIndex(9,3);" class="btn btn-primary my-btn"><img src="<?php echo SITE_URL?>img/Transaction-Log-Icon.png" class="btn-icon" /><span class="btn-text">Transaction Log</span><span href="#" onclick="showModal('<h4>Transaction Log</h4><p>This is a log of all your transactions, this is a work in progress, we intend to make it super freindly and understandable in time</p>','text');return false;" class="help-link"><img class="help-icon" src="<?php echo SITE_URL?>img/Question-Mark-Icon.png" /></span></a>
				<div class="clear-fix"></div>
				<a href="#" onclick="setPageIndex(1); return false;" class="btn btn-warning my-btn">Back</a>
				<div class="clear-fix"></div>
				</div>
				<?php
				break;
			case 4:
				?>
				<!-- The Modal -->
				<div id="myModal" class="modal">
				  <!-- Modal content -->
				  <div class="modal-content">
				 	<div class="modal-frame">
				    	<span class="close">&times;</span>
				    </div>
				    <p id="myModalContent"></p>
				  </div>
				</div>
				<h4 class="page-title">Get spending!</h4>
				<div class="my-menu col-sm-12">
				<div class="btn btn-primary balance my-btn"><img src="<?php echo SITE_URL?>img/Litecoin-Icon.png" class="btn-icon" /><span class="btn-text" id="balanceLTC"></span><span href="#" onclick="showModal('<p>Your Litecoin balance (cleared and pending) is shown here, we\'ve used Litcoin because the network is fast and cheap compared with Bitcoin and functions almost identically to Bitcoin\'s network</p>','text');return false;" class="help-link"><img class="help-icon" src="<?php echo SITE_URL?>img/Question-Mark-Icon.png" /></span></div>
				<div class="clear-fix"></div>
				<div class="btn btn-primary balance my-btn"><img src="<?php echo SITE_URL?>img/USD-Dollar-Sign.png" class="btn-icon " /><span class="btn-text" id="balanceUSD"></span><span href="#" onclick="showModal('<p>Your $ balance is show here, there is a delay changing and spending $ via getcryptonow.com because we rely on a third party exchange for these $ exchange</p>','text');return false;" class="help-link"><img class="help-icon" src="<?php echo SITE_URL?>img/Question-Mark-Icon.png" /></span></div>
				<div class="clear-fix"></div>
				<a href="#" onclick="setPageIndex(5); return false;" class="btn btn-primary my-btn"><img class="btn-icon" src="<?php echo SITE_URL?>img/Litecoin-Icon.png"><span class="btn-text">Spend Litecoin</span><span href="#" onclick="showModal('<h4>Spend Litecoin</h4><p>Send (Spend) a specified amount of Litcoin by sending it to a specified Litecoin address</p>','text');return false;" class="help-link"><img class="help-icon" src="<?php echo SITE_URL?>img/Question-Mark-Icon.png" /></span></a>
				<div class="clear-fix"></div>
				<a href="#" onclick="setPageIndex(6); return false;" class="btn btn-primary my-btn"><img class="btn-icon" src="<?php echo SITE_URL?>img/Access-Your-Cryptocurrency.png"><span class="btn-text">Spend Bitcoin</span><span href="#" onclick="showModal('<h4>Spend Bitcoin</h4><p>Send (Spend) a specified amount of Bitcoin by sending it to a specified Bitcoin address</p>','text');return false;" class="help-link"><img class="help-icon" src="<?php echo SITE_URL?>img/Question-Mark-Icon.png" /></span></a>
				<div class="clear-fix"></div>
				<a href="#" onclick="setPageIndex(7); return false;" class="btn btn-primary my-btn"><img class="btn-icon" src="<?php echo SITE_URL?>img/Ethereum-icon.png"><span class="btn-text">Spend Ethereum</span><span href="#" onclick="showModal('<h4>Spend Ethereum</h4><p>Send (Spend) a specified amount of Ethereum by sending it to a specified Ethereum address</p>','text');return false;" class="help-link"><img class="help-icon" src="<?php echo SITE_URL?>img/Question-Mark-Icon.png" /></span></a>
				<div class="clear-fix"></div>
				<a href="#" onclick="setPageIndex(9,4);" class="btn btn-primary my-btn"><img src="<?php echo SITE_URL?>img/Transaction-Log-Icon.png" class="btn-icon" /><span class="btn-text">Transaction Log</span><span href="#" onclick="showModal('<h4>Transaction Log</h4><p>This is a log of all your transactions, this is a work in progress, we intend to make it super freindly and understandable in time</p>','text');return false;" class="help-link"><img class="help-icon" src="<?php echo SITE_URL?>img/Question-Mark-Icon.png" /></span></a>
				<div class="clear-fix"></div> 
				<a href="#" onclick="setPageIndex(3); return false;" class="btn btn-warning my-btn">Back</a><div class="clear-fix"></div>
				</div>
				<?php
				break;		
			case 5:
				?>
				<!-- The Modal -->
				<div id="myModal" class="modal">
				  <!-- Modal content -->
				  <div class="modal-content">
				 	<div class="modal-frame">
				    	<span class="close">&times;</span>
				    </div>
				    <p id="myModalContent"></p>
				  </div>
				</div>
				<h4 class="page-title">Spend Litecoin</h4>
				<div class="my-menu col-sm-12">
				<div class="btn btn-primary balance my-btn"><img src="<?php echo SITE_URL?>img/Litecoin-Icon.png" class="btn-icon" /><span class="btn-text" id="balanceLTC"></span><span href="#" onclick="showModal('<p>Your Litecoin balance (cleared and pending) is shown here, we\'ve used Litcoin because the network is fast and cheap compared with Bitcoin and functions almost identically to Bitcoin\'s network</p>','text');return false;" class="help-link"><img class="help-icon" src="<?php echo SITE_URL?>img/Question-Mark-Icon.png" /></span></div>
				<div class="clear-fix"></div>
				<div class="btn btn-primary balance my-btn"><img src="<?php echo SITE_URL?>img/USD-Dollar-Sign.png" class="btn-icon" /><span class="btn-text" id="balanceUSD"></span><span href="#" onclick="showModal('<p>Your $ balance is show here, there is a delay changing and spending $ via getcryptonow.com because we rely on a third party exchange for these $ exchange</p>','text');return false;" class="help-link"><img class="help-icon" src="<?php echo SITE_URL?>img/Question-Mark-Icon.png" /></span></div>
				<div class="clear-fix"></div>
				</div>
				<div class="col-sm-0 col-md-4"></div>
				<form id="form" method="post" class="col-sm-12 col-md-4" action="<?php echo SITE_URL ?>actions/links-handler-m.php">
					<div class="form-group">
						<label for="pin">Password</label>
						<input type="password" class="form-control" id="pin" name="pin" placeholder="Enter password">
					</div>
					<div class="form-group">
						<label for="address">Address</label>
						<input type="text" class="form-control" id="address" name="address" placeholder="Enter a Litecoin address" required>
					</div>
					<div class="form-group">
						<label for="amount">Amount</label>
						<input type="text" class="form-control" id="amount" name="amount" onkeyup="recalculateUSD();" placeholder="Enter the amount to spend" required>
					</div>
					<div class="ghost" id="cryptoToUSD"></div>
					<input type="hidden" name="action" value="cashout" required>
					<input type="hidden" name="USD" id="USD" value="0" required>
					<input type="hidden" id ="currency" name="currency" value="LTC" required>
					<button type="submit" class="btn btn-primary  my-btn">Spend</button>
				</form>
				<div class="col-sm-0 col-md-4"></div>
				<div class="my-menu col-sm-12">
					<a href="#" onclick="setPageIndex(4); return false;" class="btn btn-warning my-btn">Back</a> 
				</div>
				<script type="text/javascript">
				$(document).ready(function(){
					$("#form").validate();
					calculateSpendToUSD();
					recalculateUSD();
				});
				function recalculateUSD(){
					amount = $("#USD").val() * $("#amount").val();
					amount = amount.toFixed(2);
					$("#cryptoToUSD").show();
					$("#cryptoToUSD").html("~$"+amount);
					return;
				}
				function calculateSpendToUSD(){
					symbol = null;
					amount = "1";
					if ($("#currency").val()=="LTC") symbol = "ltcusd";
					if ($("#currency").val()=="BTC") symbol = "btcusd";
					if ($("#currency").val()=="ETH") symbol = "ethusd";
					$.ajax({
						url : SITE_URL+"actions/links-handler-m.php",
						data: {amount:amount,symbol: symbol, action:"get-symbol"},
						type: "post",
						success: function(txt){
							$("#USD").val(txt);
						},
						error: function (xhr, ajaxOptions, thrownError) {
					    	//alert(xhr.status+ " "+xhr.responseText+" "+thrownError);
					    }
					});
				}
				</script>

				<?php
				break;
			case 6:
				?>
				<!-- The Modal -->
				<div id="myModal" class="modal">
				  <!-- Modal content -->
				  <div class="modal-content">
				 	<div class="modal-frame">
				    	<span class="close">&times;</span>
				    </div>
				    <p id="myModalContent"></p>
				  </div>
				</div>
				<h4 class="page-title">Spend Bitcoin</h4>
				<div class="my-menu col-sm-12">
				<div class="btn btn-primary balance my-btn"><img src="<?php echo SITE_URL?>img/Litecoin-Icon.png" class="btn-icon" /><span class="btn-text" id="balanceLTC"></span><span href="#" onclick="showModal('<p>Your Litecoin balance (cleared and pending) is shown here, we\'ve used Litcoin because the network is fast and cheap compared with Bitcoin and functions almost identically to Bitcoin\'s network</p>','text');return false;" class="help-link"><img class="help-icon" src="<?php echo SITE_URL?>img/Question-Mark-Icon.png" /></span></div>
				<div class="clear-fix"></div>
				<div class="btn btn-primary balance my-btn"><img src="<?php echo SITE_URL?>img/USD-Dollar-Sign.png" class="btn-icon" /><span class="btn-text" id="balanceUSD"></span><span href="#" onclick="showModal('<p>Your $ balance is show here, there is a delay changing and spending $ via getcryptonow.com because we rely on a third party exchange for these $ exchange</p>','text');return false;" class="help-link"><img class="help-icon" src="<?php echo SITE_URL?>img/Question-Mark-Icon.png" /></span></div>
				<div class="clear-fix"></div>
				</div>
				<div class="col-sm-0 col-md-4"></div>
				<form id="form" method="post" class="col-sm-12 col-md-4" action="<?php echo SITE_URL ?>actions/links-handler-m.php">
					<div class="form-group">
						<label for="pin">Password</label>
						<input type="password" class="form-control" id="pin" name="pin" placeholder="Enter password">
					</div>
					<div class="form-group">
						<label for="address">Address</label>
						<input type="text" class="form-control" id="address" name="address" placeholder="Enter a Bitcoin address" required>
					</div>
					<div class="form-group">
						<label for="amount">Amount</label>
						<input type="text" class="form-control" id="amount" name="amount" onkeyup="recalculateUSD();" placeholder="Enter the amount to spend" required>
					</div>
					<div class="ghost" id="cryptoToUSD"></div>
					<input type="hidden" name="action" value="cashout" required>
					<input type="hidden" name="USD" id="USD" value="0" required>
					<input type="hidden" id ="currency" name="currency" value="BTC" required>
					<button type="submit" class="btn btn-primary  my-btn">Spend</button>
				</form>
				<div class="col-sm-0 col-md-4"></div>
				<div class="my-menu col-sm-12">
					<a href="#" onclick="setPageIndex(4); return false;" class="btn btn-warning my-btn">Back</a> 
				</div>
				<script type="text/javascript">
				$(document).ready(function(){
					$("#form").validate();
					calculateSpendToUSD();
					recalculateUSD();
				});
				function recalculateUSD(){
					amount = $("#USD").val() * $("#amount").val();
					amount = amount.toFixed(2);
					$("#cryptoToUSD").show();
					$("#cryptoToUSD").html("~$"+amount);
					return;
				}
				function calculateSpendToUSD(){
					symbol = null;
					amount = "1";
					if ($("#currency").val()=="LTC") symbol = "ltcusd";
					if ($("#currency").val()=="BTC") symbol = "btcusd";
					if ($("#currency").val()=="ETH") symbol = "ethusd";
					$.ajax({
						url : SITE_URL+"actions/links-handler-m.php",
						data: {amount:amount,symbol: symbol, action:"get-symbol"},
						type: "post",
						success: function(txt){
							$("#USD").val(txt);
						},
						error: function (xhr, ajaxOptions, thrownError) {
					    	//alert(xhr.status+ " "+xhr.responseText+" "+thrownError);
					    }
					});
				}
				</script>

				<?php
				break;
			case 7:
				?>
				<!-- The Modal -->
				<div id="myModal" class="modal">
				  <!-- Modal content -->
				  <div class="modal-content">
				 	<div class="modal-frame">
				    	<span class="close">&times;</span>
				    </div>
				    <p id="myModalContent"></p>
				  </div>
				</div>
				<h4 class="page-title">Spend Ethereum</h4>
				<div class="my-menu col-sm-12">
				<div class="btn btn-primary balance my-btn"><img src="<?php echo SITE_URL?>img/Litecoin-Icon.png" class="btn-icon" /><span class="btn-text" id="balanceLTC"></span><span href="#" onclick="showModal('<p>Your Litecoin balance (cleared and pending) is shown here, we\'ve used Litcoin because the network is fast and cheap compared with Bitcoin and functions almost identically to Bitcoin\'s network</p>','text');return false;" class="help-link"><img class="help-icon" src="<?php echo SITE_URL?>img/Question-Mark-Icon.png" /></span></div>
				<div class="clear-fix"></div>
				<div class="btn btn-primary balance my-btn"><img src="<?php echo SITE_URL?>img/USD-Dollar-Sign.png" class="btn-icon " /><span class="btn-text" id="balanceUSD"></span><span href="#" onclick="showModal('<p>Your $ balance is show here, there is a delay changing and spending $ via getcryptonow.com because we rely on a third party exchange for these $ exchange</p>','text');return false;" class="help-link"><img class="help-icon" src="<?php echo SITE_URL?>img/Question-Mark-Icon.png" /></span></div>
				<div class="clear-fix"></div>
				</div>
				<div class="row">
				<div class="col-sm-0 col-md-4"></div>
				<div class="col-sm-12 col-md-4">
				<div>Keep in mind that when sending Ethereum, the exchange charges a fee of ETH 0.01. This will translate into costs for the recipient of the Ethereum.</div>
				</div>
				</div>
				<div class="col-sm-0 col-md-4"></div>
				<form id="form" method="post" class="col-sm-12 col-md-4" action="<?php echo SITE_URL ?>actions/links-handler-m.php">
					<div class="form-group">
						<label for="pin">Password</label>
						<input type="password" class="form-control" id="pin" name="pin" placeholder="Enter password">
					</div>
					<div class="form-group">
						<label for="address">Address</label>
						<input type="text" class="form-control" id="address" name="address" placeholder="Enter an Etherum address" required>
					</div>
					<div class="form-group">
						<label for="amount">Amount</label>
						<input type="text" class="form-control" id="amount" name="amount" onkeyup="recalculateUSD();" placeholder="Enter the amount to spend" required>
					</div>
					<div class="ghost" id="cryptoToUSD"></div>
					<input type="hidden" name="action" value="cashout" required>
					<input type="hidden" name="USD" id="USD" value="0" required>
					<input type="hidden" id ="currency" name="currency" value="ETH" required>
					<button type="submit" class="btn btn-primary  my-btn">Spend</button>
				</form>
				<div class="col-sm-0 col-md-4"></div>
				<div class="my-menu col-sm-12">
					<a href="#" onclick="setPageIndex(4); return false;" class="btn btn-warning my-btn">Back</a> 
				</div>
				<script type="text/javascript">
				$(document).ready(function(){
					$("#form").validate();
					calculateSpendToUSD();
					recalculateUSD();
				});
				function recalculateUSD(){
					amount = $("#USD").val() * $("#amount").val();
					amount = amount.toFixed(2);
					$("#cryptoToUSD").show();
					$("#cryptoToUSD").html("~$"+amount);
					return;
				}
				function calculateSpendToUSD(){
					symbol = null;
					amount = "1";
					if ($("#currency").val()=="LTC") symbol = "ltcusd";
					if ($("#currency").val()=="BTC") symbol = "btcusd";
					if ($("#currency").val()=="ETH") symbol = "ethusd";
					$.ajax({
						url : SITE_URL+"actions/links-handler-m.php",
						data: {amount:amount,symbol: symbol, action:"get-symbol"},
						type: "post",
						success: function(txt){
							$("#USD").val(txt);
						},
						error: function (xhr, ajaxOptions, thrownError) {
					    	//alert(xhr.status+ " "+xhr.responseText+" "+thrownError);
					    }
					});
				}
				</script>

				<?php
				break;
			case 8:
				?>
				<!-- The Modal -->
				<div id="myModal" class="modal">
				  <!-- Modal content -->
				  <div class="modal-content">
				 	<div class="modal-frame">
				    	<span class="close">&times;</span>
				    </div>
				    <p id="myModalContent"></p>
				  </div>
				</div>
				<h4 class="page-title">Convert to USD</h4>
				<div class="my-menu col-sm-12">
				<div class="btn btn-primary balance my-btn"><img src="<?php echo SITE_URL?>img/Litecoin-Icon.png" class="btn-icon" /><span class="btn-text" id="balanceLTC"></span><span href="#" onclick="showModal('<p>Your Litecoin balance (cleared and pending) is shown here, we\'ve used Litcoin because the network is fast and cheap compared with Bitcoin and functions almost identically to Bitcoin\'s network</p>','text');return false;" class="help-link"><img class="help-icon" src="<?php echo SITE_URL?>img/Question-Mark-Icon.png" /></span></div>
				<div class="clear-fix"></div>
				<div class="btn btn-primary balance my-btn"><img src="<?php echo SITE_URL?>img/USD-Dollar-Sign.png" class="btn-icon" /><span class="btn-text" id="balanceUSD"></span><span href="#" onclick="showModal('<p>Your $ balance is show here, there is a delay changing and spending $ via getcryptonow.com because we rely on a third party exchange for these $ exchange</p>','text');return false;" class="help-link"><img class="help-icon" src="<?php echo SITE_URL?>img/Question-Mark-Icon.png" /></span></div>
				<div class="clear-fix"></div>
				</div>
				<div class="col-sm-0 col-md-4"></div>
				<div class="col-sm-12 col-md-4">
				<div>The cost of converting into USD is %1 of total amount of LTC for a minimum of LTC 0.004 cost, plus LTC 0.001 to make the transaction to the exchange, for any amount chosen.</div>
					<div class="form-group">
						<label for="amount">Amount of LTC</label>
						<input type="text" class="form-control" id="amount" name="amount" onkeyup="recalculateUSD();" placeholder="Enter the amount to convert" required>
					</div>
					<div class="ghost" id="cryptoToUSD"></div>
					<input type="hidden" name="USD" id="USD" value="0" required>
					<input type="hidden" id ="currency" name="currency" value="LTC" required>
					<a class="btn btn-primary  my-btn" onclick="convertToUSD(); return false;">Convert to USD</a>
				</div>
				<div class="col-sm-0 col-md-4"></div>
				<div class="my-menu col-sm-12">
					<a href="#" onclick="setPageIndex(3); return false;" class="btn btn-warning my-btn">Back</a> 
				</div>
				<script type="text/javascript">
				$(document).ready(function(){
					$("#form").validate();
					calculateSpendToUSD();
					recalculateUSD();
				});
				function recalculateUSD(){
					amount = $("#USD").val() * $("#amount").val();
					amount = amount.toFixed(2);
					$("#cryptoToUSD").show();
					$("#cryptoToUSD").html("~$"+amount);
					return;
				}
				function calculateSpendToUSD(){
					symbol = null;
					amount = "1";
					if ($("#currency").val()=="LTC") symbol = "ltcusd";
					if ($("#currency").val()=="BTC") symbol = "btcusd";
					if ($("#currency").val()=="ETH") symbol = "ethusd";
					$.ajax({
						url : SITE_URL+"actions/links-handler-m.php",
						data: {amount:amount,symbol: symbol, action:"get-symbol"},
						type: "post",
						success: function(txt){
							$("#USD").val(txt);
						},
						error: function (xhr, ajaxOptions, thrownError) {
					    	//alert(xhr.status+ " "+xhr.responseText+" "+thrownError);
					    }
					});
				}
				</script>

				<?php
				break;
			case 9:
				?>
				<!-- The Modal -->
				<div id="myModal" class="modal">
				  <!-- Modal content -->
				  <div class="modal-content">
				 	<div class="modal-frame">
				    	<span class="close">&times;</span>
				    </div>
				    <p id="myModalContent"></p>
				  </div>
				</div>
				<h4 class="page-title">Transaction Log</h4>
				<div class="my-menu col-sm-12">
				<div id="date"></div>
				<?php 
				$Balances = new Balances();
				$balances = $Balances->getByLink($_SESSION['link']['link']);
				if (!empty($balances)){
					$balances = array_reverse($balances);
					echo "<h4>Transactions</h4>";
					echo "<div class='table-wrapper col-sm-12'>";
					echo "<table class='table table-striped myTable'>";
					echo "<thead>";
					echo "<tr>";
					//set headers:
					foreach ($balances[0] as $key => $value){

						if ($key!="id" && $key!="link_id" && $key!="description" && $key!="in_exchange" &&$key!="tx" && $key!="disposed"){

							$th = $key;
							if ($key=="concept_flag")	$th = "description";
							if ($key=="tx") $th = "transaction ID";
							echo "<th>".$th."</th>";	
						}
						
					}
					echo "</tr>";
					echo "</thead>";
					echo "<tbody>";
					for ($i=0;$i<count($balances);$i++){
						echo "<tr>";
						foreach($balances[$i] as $key=>$value){
							if ($key!="id" && $key!="link_id" && $key!="description" && $key!="in_exchange" &&$key!="tx" && $key!="disposed"){
								$td = $value;
								if ($key=="concept_flag") $td = $Balances::CONCEPT_ARRAY[$value];
								if ($value == "-1" && $key="tx") $td = "";
								echo "<td>".$td."</td>";
							}
						}
						echo "</tr>";
					}
					echo "</tbody>";
					echo "</table>";
					echo "</div>";




				}else{
					echo "<div>No transactions were made yet.</div>";
				}


				$Orders = new Orders();
				$orders = $Orders->getByLink($_SESSION['link']['link'],false);
				if (!empty($orders)){
					$orders = array_reverse($orders);
					echo "<h4>Pending orders</h4>";
					echo "<div class='table-wrapper col-sm-12'>";
					echo "<table class='table table-striped myTable'>";
					echo "<thead>";
					echo "<tr>";
					//set headers:
					foreach ($orders[0] as $key => $value){

						if ($key!="id" && $key!="link_id" && $key!="order_id" && $key!="disposed" ){
							$th = $key;
							if ($key=="price") $th = "price USD";
							echo "<th>".$th."</th>";	
						}
						
					}
					echo "</tr>";
					echo "</thead>";
					echo "<tbody>";
					for ($i=0;$i<count($orders);$i++){
						echo "<tr>";
						foreach($orders[$i] as $key=>$value){
							if ($key!="id" && $key!="link_id" && $key!="order_id" && $key!="disposed" ){
								$td = $value;
								echo "<td>".$td."</td>";
							}
						}
						echo "</tr>";
					}
					echo "</tbody>";
					echo "</table>";
					echo "</div>";




				}else{
				}


				if (!isset($_SESSION[$_GET['link']]['backIndex'])) $_SESSION[$_GET['link']]['backIndex'] = 1;
				?>
				<a href="#" onclick="setPageIndex(9,<?php echo json_encode($_SESSION[$_GET['link']]['backIndex'])?>); return false;" class="btn btn-primary my-btn">Refresh</a><div class="clear-fix"></div>
				<a href="#" onclick="setPageIndex(<?php echo json_encode($_SESSION[$_GET['link']]['backIndex'])?>); return false;" class="btn btn-warning my-btn">Back</a><div class="clear-fix"></div>
				</div>
				<script type="text/javascript">
				$(document).ready(function(){
					var dateInterval = setInterval(getDate,500);

				});
				function getDate(){
					$.ajax({
						url: SITE_URL+"actions/get-date.php",
						type: "post",
						success: function(txt){
							$("#date").html(txt);
						},
						error: function (xhr, ajaxOptions, thrownError) {
				        	//alert(xhr.status+ " "+xhr.responseText+" "+thrownError);
				      	}
					});
				}
				</script>
				<?php
				break;
			case 10:
				?>
				<!-- The Modal -->
				<div id="myModal" class="modal">
				  <!-- Modal content -->
				  <div class="modal-content">
				 	<div class="modal-frame">
				    	<span class="close">&times;</span>
				    </div>
				    <p id="myModalContent"></p>
				  </div>
				</div>
				<h4 class="page-title">Make money promoting this app</h4>
				<div class="col-sm-0 col-md-4"></div>
				<div class="col-sm-12 col-md-4 my-text-box"><p>When creating a sub-link from this menu, you will be able to collect half the fees that are collected from getcryptonow.com system, through trades into USD made by the sub-links you create here, into the LTC account you have with your own link.</p>
				<p>Is up to you, then, to distribute the sub-links you create.</p></div>
				<div class="col-sm-0 col-md-4"></div>
				<div class="my-menu col-sm-12">
				<div id="ref-link"></div>
				<div class="clear-fix"></div>
				<a href="#" onclick="createLinkFromRef(); return false;" class="btn btn-primary my-btn">Create sub-link</a>
				<div class="clear-fix"></div>
				<a href="#" onclick="setPageIndex(1); return false;" class="btn btn-warning my-btn">Back</a> 
				</div>
				<script type="text/javascript">
					function createLinkFromRef(){
						$("#ref-link").html("Working, please wait...");
						refLink = ""
						<?php 
						$link = "";
						if (isset($_SESSION['link'])) $link = $_SESSION['link']['link']; ?>
						
						refLink = <?php echo json_encode($link); ?>;	
						$.ajax({
							url: SITE_URL+"actions/create-link.php",
							dataType: "json",
							data: {refLink: refLink },
							type: "post",
							success: function(json){
								if (json['message'] != "ok"){
									
									$("#ref-link").html('<div class="error">'+json['message']+'</div>');
									return;
								}else{
									$("#ref-link").html('<div class="success">'+SITE_URL+"?link="+json['link']['link']+'</div>');
									return;
								}
							},
							error: function (error) {
								//alert(JSON.stringify(error));
								$("#ref-link").html("<div class='error'>There was an error creating your link. Please try again.</div>");
					            
					        }
						});
					}




				</script>
				<?php
				break;
			case 11:
				$link = $_SESSION['link']['link'];
				?>
				<!-- The Modal -->
				<div id="myModal" class="modal">
				  <!-- Modal content -->
				  <div class="modal-content">
				 	<div class="modal-frame">
				    	<span class="close">&times;</span>
				    </div>
				    <p id="myModalContent"></p>
				  </div>
				</div>
				<h4 class="page-title">Extra features</h4>
				<div class="my-menu col-sm-12">
				<a href="#" onclick="setPageIndex(2); return false;" class="btn btn-primary my-btn "><img src="<?php echo SITE_URL?>img/Password-lock-my-link.png" class="btn-icon" /><span class="btn-text">Password lock my link</span><span href="#" onclick="showModal('<video class=\'video-js\' id=\'player\'  playsinline><source src=\'<?php echo SITE_URL?>vid/passwordlocklink.MP4\' type=\'video/mp4\'>Your browser doesn\'t support HTML5.</video>','video');return false;" class="help-link"><img class="help-icon" src="<?php echo SITE_URL?>img/Question-Mark-Icon.png" /></span></a>
				<div class="clear-fix"></div>
				<a href="#" onclick="copyLink('<?php echo $link ?>');return false;" class="btn btn-primary my-btn "><img src="<?php echo SITE_URL?>img/Copy-Button.png" class="btn-icon" /><span class="btn-text">Copy link</span><span href="#" onclick="showModal('<video class=\'video-js\' id=\'player\'  playsinline><source src=\'<?php echo SITE_URL?>vid/copylink.MP4\' type=\'video/mp4\'>Your browser doesn\'t support HTML5.</video>','video');return false;" class="help-link"><img class="help-icon" src="<?php echo SITE_URL?>img/Question-Mark-Icon.png" /></span></a>
				<div class="clear-fix"></div>
				<a href="#" onclick="removeLinkInSession(); return false;" class="btn btn-danger my-btn"><img src="<?php echo SITE_URL?>img/Create-Fresh-Link.png" class="btn-icon" /><span class="btn-text">Create fresh link</span><span href="#" onclick="showModal('<h4>Create fresh link</h4><p>Caution - you can create a fresh unique getcryptonow.com link here but make sure you\'ve saved your existing links first</p>','text');return false;" class="help-link"><img class="help-icon" src="<?php echo SITE_URL?>img/Question-Mark-Icon.png" /></span></a>
				<div class="clear-fix"></div>
				<a href="#" onclick="setPageIndex(1); return false;" class="btn btn-warning my-btn">Back</a><div class="clear-fix"></div>
				</div>
				<?php
				break;
			case 12:
				require("blog.php");
				break;
			case 13:
				require("blog_login.php");
				break;
			case 14:
				require("blog_register.php");
				break;
			case 15:
				require("dashboard.php");
				break;
			case 16:
				require("edit_article.php");
				break;
			case 17:
				?>
				<!-- The Modal -->
				<div id="myModal" class="modal">
				  <!-- Modal content -->
				  <div class="modal-content">
				 	<div class="modal-frame">
				    	<span class="close">&times;</span>
				    </div>
				    <p id="myModalContent"></p>
				  </div>
				</div>
				<h4 class="page-title">Glossary</h4>
				<div class="my-menu col-sm-12">
				<a href="#" class="btn btn-primary my-btn " onclick="setPageIndex(18,17); return false;"><span class="btn-text">Basic terminology</span></a>
				<div class="clear-fix"></div>
				<a href="#" class="btn btn-primary my-btn " onclick="setPageIndex(19,17); return false;"><span class="btn-text">More advanced terms</span></a>
				<div class="clear-fix"></div>
				<a href="#" onclick="setPageIndex(1); return false;" class="btn btn-warning my-btn">Back</a> 
				</div>
				<?php
				break;
			case 18:
				?>
				<!-- The Modal -->
				<div id="myModal" class="modal">
				  <!-- Modal content -->
				  <div class="modal-content">
				 	<div class="modal-frame">
				    	<span class="close">&times;</span>
				    </div>
				    <p id="myModalContent"></p>
				  </div>
				</div>
				<h4 class="page-title">Glossary</h4>
				<div class="my-menu col-sm-12 center-text">
				<a href="#" class="btn btn-primary my-btn title-btn " onclick="return false;"><span class="btn-text">Cryptocurrency</span></a>
				<div class="term-text">a digital currency in which encryption techniques are used to regulate the generation of units of currency and verify the transfer of funds, operating independently of a central bank.</div>
				<a href="#" class="btn btn-primary my-btn title-btn " onclick="return false;"><span class="btn-text">Blockchain</span></a>
				<div class="term-text">a digital ledger in which transactions made in bitcoin or another cryptocurrency are recorded chronologically and publicly.</div>
				<div class="clear-fix"></div>
				<a href="#" class="btn btn-primary my-btn title-btn " onclick="return false;"><span class="btn-text">Decentralize</span></a>
				<div class="term-text">the process of redistributing or dispersing functions, powers, people or things away from a central location or authority.</div>
				<div class="clear-fix"></div>
				<a href="#" class="btn btn-primary my-btn title-btn " onclick="return false;"><span class="btn-text">Fluctuation (price)</span></a>
				<div class="term-text">an irregular rising and falling in number or amount; a variation.</div>
				<div class="clear-fix"></div>
				<a href="#" class="btn btn-primary my-btn title-btn " onclick="return false;"><span class="btn-text">Exchanges (cryptocurrencies)</span></a>
				<div class="term-text">businesses that allow customers to trade digital currencies for other assets, such as conventional fiat money, or different digital currencies.  They can be market makers that typically take the bid/ask spreads as transaction commissions for their services or simply charge fees as a matching platform.</div>
				<div class="clear-fix"></div>
				<a href="#" class="btn btn-primary my-btn title-btn " onclick="return false;"><span class="btn-text">QR Code</span></a>
				<div class="term-text">a machine-readable code consisting of an array of black and white squares, typically used for storing URLs or other information for reading by the camera on a smartphone.</div>
				<div class="clear-fix"></div>
				<a href="#" class="btn btn-primary my-btn title-btn " onclick="return false;"><span class="btn-text">(Bitcoin) Faucet</span></a>
				<div class="term-text">a reward system, in the form of a website or app, that dispenses rewards in the form of a satoshi, which is a hundredth of a millionth BTC, for visitors to claim in exchange for completing a captcha or task as described by the website. There are also faucets that dispense alternative cryptocurrencies.</div>
				<div class="clear-fix"></div>
				<a href="#" class="btn btn-primary my-btn title-btn " onclick="return false;"><span class="btn-text">Decentralize</span></a>
				<div class="term-text">the process of redistributing or dispersing functions, powers, people or things away from a central location or authority.</div>
				<div class="clear-fix"></div>
				<a href="#" class="btn btn-primary my-btn title-btn " onclick="return false;"><span class="btn-text">Mining</span></a>
				<div class="term-text">obtain units of (a cryptocurrency) by running a computer process to solve specific mathematical problems.</div>
				<div class="clear-fix"></div>
				<a href="#" class="btn btn-primary my-btn title-btn " onclick="return false;"><span class="btn-text">Peer to Peer</span></a>
				<div class="term-text">denoting or relating to computer networks in which each computer can act as a server for the others, allowing shared access to files and peripherals without the need for a central server.</div>
				<div class="clear-fix"></div>
				<a href="#" class="btn btn-primary my-btn title-btn " onclick="return false;"><span class="btn-text">Private Key</span></a>
				<div class="term-text">a cryptographic key that can be obtained and used by anyone to encrypt messages intended for a particular recipient, such that the encrypted messages can be deciphered only by using a second key that is known only to the recipient (the private key ).</div>
				<div class="clear-fix"></div>
				<a href="#" class="btn btn-primary my-btn title-btn " onclick="return false;"><span class="btn-text">Satoshi</span></a>
				<div class="term-text">the smallest fraction of a Bitcoin that can currently be sent: 0.00000001 BTC, that is, a hundredth of a millionth BTC. In the future, however, the protocol may be updated to allow further subdivisions, should they be needed.</div>
				<div class="clear-fix"></div>
				<a href="#" class="btn btn-primary my-btn title-btn " onclick="return false;"><span class="btn-text">Transaction Fee</span></a>
				<div class="term-text">the price one pays as remuneration for rights or services. Fees usually allow for overhead, wages, costs, and markup.</div>
				<div class="clear-fix"></div>
				<a href="#" class="btn btn-primary my-btn title-btn " onclick="return false;"><span class="btn-text">Cryptocurrency Wallets</span></a>
				<div class="term-text">an application that stores the digital credentials for your cryptocurrency holdings and allows one to access and spend your digital currency.</div>
				<div class="clear-fix"></div>
				<a href="#" class="btn btn-primary my-btn title-btn " onclick="return false;"><span class="btn-text">Software Wallets</span></a>
				<div class="term-text">connect to the cryptocurrency network and allow spending in addition to holding the credentials that prove ownership.</div>
				<div class="clear-fix"></div>
				<a href="#" class="btn btn-primary my-btn title-btn " onclick="return false;"><span class="btn-text">Online Wallets</span></a>
				<div class="term-text">credentials to access funds are stored with the online wallet provider rather than on the user's hardware.</div>
				<div class="clear-fix"></div>
				<a href="#" class="btn btn-primary my-btn title-btn " onclick="return false;"><span class="btn-text">Paper Wallets</span></a>
				<div class="term-text">store the credentials necessary to spend cryptocurrency offline.</div>
				<div class="clear-fix"></div>
				<a href="#" class="btn btn-primary my-btn title-btn " onclick="return false;"><span class="btn-text">Network</span></a>
				<div class="term-text">a number of interconnected computers, machines, or operations.</div>
				<div class="clear-fix"></div>
				<a href="#" class="btn btn-primary my-btn title-btn " onclick="return false;"><span class="btn-text">HTTP</span></a>
				<div class="term-text">Hypertext Transfer (or Transport) Protocol, the data transfer protocol used on the World Wide Web.</div>
				<div class="clear-fix"></div>
				<a href="#" class="btn btn-primary my-btn title-btn " onclick="return false;"><span class="btn-text">micro transaction</span></a>
				<div class="term-text">a very small financial transaction conducted online.</div>
				<div class="clear-fix"></div>
				<a href="#" class="btn btn-primary my-btn title-btn " onclick="return false;"><span class="btn-text">node</span></a>
				<div class="term-text">a piece of equipment, such as a PC or peripheral, attached to a network.</div>
				<div class="clear-fix"></div>
				<a href="#" class="btn btn-primary my-btn title-btn " onclick="return false;"><span class="btn-text">open source</span></a>
				<div class="term-text">denoting software for which the original source code is made freely available and may be redistributed and modified.</div>
				<div class="clear-fix"></div>
				<a href="#" class="btn btn-primary my-btn title-btn " onclick="return false;"><span class="btn-text">timestamp</span></a>
				<div class="term-text">a digital record of the time of occurrence of a particular event.</div>
				<div class="clear-fix"></div>
				<a href="#" class="btn btn-primary my-btn title-btn " onclick="return false;"><span class="btn-text">USD</span></a>
				<div class="term-text">abbreviation of the official currency of the United States and its insular territories per the United States Constitution.</div>
				<div class="clear-fix"></div>
				<a href="#" class="btn btn-primary my-btn title-btn " onclick="return false;"><span class="btn-text">Euro</span></a>
				<div class="term-text">official currency of the eurozone, which consists of 19 of the 28 member states of the European Union.</div>
				<div class="clear-fix"></div>
				<a href="#" class="btn btn-primary my-btn title-btn " onclick="return false;"><span class="btn-text">Off shore</span></a>
				<div class="term-text">made, situated, or conducting business abroad, especially in order to take advantage of lower costs or less stringent regulation.</div>
				<div class="clear-fix"></div>
				<a href="#" class="btn btn-primary my-btn title-btn " onclick="return false;"><span class="btn-text">crony capitalism</span></a>
				<div class="term-text">an economic system characterized by close, mutually advantageous relationships between business leaders and government officials.</div>
				<div class="clear-fix"></div>
				<a href="#" class="btn btn-primary my-btn title-btn " onclick="return false;"><span class="btn-text">wealth redistribution</span></a>
				<div class="term-text">Redistribution of income and redistribution of wealth are respectively the transfer of income and of wealth (including physical property) from some individuals to others by means of a social mechanism such as taxation, charity, welfare, public services, land reform, monetary policies, confiscation, divorce or tort law.</div>
				<div class="clear-fix"></div>
				<a href="#" class="btn btn-primary my-btn title-btn " onclick="return false;"><span class="btn-text">free enterprise</span></a>
				<div class="term-text">an economic system in which private business operates in competition and largely free of state control.</div>
				<div class="clear-fix"></div>
				<a href="#" onclick="setPageIndex(1); return false;" class="btn btn-warning my-btn">Back</a> 
				</div>
				<?php
				break;
			default:
				echo "<div class='error'>Wrong page index</div>";
				break;
		}
		?>

		<script type="text/javascript">
		
		var linkUpdate = "";
		var linkToExchange = "";
		var linkToUSD = "";
		var messageInterval ="";
		$(document).ready(function(){
			
			

		<?php
		$LTC = "Loading LTC balance...";
		$USD = "Loading USD balance...";
		
		if (isset($_SESSION[$_GET['link']]['display']['LTC'])) $LTC = $_SESSION[$_GET['link']]['display']['LTC'];
		if (isset($_SESSION[$_GET['link']]['display']['USD'])) $USD = $_SESSION[$_GET['link']]['display']['USD'];

		?>

		$("#balanceLTC").html(<?php echo json_encode($LTC)?>);	
		$("#balanceUSD").html(<?php echo json_encode($USD)?>);	
		linkUpdate = setInterval(updateLinkStatus,15000);
		updateLinkStatus();
		});
		function updateLinkStatus(){

			var myLink = ""
			<?php 
			$link = "";
			if (isset($_SESSION['link'])) $link = $_SESSION['link']['link']; ?>
			
			myLink = <?php echo json_encode($link); ?>;	
			if (myLink!="" && myLink!=null){
				$.ajax({
				 	url: SITE_URL +"actions/links-handler-m.php",
				 	type: "post",
				 	cache: false,
				 	data: {link: myLink, action: "update"},
				 	dataType: "json",
				 	success: function(json){
				 		if (json['status']!="ok") {
				 			if(json['action']=="destroy") window.location = SITE_URL;
				 			
				 			return;
				 		}
				 		
				 		
						$("#balanceLTC").html(json['LTC']);	
						$("#balanceUSD").html(json['USD']);	
				 		
				 	},
				 	error: function (xhr, ajaxOptions, thrownError) {
			        	//alert(xhr.status+ " "+xhr.responseText+" "+thrownError);
			      	}
				});	
				
		 	}
		}

		function removeLinkInSession(){
			swal({
			  title: "CAUTION",
			  text: "Are you sure you want to remove your current session and get a new link? Make sure you stored this link somewhere before you proceed. Press 'Cancel' to cancel this action.",
			  type: "warning",
			  showCancelButton: true,
			  confirmButtonColor: "#DD6B55",
			  confirmButtonText: "Yes, remove it!",
			  closeOnConfirm: false
			},
			function(){
				$.ajax({
					url : SITE_URL+"actions/remove-link-session.php",
					type: "post",
					success: function (txt){
						if (txt == "ok"){
							window.location=SITE_URL;
						}else{
							//alert(txt);
						}
					}
				});
			  
			});
			
		}
		function convertToUSD(){
			amount = $("#amount").val();
			moveBalance(amount);
		}

		function moveBalance(amount){
			where = "exchange";
			$.ajax({
				url : SITE_URL+"actions/handle-balance-m.php",
				data: {action:where,amount:amount},
				type : "post",
				dataType: "json",
				success: function(json){

					
					location = SITE_URL+"?link="+json['link'];
					
				},
				error: function (xhr, ajaxOptions, thrownError) {
			    	//alert(xhr.status+ " "+xhr.responseText+" "+thrownError);
			    }
			});


		}


		function exchangeToUSD(){
			where = "exchange";
			$.ajax({
				url: SITE_URL +"actions/links-handler-m.php",
				data: {action: "exchange", currency: "USD"},
				type: "post",
				dataType: "json",
				success: function(json){
					location = SITE_URL+"?link="+json['link']+"&status="+json['status']+"&msg="+json['msg'];
					
				},
				error: function (xhr, ajaxOptions, thrownError) {
			    	//alert(xhr.status+ " "+xhr.responseText+" "+thrownError);
			    }
			});	
			
		}

		function bookMark(link){
			return false;
			url = SITE_URL+"?link="+link;
			title = "www.getcryptonow.com - private link";
			if (window.sidebar) // firefox
			 window.sidebar.addPanel(title, url, "");
			else if(window.opera && window.print){ // opera
			 var elem = document.createElement('a');
			 elem.setAttribute('href',url);
			 elem.setAttribute('title',title);
			 elem.setAttribute('rel','sidebar');
			 elem.click();
			} 
			else if(document.all)// ie
			 window.external.AddFavorite(url, title);
			

		}
		function copyLink(link){
			if (document.queryCommandSupported("copy")){
				var aux = document.createElement("input");
				aux.setAttribute("value", SITE_URL+"?link="+link);
				document.body.appendChild(aux);
				aux.select();
				document.execCommand("copy");
				document.body.removeChild(aux);
				swal("Done!", "Link copied to clipboard!", "success")
				
			}else{
				swal(SITE_URL+"?link="+link);
			}
			
			
			 
			return true;
		}

		
		function showModal(txt,media=false){
			$("#myModal").show();
			$("#myModalContent").html(txt);
			$(".close").on("click",function(){
				if (media=="video"){
					delete($("#player"));
					$("#player").remove();	
					player.dispose();	
				}
				$("#myModal").hide();
				$("#myModalContent").empty();

			});
			if (media=="video"){
				var player=videojs("player", {
				  controls: true,
				  preload: 'auto',
				  aspectRatio: '16:9'
				});
				player.play();
			}
		}

		function viewDisclaimer(){
			showModal('<video class=\'video-js\' id=\'player\'  playsinline><source src=\'<?php echo SITE_URL?>vid/disclaimervideo.MP4\' type=\'video/mp4\'>Your browser doesn\'t support HTML5.</video>','video');
			
			
		}
		function watchIntro(){
			showModal('<video class=\'video-js\' id=\'player\'  playsinline><source src=\'<?php echo SITE_URL?>vid/intro3.MP4\' type=\'video/mp4\'>Your browser doesn\'t support HTML5.</video>','video');
		}

		$(".help-link").click(function(event){
			event.stopPropagation();
			return false;
		});
			


		</script>
		</div>
	<?php
	}
}

?>

