<?php
$blogger_id = false;
if (isset($_SESSION['blogger_id']) ) $blogger_id = $_SESSION['blogger_id'];
if (!$blogger_id) {
	?>
	<script type="text/javascript">
		setPageIndex(13);
	</script>
	<?php
}
if ($_POST['action']=='delete' && isset($_POST['post_id'])){
	$Articles = new $Articles();
	$Articles->delete($_POST['$post_id']);
}
?>
<h4>Dashboard</h4>
<div class="jumbotron text-center">
	<p>Welcome to dashboard <small><?php echo $_SESSION['blogger_username'] ?></small></p>
	<a href="#" class="btn btn-primary" onclick="addArticle(); return false;">Create Article</a>
</div>
<div>
	<table class="table table-striped">
		<th>Title</th>
		<th>Body</th>
		<th>Author</th>
		<th>Date</th>
		<?php
		$posts = false;
		if ($blogger_id != false){
			$Articles = new $Articles();
			$posts = $Articles->getByBlogger($blogger_id);	
		}
		if ($posts!=false){
			for ($i=0;$i<count($posts);$i++){
				echo "<tr>";
					echo "<td>";	
						echo $posts[$i]['title'];		
					echo "</td>";
					echo "<td>";	
						echo $posts[$i]['body'];		
					echo "</td>";
					echo "<td>";	
						echo $posts[$i]['author'];		
					echo "</td>";
					echo "<td>";	
						echo $posts[$i]['date_created'];		
					echo "</td>";
					echo "<td>";
						echo "<a href='#' class='btn btn-default pull-right' onclick='editPost(".$post[$i]['id'].");'>Edit</a>";
					echo "</td>";
					echo "<td>";
						echo "<form id='delete-form' method='post' action=''>";
							echo "<input type='submit' value='Delete' class='btn btn-danger' data-callback='deletePost(".$post['id'].")'>";
							echo "<input type='hidden' name='post_id' value='".$post[$i]['id']."'>";
							echo "<input type='hidden' name='action' value='delete' >";
						echo "</form>";
					echo "</td>";
				echo "</tr>";
		
			}	
		}

		?>
		<script type="text/javascript">
		function 

		function editPost(id){

		}

		function deletePost(id){
			swal({
			  title: "CAUTION",
			  text: "Are you sure you want to delete this post?. Press 'Cancel' to cancel this action.",
			  type: "warning",
			  showCancelButton: true,
			  confirmButtonColor: "#DD6B55",
			  confirmButtonText: "Yes, delete it",
			  closeOnConfirm: false
			},
			function(){
				$("#delete-form").submit();
			});
		}
		</script>
	</table>
</div>