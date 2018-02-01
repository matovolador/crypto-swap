<?php
include ("../_config.php");
session_start();
$chats = new Chats();

if ($_POST['action']=="joinRoom"){
	if (!isset($_POST['key'])) die("bad params");
	$key = $_POST['key'];
	$username = $_POST['username'];
	$result = $chats->logIntoRoom($key,$username);
	echo $result;

	exit();
}

if ($_POST['action']=="createRoom"){
	if (!isset($_POST['username'])) die("bad params");
	$username = $_POST['username'];
	$roomKey=$chats->createRoom();
	if ($roomKey == false){
		echo "Error creating room";
		exit();
	}
	$res = $chats->logIntoRoom($roomKey,$username);
	if ($res=="ok"){
		echo "success";
		exit();	
	}else{
		header("Location: ".SITE_URL."chat?status=error&msg=".$res);
		exit();
	}

	

}

if ($_POST['action']=="readChat"){
	$roomKey = $_SESSION['room_key'];
	$chatText = $chats->readChat($roomKey);
	if ($chatText!=false){
		echo $chatText;
		exit();
	}else{
		echo "FALSE";
		exit();
	}
	exit();
}

if ($_POST['action']=="writeToChat"){
	$roomKey = $_SESSION['room_key'];
	$username = $_SESSION['room_username'];
	$content = $_POST['contents'];
	$content = "<b>".$username.":</b>".$content."<br />";
	$flag = $chats->writeToChat($roomKey,$content);
	if (!$flag){
		echo $flag;
	}else{
		echo "success";
	}

	exit();
}

if ($_POST['action']=="leaveRoom"){
	unset($_SESSION['room_key']);
	unset($_SESSION['room_username']);
	echo "OK";
	exit();
}

echo "ERROR: Uncatched <action>";
exit();

?>