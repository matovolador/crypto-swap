<?php include("../_config.php"); 
session_start();

?>
<div class="panel-body">
	<h3>Cashout</h3>
	<?php
	$msg=""; 
	if (isset($_GET['msg'])){
		$status = $_GET['status'];
		$msg = $_GET['msg'];
		if ($status == "success"){
			echo "<div class='success'>".$msg."</div>";
		}
		if ($status == "error"){
			echo "<div class='error'>".$msg."</div>";
		}
	}
	?>
	<form id="form" method="post" action="<?php echo SITE_URL ?>actions/securimage-implementation.php">
		<div class="form-group">
			<label for="link">Code</label>
			<input type="text" class="form-control" id="link" name="link" placeholder="Enter code" required>
		</div>
		<?php 
		if (isset($_SESSION['cashout-code-attempts'])) {
			if ($_SESSION['cashout-code-attempts'] >= 1){  ?>
			<div class="form-group">
				<img id="captcha" src="<?php echo SITE_URL?>securimage/securimage_show.php" alt="CAPTCHA Image" />
				<input type="text" name="captcha_code" size="10" maxlength="6" />
				<a href="#" onclick="document.getElementById('captcha').src = SITE_URL+'securimage/securimage_show.php?' + Math.random(); return false">[ Different Image ]</a>
			<?php
			}
		}
		?>
		<input type="hidden" name="action" value="cashout" required>
		<button type="submit" class="btn btn-primary">Submit</button>
	</form>
	<script type="text/javascript">
		$("#form").validate();
	</script>
</div>