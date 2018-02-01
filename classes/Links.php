<?php
class Links {
	private $db;
	function __construct(){
		$this->db = new PDOdb();
	}


	public function generateLink($amount,$assigned_address,$refLink=NULL){
		$currency = "LTC";
		//$amount = $this->proccessAmount($amount);
		
		//if (!$amount) return false;
		
		
		$key=$this->randKey();
		$res=$this->db->request("INSERT INTO links (currency,amount,link,assigned_address,ref_link) VALUES (?,?,?,?,?)","insert",[$currency,$amount,$key,$assigned_address,$refLink]);
		$link = $this->db->request("SELECT * FROM links WHERE link = ? LIMIT 1","select",[$key],true);
		return $link;
	}
	public function getLink($link){
        $res = $this->db->request("SELECT * FROM links WHERE link = ? LIMIT 1","select",[$link],true);
        if (!empty($res)) return $res;
        return false;
    }
	public function getLinkByAddress($address){
		$res = $this->db->request("SELECT * FROM links WHERE assigned_address = ? LIMIT 1","select",[$address],true);
		if (!empty($res)) return $res;
		return false;
	}
	public function getLinkWithPin($link,$pin){
        $pin = md5($pin);
        $res = $this->db->request("SELECT * FROM links WHERE link = ? AND pin=? LIMIT 1","select",[$link,$pin],true);
        if (!empty($res)) return $res;
        return false;
    }
	public function updateConfirmations($link,$confirmations){
		$res = $this->db->request("UPDATE links SET confirmations=? WHERE link=? LIMIT 1","update",[$confirmations,$link]);
		return $res;
	}
	
    public function createPIN($link,$pin){
        $pin = md5($pin);
        $res = $this->db->request("UPDATE links SET pin=? WHERE link = ? LIMIT 1","update",[$pin,$link]);
        if ($res) return $pin;
        return false;
    }
    
    public function enterPIN($pin,$link){
        $pin = md5($pin);
        $res = $this->db->request("SELECT * FROM links WHERE pin= ? AND link = ? LIMIT 1","select",[$pin,$link],true);
        if (!empty($res)) return $res;
        return false;
    }

    public function changePIN($pin,$link,$newPin){
        $pin = md5($pin);
        $newPin = md5($newPin);
        $res = $this->db->request("UPDATE links SET pin=? WHERE link = ? AND pin = ? LIMIT 1","update",[$newPin,$link,$pin]);
        if ($res) return $newPin;
        return false;   
    }

    public function linkCashout($link,$pin){
        $pin = md5($pin);
    	$res = $this->db->request("SELECT * FROM links WHERE link = ? AND pin = ? LIMIT 1","select",[$link,$pin],true);
    	if (!empty($res)) return $res;
    	return false;
    }
    

	 public function randKey(){
        $randKey=Utils::getRandKey(20);
        $res=$this->db->request("SELECT * FROM links WHERE link=? LIMIT 1","select",[$randKey],true);
        while(!empty($res)){
            $randKey=Utils::getRandKey(20);
            $res=$this->request("SELECT * FROM links WHERE link=? LIMIT 1","select",[$randKey],true);
        }
        return $randKey;
    }

        

}