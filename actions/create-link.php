<?php
include("../_config.php");
session_start();
$Links = new Links();
$Litecoind = new Litecoind();
$address = $Litecoind->createNewAddress();
if (!$address){
	$msg = "There was a problem creating the address. Please try again.";
	$msg = array("message" => $msg);
	echo json_encode($msg);
	exit();
}
$flag = $Links->getLinkByAddress($address);
if (!$flag){
	if (isset($_POST['refLink'])){
		$flag = $Links->getLink($_POST['refLink']);
		if (!$flag){
			$msg = "Error: The link used as referral doesn't exist. Please try again.";
			$msg = array("message" => $msg);
			echo json_encode($msg);
			exit();		
		}else if($flag['ref_link']!=NULL){
			$msg = "Error: Your link has a referral. You cannot make links from this link as referral.";
			$msg = array("message" => $msg);
			echo json_encode($msg);
			exit();		
		}
		$link = $Links->generateLink(0,$address,$_POST['refLink']);	
		$msg = "ok";
		$msg = array("message" => $msg,"link" => $link);
		echo json_encode($msg);
		exit();
	}else{
		$_SESSION['link'] = $Links->generateLink(0,$address);
		$msg = "ok";
		$msg = array("message" => $msg,"link" => $_SESSION['link']['link']);
		$_SESSION['page_index'] = 1;
		echo json_encode($msg);
		exit();	
	}
	exit();
}else{
	$msg = "Error: Address already in use. Contact administrator.";
	$msg = array("message" => $msg);
	echo json_encode($msg);
	exit();
}