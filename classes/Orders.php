<?php
class Orders {
	private $BF;
	private $db;
	function __construct(){
		$this->BF = new Bitfinex(BITFINEX_API_KEY,BITFINEX_API_SECRET);
		$this->db = new PDOdb();

	}
	public function add($orderID,$linkID,$symbol,$side,$amount,$price,$address){
		$res=$this->db->request("INSERT INTO orders (order_id,link_id,symbol,side,amount,price,to_address) VALUES (?,?,?,?,?,?,?)","insert",[$orderID,$linkID,$symbol,$side,$amount,$price,$address]);
		return $res;
	}
	public function get($orderID){
		$res = $this->db->request("SELECT * FROM orders WHERE order_id=? LIMIT 1","select",[$orderID],true);
		if (empty($res)) return false;
		return $res;
	}
	public function getByLink($linkID,$getDisposed=true){
		if ($getDisposed){
			$res = $this->db->request("SELECT * FROM orders WHERE link_id=?","select",[$linkID]);	
		}else{
			$res = $this->db->request("SELECT * FROM orders WHERE link_id=? AND disposed=0","select",[$linkID]);
		}
		
		if (empty($res)) return false;
		return $res;	
	}
	public function queryExchange($orderID){
		$res=$this->BF->get_order(intval($orderID));
		if (empty($res) || $res['error']==1) return false;
		return $res;
		
	}

	public function dispose($orderID){
		$res = $this->db->request("UPDATE orders SET disposed=1 WHERE order_id = ?","update",[$orderID]);
		return $res;
	}

	//UTILITIES:
	public function calculateLimitPrice($usd,$symbol){
		$symbol = strtoupper($symbol);
		$orders = $this->BF->get_book($symbol);
		$asks = $orders['asks'];
		for ($i = 0;$i<count($asks);$i++){

			if ($usd < $asks[$i]['price'] * $asks[$i]['amount']){
				$amountCoins = $usd / $asks[$i]['price'];
				if ($amountCoins <= $asks[$i]['amount']){
					return array(
						"price" => $asks[$i]['price'],
						"amount" => $amountCoins
						);
				}	
			}
			
		}
	}

	public function getAveragePrice($symbol){
		$result=$this->BF->get_ticker($symbol);
		if (isset($result['mid'])) return $result['mid'];
		return false;
	}
}