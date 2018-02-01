<?php
include("../_config.php");
session_start();
if (!isset($_POST['pin'])||!isset($_POST['link'])) die ("bad params");
$Links = new Links();
$link = $Links->getLinkWithPin($_POST['link'],$_POST['pin']);
if (!$link){
	echo "That link doesn't exist, or that PIN is not correct. Please try again";
	exit();
}else{
	$_SESSION['link'] = $link;
	$_SESSION['pin'] = $_POST['pin'];
	echo "ok";
	exit();
}