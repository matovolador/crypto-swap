<?php include("../_config.php"); 
session_start();
$chats = new Chats();
?>
<div class="panel-body">
	<h3>Chat Room</h3>
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
	<div id="chat-container">
		<a href="#" class="prevent-default btn btn-link" onclick="joinRoom();">Join Room</a>
		<a href="#" class="prevent-default btn btn-link" onclick="createRoom();">Create Room</a>
		
	</div>
	<div id="chat-entry">
		<div class="form-group">
				<input type="text" class="form-control" id="chat-entry-text" name="chat-entry-text" placeholder="Enter text" required>
		</div>
		<button type="submit" class="btn btn-primary prevent-default" id="chat-btn" >Submit</button>
		<a href="#" class="prevent-default" onclick="leaveRoom();">Leave Room</a>
	</div>


</div>


<script type="text/javascript">
var userName = "";
var roomKey = "";
$( '#chat-entry-text' ).bind('keypress', function(e){
   if ( e.keyCode == 13 ) {
     writeToChat($(this).val());
   }
 });
$( '#chat-btn' ).on('click', function(e){
   writeToChat($("#chat-entry-text").val());
   
 });
function joinRoom(){
	if ( roomKey == ""){
		roomKey = prompt("Enter Room Key:");
		userName = prompt("Enter your username: ");
		$.ajax({
			async: false,
			url: SITE_URL+"actions/chat-handler.php" ,
			type: "post",
			data: {action: "joinRoom",key: roomKey,username: userName},
			success: function(result){
				if (result == "ok"){
					window.location.reload();	
					return;
				}else{
					$("#chat-container").html("<div class='error'>Error joining chat room. Result: "+result+".</div>");
				}
				
			}
		});
	}

}

function createRoom(){
	userName = prompt("Enter your username:");

	$.ajax({
		async:false,
		url: SITE_URL+"actions/chat-handler.php",
		type: "post",
		data: {action:"createRoom",username:userName},
		success: function(result){
			if (result=="success"){
				window.location.reload();

			}else{
				$("#chat-container").html("<div class='error'>Error creating chat room. Response: "+result+".</div>");
			}
		}
	});

}

function leaveRoom(){
	$.ajax({
		async: false,
		url: SITE_URL+"actions/chat-handler.php",
		type: "post",
		data: {action: "leaveRoom"},
		success: function(txt){
			window.location.reload();
		}
	});
	clearInterval(chatTicker);
	//TODO destroy in DB and $_SESSION['room_username'] and $_SESSION['room_key']
}



///CHAT SYSTEM SCRIPTS:

function readChat(){

	$.ajax({
		async: true,
		data: {action: "readChat"},
		type: "post",
		url: SITE_URL+"actions/chat-handler.php",

		success: function(txt){
			if (txt=="FALSE"){
				//ERROR READING CHAT FILE:
				$("#chat-container").html("<div class='error'>Error reading chat file.</div>");
				return;
			}
			
			if (txt!= $("#chat-container").html()){
				$("#chat-container").html(txt);
			}


		}

	});
}

function writeToChat(text){
	$.ajax({
		async: true,
		data: {action: "writeToChat",contents: text},
		type: "post",
		url: SITE_URL+"actions/chat-handler.php",
		success: function(result){
			if (result=="ERROR"){
				return;
				$("#chat-container").html("<div class='error'>Error writting to chat file.</div>");
			}
			if (result == "success"){
				//do something
			}
		}

	});
	$('#chat-entry-text').val("");
}

$(document).ready(function(){
var chatTicket;
$("#chat-entry").hide();
<?php 
if (isset($_SESSION['room_username'])&& isset($_SESSION['room_key'])){
	$roomKey = $_SESSION['room_key'];
	$userName = $_SESSION['room_username'];
}else{
	$roomKey = "";
	$userName = "";
}
?>

userName = <?php echo json_encode($userName); ?>;
roomKey = <?php echo json_encode($roomKey); ?>;

if (userName != "" && roomKey !=""){
	chatTicker = setInterval(readChat,500);
	$("#chat-entry").show();
}



});
</script>