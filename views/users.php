<?php include ("../_config.php"); 
$users = new Users();
if (!isset($_GET['id'])){
	$res = $users->getUsers();
}else{
	$res = $users->getUserById($_GET['id']);	
}

?>
<div class="panel-body">
	<h1>Users</h1>
	<?php
	if (!isset($_GET['id'])){ ?>
	<ul>
		<?php
		while($row = $res->fetch_assoc()){
			echo "<li>Name :". $row["name"] . " - Email: " . $row["email"] ." <a href=".SITE_URL."users/".$row['id'].">View</a></li>";

		}
		?>


	</ul>
	<?php
	}else{
		if($row = $res->fetch_assoc()){
			echo "<p>Name :". $row["name"] . " - Email: " . $row["email"] ."</p>";
		}else{
			echo "<p>User by id=".$_GET['id']."not found</p>";
		}
		
	} ?>
	
</div>