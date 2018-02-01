<?php
include("../_config.php");
session_start();
$Litecoind = new Litecoind();
$address = $Litecoind->createNewAddress();
if (!isset($address['code'])){
	$_SESSION['assigned_address'] = $address;
	echo "ok";
	exit();
}else{
	echo "Error: ".$address['message'];
	exit();
}

