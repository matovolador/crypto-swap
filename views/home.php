<?php include("../_config.php"); 
session_start();
?>
<script type="text/javascript">var SITE_URL = "<?php echo SITE_URL;?>"</script>
<div class="panel-body">
	<h1>GetCryptoNow!</h1>
	<h4>Crypto Currencies App</h4>
	<div class="error ghost" id="alerts"></div>
	<a href="#" class="btn btn-primary" onclick="createLink(); return false;">Click to create your private link!</a>
</div>
<script type="text/javascript">

function createLink(){
	$.ajax({
		url: SITE_URL+"actions/create-link.php",
		dataType: "json",
		type: "post",
		success: function(json){
			alert(json);
			if (json['message'] != "ok"){
				$("#alerts").html(json['message']);
				$("#alerts").show();
				return;
			}else{
				window.location = SITE_URL+"crypto-links?link="+json['link'];
			}
		},
		error: function (error) {
            alert('error; ' + JSON.stringify(error));
              }
	});
}

</script>