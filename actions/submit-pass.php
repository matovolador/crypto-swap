<?php
include("../_config.php");
session_start();
if (isset($_POST['password'])){
	if ( md5($_POST['password']) == md5("biz")){
		$_SESSION['admin'] = true;
		echo "ok";
		exit();
	}else{
		echo "Wrong password";
		exit();
	}

}else{
	die ("bad params");
}