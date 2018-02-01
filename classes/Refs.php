<?php
class Refs{
	private $db;

	function __construct(){
		$this->db = new PDOdb();
	}

	public function registerRef($ref){
		$flag = false;
		$date = date('Y-m-d H:i:s');
		$ip = $this->getUserIP();
		$this->db->request("INSERT INTO refs (ref,ip,date_created) VALUES (?,?,?)","insert",[$ref,$ip,$date]);
		return $flag;
	}

	public function getRefs(){
		$res = $this->db->request("SELECT * FROM refs ORDER BY id DESC","select");
		return $res;
	}
	private function getUserIP(){
	    $client  = @$_SERVER['HTTP_CLIENT_IP'];
	    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
	    $remote  = $_SERVER['REMOTE_ADDR'];

	    if(filter_var($client, FILTER_VALIDATE_IP))
	    {
	        $ip = $client;
	    }
	    elseif(filter_var($forward, FILTER_VALIDATE_IP))
	    {
	        $ip = $forward;
	    }
	    else
	    {
	        $ip = $remote;
	    }

	    return $ip;
	}

}