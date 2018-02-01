<?php 
include("../_config.php");
session_start();

$securimage = new Securimage();

if ($securimage->check($_POST['captcha_code']) == false) {
	$msg="Captcha code was incorrect. Please try again.";
	if (!isset($_SESSION['cashout-code-attempts'])){
		$_SESSION['cashout-code-attempts']=1;
	}else{
		$_SESSION['cashout-code-attempts']++;
	}
  	header("Location: ".SITE_URL."cash-link?status=error&msg=".$msg);
  	exit();
}
if ($_POST['link']!="asd"){
	if (!isset($_SESSION['cashout-code-attempts'])){
		$_SESSION['cashout-code-attempts']=1;
	}else{
		$_SESSION['cashout-code-attempts']++;
	}
	$msg="The link provided is incorrect. Please try again";
	header("Location: ".SITE_URL."cash-link?status=error&msg=".$msg);
  	exit();	
}


//Success:
$msg = "Success!";
header("Location: ".SITE_URL."cash-link?status=success&msg=".$msg);
exit();

