<?php 
include("../_config.php");
session_start();
$Links = new Links();
$Litecoind = new Litecoind();

if ($_POST['action']=="create"){
	$address = $Litecoind->createNewAddress();
	if (!$address){
		$msg = "There was a problem creating the address. Please try again.";
		$_SESSION['script_status']="error";
		$_SESSION['script_message'] = $msg;
		$_SESSION['crypto_page'] = "create";
		header("Location: ".SITE_URL);
		exit();
	}
	$flag = $Links->getLinkByAddress($address);
	if (!$flag){
		$_SESSION['assigned_address'] = $address;
		$_SESSION['link'] = $Links->generateLink(0,$address);
		$msg = "Your link is: ".$_SESSION['link']['link'].". To create a balance you must send LTC to this address: ".$_SESSION['assigned_address'].".";
		$_SESSION['script_status']="success";
		$_SESSION['script_message'] = $msg;
		$_SESSION['crypto_page'] = "create";
		header("Location: ".SITE_URL."?p=create&status=success&msg=".$msg);
		exit();
	}else{
		$msg = "That address is already in use for a different Link. Please try again.";
		$_SESSION['script_status']="error";
		$_SESSION['script_message'] = $msg;
		$_SESSION['crypto_page'] = "create";
		header("Location: ".SITE_URL);
		exit();
	}
}
if ($_POST['action']=="submit-link"){
	if (!isset($_POST['link'])) die ("bad params");
	$link = $_POST['link'];
	$link = $Links->getLink($link);
	if ($link){
		$_SESSION['link'] = $link;
		header("Location: ".SITE_URL."");
		exit();	

	}else{
		$msg = "That link doesn't exist. Please try again.";
		$_SESSION['script_status']="error";
		$_SESSION['script_message'] = $msg;
		$_SESSION['crypto_page'] = "create";
		header("Location: ".SITE_URL);
		exit();	
	}

}
if ($_POST['action']=="submit-pin"){

	if (!isset($_POST['pin'])||!isset($_POST['link'])) die ("bad params");



	$Links = new Links();
	$link = $Links->getLinkWithPin($_POST['link'],$_POST['pin']);
	if ($link){
		$_SESSION['link'] = $link;
		$_SESSION['pin'] = md5($_POST['pin']);
		$_SESSION[$link]['page_index'] = 1;
		unset($_SESSION['script_status']);
		unset($_SESSION['script_message']);
		header("Location: ".SITE_URL."?link=".$_SESSION['link']['link']);
		exit();
	}else{
		$_SESSION['script_status']="error";
		$_SESSION['script_message']= "That password is incorrect. Please try again.";
		header("Location: ".SITE_URL."?link=".$_POST['link']);
		exit();
	}



}
if ($_POST['action'] == "update"){
	$link = $_POST['link'];	

	$Links = new Links();
	$Balances = new Balances();
	$res = $Links->getLink($link);
	if ($res){
		//UPDATE CONFIRMATIONS:
		//Get deposits:
		$deposits = $Litecoind->getDepositsByAddress($res['assigned_address']);
		//add if new :
		for ($i=0;$i<count($deposits);$i++){
			$Balances->add($res['link'],"LTC",$deposits[$i]['amount'],"A01",$deposits[$i]['tx']);
		}
		//Update confirmations on all Transactions under this Link:
		$balances = $Balances->getByLink($res['link']);
		for ($i=0;$i<count($balances);$i++){
			$tx = $balances[$i]['tx'];
			if ($tx!=NULL){
				$txArray = $Litecoind->getTransaction($tx);	
				$Balances->setConfirmations($balances[$i]['id'],$txArray['confirmations']);
			}
		}



		//CALCULATE BALANCES:
		$balances = $Balances->getByLink($res['link']);
		$LTCserver = 0;
		$LTCserverPending = 0;
		$LTCexchange = 0;
		$LTCexchangePending = 0;
		$fees = false;
		$USD = 0;
		$h=0;


		for ($i=0;$i<count($balances);$i++){
			if (!$balances[$i]['disposed']){
				if ($balances[$i]['concept_flag']=="A01" ){
					//balance is in the server and in LTC
					if ($balances[$i]['confirmations']>=6 || $balances[$i]['tx']=="-1") $LTCserver += $balances[$i]['amount'];
					if ($balances[$i]['confirmations']<6 && $balances[$i]['tx']!="-1") $LTCserverPending += $balances[$i]['amount'];
				}
				if ($balances[$i]['concept_flag'] == "A05"){
					if ($balances[$i]['confirmations']>=6 || $balances[$i]['tx']=="-1") {
						$LTCexchange += $balances[$i]['amount'];
						$LTCbalancesToConvert[$h] = $balances[$i];
						$h++;
					}
					
					if ($balances[$i]['confirmations']<6 && $balances[$i]['tx']!="-1" ) $LTCexchangePending += $balances[$i]['amount'];
	 			}
	 			if ($balances[$i]['concept_flag'] == "A04" ||$balances[$i]['concept_flag'] == "A07" ){
	 				$USD+=$balances[$i]['amount'];
	 			}
			}

			
		}
		$LTCserver = number_format($LTCserver,6) + 0;
		$LTCserverPending = number_format($LTCserverPending,6) + 0;
		$LTCexchange = number_format($LTCexchange,6) + 0;
		$LTCexchangePending = number_format($LTCexchangePending,6)  + 0;
		$USD =  number_format($USD,6) + 0;
		if ($LTCserver<0.001) $LTCserver = 0;
		if ($LTCserverPending<0.001) $LTCserverPending = 0;
		if ($LTCexchange<0.001) $LTCexchange = 0;
		if ($LTCexchangePending<0.001) $LTCexchangePending = 0;
		if ($USD < 0.001) $USD = 0;
		$displayBalance = array("LTCserver" => $LTCserver , "LTCserverPending" => $LTCserverPending,"LTCexchange" => $LTCexchange, "LTCexchangePending" => $LTCexchangePending , "USD" => $USD);
		if ($displayBalance["LTCexchange"]>0){
			//try to convert it to USD:
			$symbol = "ltcusd";
			$side = "sell";
			$type = "exchange market";
			$price = "123123.0";
			$amount = $displayBalance['LTCexchange']."";
			$BF = new Bitfinex(BITFINEX_API_KEY,BITFINEX_API_SECRET);
		
			
			$exchange = "bitfinex";
			$result = $BF->new_order($symbol, $amount, $price, $exchange, $side, $type, TRUE, FALSE, FALSE, NULL);
			if (isset($result['id']) && !isset($result['error'])){
				$price = $result['price'];
				$Balances->handleSell($res['link'],$displayBalance['LTCexchange'],$price);
				for ($i=0;$i<count($LTCbalancesToConvert);$i++){
					$Balances->setDisposed($LTCbalancesToConvert[$i]['id']);
				}				
			}

		}
		$_SESSION['link'] = $Links->getLink($link);
		$_SESSION['link']['balance'] = $displayBalance;
		$Orders = new Orders();
		$avgUSDinSession = "Calculating...";
		if (isset($_SESSION['link']['avgUSD'])) $avgUSDinSession = $_SESSION['link']['avgUSD'];
		$avgUSD = $Orders->getAveragePrice("LTCUSD");
		if ($avgUSD) {
			$_SESSION['link']['avgUSD'] = $avgUSD ;
		} else{
			$_SESSION['link']['avgUSD'] = $avgUSDinSession;	
		}
		///DISPLAY STRINGS:
		
		$myLink = $_SESSION['link'];
 		$flag = false;
	
		$string = "";
		if ($myLink['balance']['LTCserver']>0){
			$string .="LTC ".$myLink['balance']['LTCserver'];
			$flag = true;
		}
		if ($myLink['balance']['LTCserverPending']>0){
			if ($string !=""){
				$string .="<br />";
			}else{
				$string .="";
			} 
			$string .= "LTC ".$myLink['balance']['LTCserverPending']."<br />pending confirmation...";
			$flag = true;
		}
		if ($myLink['balance']['LTCexchangePending']>0 || $myLink['balance']['LTCexchange']>0){
			if ($string !=""){
				$string .="<br />";
			}else{
				$string .="";
			} 
			$string .= "LTC ".($myLink['balance']['LTCexchangePending']+$myLink['balance']['LTCexchange'])."<br />converting to USD...";
			$flag = true;
		}
		if ($flag){
			$string .= "<br />~$".(round ( ($myLink['balance']['LTCserver']+$myLink['balance']['LTCserverPending']+$myLink['balance']['LTCexchangePending']+$myLink['balance']['LTCexchange'])*$myLink['avgUSD'], 2 ,PHP_ROUND_HALF_DOWN ));
			
		}

		if (!$flag){
			$string="LTC 0";
		}

		$_SESSION[$link]['display']['LTC']=$string;
		if ($myLink['balance']['USD']>0){
			$_SESSION[$link]['display']['USD']="$ ".$myLink['balance']['USD'];	
		}else{
			
			$_SESSION[$link]['display']['USD']="$ 0";	
			

		}



		//Check for pending:
		$Orders = new Orders();
		$orders = $Orders->getByLink($link);
		for ($i=0;$i<count($orders);$i++){
			if ($orders[$i]['disposed']!=1){
				//check if they were processed:
				$order = $Orders->queryExchange($orders[$i]['order_id']);
				if (!empty($order) && $order['original_amount'] == $order['executed_amount'] && !$order['is_live']){
					//make changes in DB
					if ($order['side']=="buy"){
						$side = "buy";
						$symbol = $orders[$i]['symbol'];
						$price = $orders[$i]['price'];
						$amount = $orders[$i]['amount'];
						$amountString = "".$amount;
						$address = $orders[$i]['to_address'];

						if ($symbol == "btcusd"){
							$what = "bitcoin";
							$record = "BTC";	
						} 
						if ($symbol == "ethusd") {
							$what = "ethereum";
							$record = "ETH";
						}
						if ($symbol == "ltcusd") {
							$what = "litecoin";
							$record = "LTC";
						}
						$BF = new Bitfinex(BITFINEX_API_KEY,BITFINEX_API_SECRET);
						$result = $BF->withdraw($what,"exchange",$amountString,$address);
						if (isset($result[0]['status'])&& $result[0]['status']=="success"){
							$_SESSION['link'] = $Links->getLink($_SESSION['link']['link']);
							$Balances->add($link,$record,$amount,"A06",NULL,1,1,$address);
							///dispose order:
							$Orders->dispose($orders[$i]['order_id']);	
						}
						
					}
				}
			}

		}


		






		//------------------------
		
		echo json_encode(array("status"=>"ok","LTC"=>$_SESSION[$link]['display']['LTC'],"USD"=>$_SESSION[$link]['display']['USD']));
		exit();		
	}else{
		unset($_SESSION['link']);
		echo json_encode(array("status"=>"That link doesn't exist. Please try again.","action" =>"destroy"));
		exit();	
	}

}

if ($_POST['action'] == "lock"){
	if (!isset($_SESSION['link'])||!isset($_POST['pin'])||!isset($_POST['pin-repeat'])) die ("bad params");
	
	$link = $_SESSION['link']['link'];	
	$pin=$_POST['pin'];
	$pinRepeat = $_POST['pin-repeat'];
	if ($pin!=$pinRepeat){
		$msg="Passwords do not match. Please try again.";
		$_SESSION['script_status']="error";
		$_SESSION['script_message']=$msg;
		header("Location: ".SITE_URL."?link=".$link);
		exit();
	}
	$linkArray = $Links->getLink($link);
	if ($linkArray){
		$_SESSION['link'] = $linkArray;

		$Links = new Links();
		$result = $Links->createPIN($link,$pin);
		if ($result){
			$_SESSION['pin'] = md5($pin);
			$_SESSION['page_index']=1;

			$msg = "Your password was created.";
			$_SESSION['script_status']="success";
			$_SESSION['script_message']=$msg;
			header("Location: ".SITE_URL."?link=".$link);
			
			exit();
		}else{

			$msg="That password is already in use for this link.";
			$_SESSION['script_status']="error";
			$_SESSION['script_message']=$msg;
			header("Location: ".SITE_URL."?link=".$link);
			exit();
			
		}
		
	}else{
		$msg="That link doesn't exist. Please try again.";
		$_SESSION['script_status']="error";
		$_SESSION['script_message']=$msg;
		header("Location: ".SITE_URL);
		exit();
	}
	

}
if ($_POST['action'] == "change-pin"){
	if ((!isset($_POST['link']) && !isset($_SESSION['link']) )||!isset($_POST['pin'])||!isset($_POST['newPin'])) die ("bad params");
	if (isset($_POST['link'])){
		$link = $_POST['link'];	
	}else{
		$link = $_SESSION['link']['link'];	
	}
	$pin = $_POST['pin'];
	$newPin = $_POST['newPin'];
	$linkArray = $Links->getLinkWithPin($link,$pin);
	if ($linkArray){
		$_SESSION['link'] = $linkArray;
		$Links = new Links();
		$result = $Links->createPIN($link,$newPin);
		if ($result){
			$_SESSION['pin'] = $newPin;
			$msg = "Your new password was created.";
			$_SESSION['script_status']="success";
			$_SESSION['script_message']=$msg;
			header("Location: ".SITE_URL."?link=".$link);
			exit();
		}else{
			$msg="There was an error creating password. Please try again.";
			$_SESSION['script_status']="error";
			$_SESSION['script_message']=$msg;
			header("Location: ".SITE_URL."?link=".$link);
			exit();
			
		}
		
	}else{
		$msg="That link and password are not associated. Please try again.";
		$_SESSION['script_status']="error";
		$_SESSION['script_message']=$msg;
		header("Location: ".SITE_URL."?link=".$link);
		exit();
	}
	

}
if ($_POST['action']=="cashout"){
	if((!isset($_SESSION['link']))||!isset($_POST['address'])||!isset($_POST['currency'])||!isset($_POST['amount'])) die ("bad params");
	
	$link = $_SESSION['link']['link'];	
	$res = $Links->getLink($link);
	$pin = "";
	if (isset($_POST['pin'])) $pin = md5($_POST['pin']);
	
	if (($_SESSION['link']['pin']!=NULL || $_SESSION['link']['pin']!= "") && $_SESSION['link']['pin']!=$pin){
		$msg =  "Password is incorrect. Please try again.";
		$_SESSION['script_status']="error";
		$_SESSION['script_message']=$msg;
		header("Location: ".SITE_URL."?link=".$link);
		exit();
	}
	$address = $_POST['address'];
	$Links = new Links();
	$amount = $_POST['amount'];
	if ($res){
		$_SESSION['link'] = $res;
		$Balances = new Balances();

		if ($_POST['currency']=="LTC"){
			$symbol = "ltcusd";
			$LTC = 0;
			$LTCserver = 0;
			$LTCexchange = 0;
			$LTCbalance = $Balances->getConfirmedLTC($res['link']);

			for ($i=0;$i<count($LTCbalance);$i++){
			
				$LTCserver += $LTCbalance[$i]['amount'];
				$LTC += $LTCbalance[$i]['amount'];
			
			}
			$LTC=number_format ( $LTC ,8 );
			$LTCserver=$LTC;

			if ($amount<=$LTC){

				if ($amount <= $LTCserver){
					//SEND COINS FROM SERVER:
					$Litecoind = new Litecoind();
					$result = $Litecoind->sendCoins($address,$amount);
					if ($result && !isset($result['message'])){
						for ($i=0;$i<count($LTCbalance);$i++){
							//if ($LTCbalance[$i]['concept_flag']=="A01" && !$LTCbalance[$i]['disposed']) $Balances->setDisposed($LTCbalance[$i]['id']);
						}
						$Balances->add($link,"LTC",-$amount,"A01","-1");
						
						$Balances->add($link,"LTC",$amount,"A06",NULL,1,1,$address);
						$msg="Transaction made successfuly.";
						$_SESSION['script_status']="success";
						$_SESSION['script_message']=$msg;
						header("Location: ".SITE_URL."?link=".$link);
						exit();

					}else{
						$msg =  $res['message'] ;
						$_SESSION['script_status']="error";
						$_SESSION['script_message']=$msg;
						header("Location: ".SITE_URL."?link=".$link);
						exit();
					}
					exit();
				}
				/*
				if ($amount <= $LTCexchange){
					//SEND COINS FROM EXCHANGE:
					$BF = new Bitfinex(BITFINEX_API_KEY,BITFINEX_API_SECRET);
					$amountString = "".$amount;
					$result = $BF->withdraw("litecoin","exchange",$amountString,$address);
					if (!isset($result['error'])&& $result['status']=="success"){
						for ($i=0;$i<count($LTCbalance);$i++){
							if ($LTCbalance[$i]['concept_flag']=="A05" && !$LTCbalance[$i]['disposed']) $Balances->setDisposed($LTCbalance[$i]['id']);
						}
						if ($LTCexchange>$amount){
							$Balances->add($res['link'],"LTC",$LTCexchange-$amount,"A05",-1,1);
						}
						$Balances->add($link,"LTC",$amount,"A06",NULL,1,1,$address);
						$msg="Transaction made successfuly.";
						$_SESSION['script_status']="success";
						$_SESSION['script_message']=$msg;
						header("Location: ".SITE_URL."?link=".$link);
						exit();
					}else{
						$msg = $result['message'];
						$_SESSION['script_status']="error";
						$_SESSION['script_message']=$msg;
						header("Location: ".SITE_URL."?link=".$link);
						exit();
					}
					exit();

				}
				
				if ($LTCexchange + $LTCserver >= $amount){
					////SEND COINS FROM BOTH THE SERVER (first) AND FROM EXCHANGE(second):
					
					
					//SEND FROM SERVER:
					$Litecoind = new Litecoind();
					$result = $Litecoind->sendCoins($address,$LTCserver);
					if ($result && !isset($result['message'])){
						//TODO SET DISPOSED----
						for ($i=0;$i<count($LTCbalance);$i++){
							if ($LTCbalance[$i]['concept_flag']=="A01" && !$LTCbalance[$i]['disposed']) $Balances->setDisposed($LTCbalance[$i]['id']);
						}
						if ($LTCserver>$amount){
							$Balances->add($res['link'],"LTC",$LTCserver-$amount,"A01",-1,1);
						}
						$Balances->add($link,"LTC",$LTCserver,"A06",$result,1,1,$address);
						$amount = $amount - $LTCserver;						

						//SEND COINS FROM EXCHANGE:
						$amountString = "".$amount;
						$BF = new Bitfinex(BITFINEX_API_KEY,BITFINEX_API_SECRET);
						$result = $BF->withdraw("litecoin","exchange",$amountString,$address);
						if (!isset($result['error'])&& $result['status']=="success"){
							for ($i=0;$i<count($LTCbalance);$i++){
								if ($LTCbalance[$i]['concept_flag']=="A05" && !$LTCbalance[$i]['disposed']) $Balances->setDisposed($LTCbalance[$i]['id']);
							}
							if ($LTCexchange>$amount){
								$Balances->add($res['link'],"LTC",$amount,"A05",-1,1);
							}
							$Balances->add($link,"LTC",$amount,"A06",NULL,1,1,$address);
							$msg="Transaction made successfuly.";
							$_SESSION['script_status']="success";
							$_SESSION['script_message']=$msg;
							header("Location: ".SITE_URL."?link=".$link);
							exit();

						}else{
							$msg = $result['message'];
							$_SESSION['script_status']="error";
							$_SESSION['script_message']=$msg;
							header("Location: ".SITE_URL."?link=".$link);
							exit();
						}




					}else{
						$msg =  $result['message'] ;
						$_SESSION['script_status']="error";
						$_SESSION['script_message']=$msg;
						header("Location: ".SITE_URL."?link=".$link);
						exit();
					}
					exit();
				}
				*/

			}
		}
		if ($_POST['currency'] == "BTC") $symbol = "btcusd";
		if ($_POST['currency'] == "ETH") $symbol = "ethusd";
		
		$USDbalance = $Balances->getUSDBalance($res['link']);
		$USD = 0;
		
		for ($i=0;$i<count($USDbalance);$i++){
			$USD += $USDbalance[$i]['amount'];
		}
		$USD =number_format ( $USD ,8 ) + 0;
		
		if ($symbol == "ltcusd"){
			//check if there is LTC in the server and send it:
			if ($LTCserver>0 && $LTCserver>=0.002){
				
				//SEND FROM SERVER:
				$Litecoind = new Litecoind();
				
				$result = $Litecoind->sendCoins($address,$LTCserver);
				
				if ($result && !isset($result['message'])){
					
					$Balances->add($link,"LTC",-$LTCserver,"A01","-1");
					
					$Balances->add($link,"LTC",$LTCserver,"A06",NULL,1,1,$address);
					$amount = $amount - $LTCserver;		
					//ALL GOOD
				}else{
					$msg =  $result['message'] ;
					$_SESSION['script_status']="error";
					$_SESSION['script_message']=$msg;
					header("Location: ".SITE_URL."?link=".$link);
					exit();
					
				}
			}
			/*
			//check if there is LTC in the exchange and send it:
			if ($LTCexchange>0){
				//SEND COINS FROM EXCHANGE:
				$BF = new Bitfinex(BITFINEX_API_KEY,BITFINEX_API_SECRET);
				$amountString = "".$amount;
				$result = $BF->withdraw("litecoin","exchange",$amountString,$address);
				if (!isset($result['error'])&& $result['status']=="success"){
					for ($i=0;$i<count($LTCbalance);$i++){
						if ($LTCbalance[$i]['concept_flag']=="A05" && !$LTCbalance[$i]['disposed']) $Balances->setDisposed($LTCbalance[$i]['id']);
					}
					if ($LTCexchange>$amount){
						$Balances->add($res['link'],"LTC",$LTCexchange-$amount,"A05",-1,1);
					}
					$Balances->add($link,"LTC",$LTCexchange,"A06",NULL,1,1,$address);
					$amount = $amount - $LTCexchange;

					//ALL GOOD
				}else{
					$msg = $result['message'];
					$_SESSION['script_status']="error";
					$_SESSION['script_message']=$msg;
					header("Location: ".SITE_URL."?link=".$link);
					exit();
				}
			}
			*/

		}
		/////MAKE THE EXCHANGE:
		$Orders = new Orders();
		$array = $Orders->calculateLimitPrice($USD,$symbol);
		$side = "buy";
		$type = "exchange limit";
		$amountString = "".$amount;
		$price = $array['price'];
		$USDspent = $amount * $price;
		if ($USDspent>$USD){
			$_SESSION['script_status']="error";
			$_SESSION['script_message']="You don't have enough USD to make that purchase.";
			header("Location: ".SITE_URL."?link=".$link);
			exit();
		}
		$exchange = "bitfinex";
		$BF = new Bitfinex(BITFINEX_API_KEY,BITFINEX_API_SECRET);
		$result = $BF->new_order($symbol, $amountString, $price, $exchange, $side, $type, TRUE, FALSE, FALSE, NULL);
		if (!isset($result['id']) && isset($result['message'])){
			$msg = $result['message'];
			$_SESSION['script_status']="error";
			$_SESSION['script_message']=$msg;
			header("Location: ".SITE_URL."?link=".$link);
			exit();	
		}else if (isset($result['id'])){
			if ($symbol=="ethusd") $amount-=0.01;
			$Balances->add($link,"USD",-$USDspent,"A04",NULL,1);
			$Orders->add($result['id'],$link,$symbol,$side,$amount,$price, $address);
			$_SESSION['script_status']="success";
			$_SESSION['script_message']="Your order has been submited. It will be processed shortly. Check your Transaction History for updates.";
			header("Location: ".SITE_URL."?link=".$link);
			
		}else if(!isset($result['id']) && !isset($result['message'])){
			$_SESSION['script_status']="error";
			$_SESSION['script_message']="Cannot connect to the exchange. Please try again later.";
			header("Location: ".SITE_URL."?link=".$link);
		}
	}
}

if ($_POST['action']=="get-symbol"){

	if (!isset($_POST['symbol'])||!isset($_POST['amount'])) {
		echo 0;
		exit();
	} 
	$amount = $_POST['amount'];
	$symbol = $_POST['symbol'];
	$link = $_SESSION['link']['link'];
	$USD = 40;
	
	/////MAKE THE EXCHANGE:
	$Orders = new Orders();
	$array = $Orders->calculateLimitPrice($USD,$symbol);

	echo $array['price']*$amount;
	exit();
}

if ($_POST['action']=="exchange"){

	if((!isset($_SESSION['link']['link']))||!isset($_POST['currency'])) die("bad params");
	$link = $_SESSION['link']['link'];	

	$Orders = new Orders();

	
	$currency = $_POST['currency'];
	$Links = new Links();
	$res = $Links->getLink($link);
	if (!$res) {
		$msg="That link doesn't exist. Please try again.";
		echo json_encode(array("status" => "error","msg"=>$msg,"link"=>$res['link']));
		exit();
	}
	$Balances = new Balances();
	$deposits = $Balances->getByLink($res['link']);
	$LTCconfirmedDeposits = false;
	$LTC = 0;
	$h = 0;
	
	for ($i=0;$i<count($deposits);$i++){
		if (!$deposits[$i]['disposed'] && $deposits[$i]['concept_flag']=="A05" && $deposits[$i]['confirmations']>=6){
			$LTCconfirmedDeposits[$h]=$deposits[$i];
			$h++;
			$LTC += $deposits[$i]['amount'];
		}
	}
	if (!$LTCconfirmedDeposits){
		$msg = "You must first send your balance to the exchange. If you did, then you must wait for it to be confirmed.";
		echo json_encode(array("status" => "error","msg"=>$msg,"link"=>$res['link']));
		exit();
	}
	
	if ($currency == "USD"){
		$symbol = "ltcusd";
		$side = "sell";
		$type = "exchange market";
		$price = "123123.0";
		$amount = $LTC."";
		$BF = new Bitfinex(BITFINEX_API_KEY,BITFINEX_API_SECRET);
	
		
		$exchange = "bitfinex";
		$result = $BF->new_order($symbol, $amount, $price, $exchange, $side, $type, TRUE, FALSE, FALSE, NULL);
		if (isset($result['error'])){
			$msg = $result['message'];

			if ($msg =="Nonce is too small") {
				$msg="Cannot perform the trade at this time. Please try again.";
			}else{
				$msg .= ". Bare in mind that sending LTC to the exchange can sometimes take longer than 6 confirmations to be processed.";
			}
			echo json_encode(array("status" => "error","msg"=>$msg,"link"=>$res['link']));
			exit();	
		}

		if (isset($result['id'])){
			$price = $result['price'];
			$Balances->handleSell($res['link'],$LTC,$price);
			for ($i=0;$i<count($LTCconfirmedDeposits);$i++){
				$Balances->setDisposed($LTCconfirmedDeposits[$i]['id']);
			}
			$msg = "Purchase made successfuly.";
			echo json_encode(array("status" => "success","msg"=>$msg,"link"=>$res['link']));
			exit();
		}
	}
}

if ($_POST['action']=="send-link-to-email"){

	if (!isset($_POST['email'])||!isset($_SESSION['link'])) die("bad params");
	$email = $_POST['email'];
	$headers = 'From: "Getcryptonow.com" no-reply@getcryptonow.com' . "\r\n";
    $headers .= "Content-Type: text/plain; charset=ISO-8859-1";  
    $title = 'Getcryptonow.com - Your link is here!';
   
    $content = "This is your link: ".SITE_URL."?link=".$_SESSION['link']['link']."\r\nSafekeep this link since its the representation of your account.";

    $flag=mail($email,utf8_decode($title),$content,$headers);
    if ($flag) return "ok";
    return "error";

}