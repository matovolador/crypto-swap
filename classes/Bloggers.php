<?php
session_start();
class Bloggers{
	private $db;

	function __construct(){
		$this->db = new PDOdb();
	}

	public function register($username,$email,$password){
		$password = md5($password);
		$res = $this->db->request("SELECT * FROM bloggers WHERE email=? LIMIT 1","select",[$email]);
		if ($res){
			return false;
		}
		$this->db->request("INSERT INTO bloggers (username,email,password) VALUES (?,?,?)","insert",[$username,$email,$password]);
		return true;

	}

	public function login($email,$password){
		$password = md5($password);
		$res = $this->db->request("SELECT * FROM bloggers WHERE email=? AND password = ? AND authorized = 1 LIMIT 1","select",[$email,$password],true);
		if ($res){
			$_SESSION['blogger_id'] = $res['blogger_id'];
			$_SESSION['blogger_username'] = $res['username'];
		}
		return $res;
	}
}