<?php include("../_config.php");?>
<div class="panel-body  center-text">
<?php
session_start();
$Refs = new Refs();

if (!isset($_SESSION['admin'])){
	?>
	<div class="row">
	<div class="col-sm-0 col-md-4"></div>
	<div class="col-sm-12 col-md-4">
		<div class="form-group">
		<label for="pass">Enter password</label>
		<input type="password" name="pass" id="pass" class="form-control">
		</div>	
		<a class="btn btn-primary" onclick="enterPass(); return false;">Submit</a>
	
	</div>
	<div class="col-sm-0 col-md-4"></div>
	</div>
	<script type="text/javascript">
	function enterPass(){
		pass = $("#pass").val();
		$.ajax({
			url: SITE_URL+"actions/submit-pass.php",
			type: "post",
			data: {password: pass},
			success: function(txt){
				if (txt == "ok"){
					window.location.reload();
				}else{
					alert(txt);
				}
			}
		});
	}
	</script>
<?php
}else{
	$date = date('Y-m-d H:i:s');
	echo $date ."<br />";
	$res = $Refs->getRefs();
	if ($res){
		?>
		<table class="table table-striped">
		<thead>
		<tr>
			<th>Ref</th>
			<th>IP</th>
			<th>Date</th>
			</tr>
		</thead>
		<tbody>	
		<?php
		for ($i=0;$i<count($res);$i++){
			echo "<tr>";
			echo "<td>".$res[$i]['ref']."</td>";
			echo "<td>".$res[$i]['ip']."</td>";
			echo "<td>".$res[$i]['date_created']."</td>";
			echo "</tr>";
		}
		echo "</tbody>";
		echo "</table>";
	}else{
		echo "No refs found.";
	}
	?>



	<?php
}
?>
