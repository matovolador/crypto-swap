<?php
class Balances {
	private $db;
	const CONCEPT_ARRAY = array(
			"A01" => "LTC Deposit.",
			"A02" => "Sending to exchange transaction fee.",
			"A03" => "LTC to USD conversion.",
			"A04" => "USD purchase.",
			"A05" => "Transfer to exchange.",
			"A06" => "Transfer to address.",
			"A07" => "USD spent.",
			"A08" => "getcryptonow.com fee.",
			"A09" => "Link Referral fee."
		);
	function __construct(){
		$this->db = new PDOdb();
	}

	public function get($id){
		$res = $this->db->request("SELECT * FROM balances WHERE id = ? LIMIT 1","select",[$linkID],true);
		if (!empty($res)) return $res;
		return false;
	}

	public function getByLink($linkID){
		$res = $this->db->request("SELECT * FROM balances WHERE link_id = ?","select",[$linkID]);
		if (!empty($res)) return $res;
		return false;

	}
	public function getByTx($tx){
		$res = $this->db->request("SELECT * FROM balances WHERE tx = ?","select",[$tx]);
		if (!empty($res)) return $res;
		return false;		
	}
	public function getInExchange($linkID){
		$res = $this->db->request("SELECT * FROM balances WHERE link_id = ? AND in_exchange = 1","select",[$linkID]);
		if (!empty($res)) return $res;
		return false;
	}
	public function getInServer($linkID){
		$res = $this->db->request("SELECT * FROM balances WHERE link_id = ? AND in_exchange = 0","select",[$linkID]);
		if (!empty($res)) return $res;
		return false;
	}
	public function getByCurrency($linkID,$currency){
		$res = $this->db->request("SELECT * FROM balances WHERE link_id = ? AND currency = ?","select",[$linkID,$currency]);
		if (!empty($res)) return $res;
		return false;
	}

	public function add($linkID,$currency,$amount,$concept_flag,$tx=NULL,$in_exchange=0,$disposed = 0,$address= NULL){
		if ($tx!=NULL && $tx!="-1"){
			$res = $this->db->request("SELECT * FROM balances WHERE tx = ? LIMIT 1","select",[$tx]);
			if (!empty($res)) return false;
		}
		
		$description = "";
		foreach (self::CONCEPT_ARRAY as $key => $value){
			if ($concept_flag == $key) $description = $value;
		}
		$res = $this->db->request("INSERT INTO balances (link_id,currency,amount,concept_flag, description,tx, in_exchange,disposed,time_created,to_address) VALUES (?,?,?,?,?,?,?,?,NOW(),?)","insert",[$linkID,$currency,$amount, $concept_flag,$description,$tx,$in_exchange,$disposed,$address]);
		return $res;
	}
	public function setDisposed($id){
		$res = $this->db->request("UPDATE balances SET disposed=1 WHERE id = ?","update",[$id]);
		return $res;		
	}
	public function setAmount($id,$amount){
		$res = $this->db->request("UPDATE balances SET amount=? WHERE id = ? ","update",[$amount,$id]);
		return $res;		
	}	
	public function setConfirmations($id,$confirmations){
		$res = $this->db->request("UPDATE balances SET confirmations=? WHERE id = ? ","update",[$confirmations,$id]);
		return $res;			
	}
	public function sendToExchange($linkID,$amount){
		$originalAmount = $amount;
		$txFee = 0.001;
		$GCNFee = $amount * 0.001;  //1%;
		if ($GCNFee<0.004) $GCNFee = 0.004;
		$referralFee = 0;
		$Links = new Links();
		$link = $Links->getLink($linkID);
		if(!$link) return false;
		$Litecoind = new Litecoind();
		
		//$this->add($linkID,"LTC",-$originalAmount,"A01","-1"); //deposit removal

		//check if link has referral:
		if ($link['ref_link']!=NULL){
			$GCNFee = $GCNFee / 2;
			$referralFee = $GCNFee;
			$refLink = $Links->getLink($link['ref_link']);
			//send referral fee:
			$res = $Litecoind->sendCoins($refLink['assigned_address'],$referralFee);

			if ($res && !isset($res['message'])) $this->add($linkID,"LTC",-$referralFee,"A09",$res,0);
		}
		//send GCN fee:
		$res = $Litecoind->sendFeesToCompany($GCNFee);
		if ($res && !isset($res['message'])) $this->add($linkID,"LTC",-$GCNFee,"A08",$res,0);

		//send to exchange
		$amount = $amount  - $GCNFee - $referralFee;
		$res = $Litecoind->sendCoins("LQD3EkQE5k1GftxHb6P31Ee7twikPU2vu6",$amount); //send coinds and create TX
		//Set records:
		if ($res && !isset($res['message'])){
			$this->add($linkID,"LTC",-$txFee,"A02",NULL,0,1); // TX fee
			$res = $this->add($linkID,"LTC",$amount - $txFee,"A05",$res,1); //transferece into exchange
			//reduce deposits:
			$res = $this->add($linkID,"LTC",-$originalAmount,"A01","-1");
		}
		
		return true;
	}

	public function getUSDBalance($link){
		$res = $this->db->request("SELECT * FROM balances WHERE link_id = ? AND currency = 'USD' AND (concept_flag = 'A04' OR concept_flag = 'A07') AND disposed = 0","select",[$link] );
		if (!empty($res)) return $res;
		return false;
	}
	public function getConfirmedLTC($link){
		$res = $this->db->request("SELECT * FROM balances WHERE link_id = ? ","select",[$link] );
		if (!empty($res)){
			$LTC = false;
			$h = 0;
			for ($i=0;$i<count($res);$i++){
				if ($res[$i]['disposed']!=1 && $res[$i]['in_exchange']!=1 && $res[$i]['concept_flag']=="A01" && ($res[$i]['confirmations']>=6 || $res[$i]['tx']=="-1")){
					$LTC[$h]=$res[$i];
					$h++;
				}

			}
			return $LTC;	
		} 
		return false;	
	}

	///REVIEW THIS WHEN REMAKING LTC SELL
	public function handleSell($link,$LTC,$price){
    	$res = $this->db->request("SELECT * FROM balances WHERE link_id= ?","select",[$link]);
    	if (!empty($res)){
    		$amountUSD = $LTC * $price * 0.999;
    		$res = $this->add($link,"LTC",-$LTC,"A03",NULL,0,1);
    		$res2 = $this->add($link,"USD",$amountUSD,"A04",NULL,1);
    		return $res && $res2;
    	}
    	return false;	
    }
}