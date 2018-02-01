<?php

class Litecoind {
	private $account ;
	private $jsRPCClient;
	function __construct(){
		$this->account = "<enter litecoin account name>";
		$this->jsonRPCClient = new jsonRPCClient('<enter jsonRPCClient credentials>');


	}
	public function getBalance(){
		$result = $this->jsonRPCClient->getbalance();
		return $result;
	}
	public function getAccountBalance(){
		$result = $this->jsonRPCClient->getbalance($this->account);
		return $result;
	}
	public function getAccountAddresses(){
		$result = $this->jsonRPCClient->getaddressesbyaccount($this->account);
		return $result;
	}	

	public function createNewAddress(){
		$result = $this->jsonRPCClient->getnewaddress($this->account);
		return $result;
	}

	public function getTransaction($txId){
		$result = $this->jsonRPCClient->gettransaction($txId);
		if (isset($result['message'])) return false;
		return $result;
	}
	public function getTransactionConfirmations($tx){
		$result = $this->getTransaction($tx);
		if (!$result) return false;
		return $result['confirmations'];	
	}
	public function getTransactionAmount($tx){
		$result = $this->getTransaction($tx);
		if (!$result) return false;
		return $result['amount'];	
	}

	public function validateTransaction($tx,$targetAddress){
		$result = $this->getTransaction($tx);
		if (!$result) return false;
		if ( $result['details'][0]['address'] == $targetAddress){
			return true;
		}else{
			return false;
		}
	}

	public function sendCoins($targetAddress,$amount){
		$result = $this->jsonRPCClient->sendtoaddress($targetAddress,$amount - 0.001 );
		return $result;	
		
		
	}

	public function getAddresses(){
		$result = $this->jsonRPCClient->getaddressesbyaccount($this->account);
		return $result;

	}
	public function getReceivedByAddress($address){
		$result = $this->jsonRPCClient->getreceivedbyaddress($address);
		return $result;
	}
	public function listReceivedByAddress(){
		$result = $this->jsonRPCClient->listreceivedbyaddress(0,true);
		return $result;
	}

	public function getInfoByAddress($address){
		$h=0;
		$flag = false;
		$list = $this->listReceivedByAddress();
		while($h<count($list) && !$flag){
			if ($address  == $list[$h]['address']){
				$received['address'] = $address;
				$received['amount'] = $list[$h]['amount'];
				$received['confirmations'] = $list[$h]['confirmations']; 
				$received['txids'] = $list[$h]['txids'];
				$flag = true;
			}
			$h++;
		}
		if ($flag){
			return $received;
		}else{
			return false;
		}
		
	}
	public function getDepositsByAddress($address){
		$res = $this->getInfoByAddress($address);
		$received = null;
		for ($h=0;$h<count($res['txids']);$h++){
			$out = $this->getTransaction($res['txids'][$h]);
			for ($i=0;$i<count($out['details']);$i++){
				if ($out['details'][$i]['category']=="receive"){
					$received[$h]=array("amount"=>$out['details'][$i]['amount'],"confirmations"=>$out['confirmations'],"tx"=>$res['txids'][$h]);
					
				}
			}
			
		}
		return $received;
	}
	public function sendFeesToCompany($amount){
		$res = $this->sendCoins("<enter litecoin address to recieve fee gains>",$amount);
		return $res;
	}
}