<?php
include("../_config.php");
session_start();
if (!isset($_POST['address'])) die ("bad params");

$address = $_POST['address'];
switch($address){
	case "lock":
		$_SESSION['crypto_page'] = "lock";
		echo "ok";
		exit();
		break;
	case "change-pin":
		$_SESSION['crypto_page'] = "change-pin";
		echo "ok";
		exit();
		break;
	case "cashout":
		$_SESSION['crypto_page'] = "cashout";
		echo "ok";
		exit();
		break;
	default:
		echo "Error: Uncatched <switch>";
		exit();
		break;

}
echo "Error";
exit();