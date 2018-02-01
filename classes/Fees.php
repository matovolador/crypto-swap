<?php
class Fees(){
	private $rate;
	private $tx_fee;
	privaet $db;
	function __construct(){
		$this->db = new PDOdb();
		$this->rate = RATES;
		$this->tx_fee = TX_FEE;
	}

	//Adds a record of a business fee ( does not include tx_fee)
	public function createFee(){
		$res = $this->db->request("INSERT INTO fees (fee) VALUES (?)","insert",[$this->rate]);
		return $res;
	}
}