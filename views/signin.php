<?php include ("../_config.php"); ?>
<div class="panel-body">
	<h3>Sign In</h3>
	<form id="form" action="<?php echo SITE_URL ?>../actions/login.php" method="post">
		<div class="form-group">
			<label for="email">Email address</label>
			<input type="email" class="form-control" id="email" name="email" placeholder="Enter email" required>
		</div>
		<div class="form-group">
			<label for="password">Password</label>
			<input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
		</div>
		<button type="submit" class="btn btn-primary">Sign In</button>
	</form>
	<script type="text/javascript">
		$("#form").validate();
	</script>
	<p>Don't have an account? <a href="<?php echo SITE_URL ?>signup">Sign Up!</a></p>
</div>