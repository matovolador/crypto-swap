<?php 
include("../_config.php");
session_start();
if (!isset($_POST['action'])||!isset($_POST['amount'])) die ("bad params");

$Links = new Links();
$Litecoind = new Litecoind();

$amount = $_POST['amount'];
$action = $_POST['action'];
$link = $_SESSION['link']['link'];

$link = $Links->getLink($link);
if ($link){
	if ($action == "exchange"){
		$Balances = new Balances();
		$balances = $Balances->getByLink($link['link']);
		$LTCserver = 0;
		$balanceToSend = [];
		$h = 0;
		for ($i=0;$i<count($balances);$i++){
			if (!$balances[$i]['disposed'] && $balances[$i]['confirmations']>=6  && !$balances[$i]['in_exchange'] && $balances[$i]['concept_flag']=="A01"){
				$balanceToSend[$h] = $balances[$i];
				$h++;
				$LTCserver += $balances[$i]['amount'];
				
			}
		}


		if ($LTCserver<$amount){
			$msg = "You are trying to convert LTC ".$amount.", and your balance is LTC ".$LTCserver;
			$_SESSION['script_status']="error";
			$_SESSION['script_message']=$msg;
			echo json_encode(array("link"=>$link['link']));
			exit();
		}
		$minimumAmount = 0.2;
		if ($amount < $minimumAmount ){
			$msg = "You must convert at least LTC ".$minimumAmount.". You are trying to convert LTC ".$amount;
			$_SESSION['script_status']="error";
			$_SESSION['script_message']=$msg;
			echo json_encode(array("link"=>$link['link']));
			exit();
		}
		//Proceed to send the LTC in the server AND adding and changing balance entries:
		$res=$Balances->sendToExchange($link['link'],$amount);
		$msg = "Your chosen amount is being converted to USD. Please wait.";
		$_SESSION['script_status']="success";
		$_SESSION['script_message']=$msg;
		echo json_encode(array("link"=>$link['link']));
		exit();
	}else{
		die ("bad params");
	}

}else{
	$msg = "That link doesn't exist";
	$_SESSION['script_status']="error";
	$_SESSION['script_message']=$msg;
	echo json_encode(array("link"=>$link['link']));
	exit();
}


 ?>