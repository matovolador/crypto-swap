<?php

class Articles{
	private $db;
	function __construct(){
		$this->db = new PDOdb();
	}

	public function getByBlogger($blogger_id){
		$res = $this->db->request("SELECT * FROM articles WHERE blogger_id=?","select",[$blogger_id]);
		return $res;
	}

	public function add($title,$body,$author,$blogger_id){
		$this->db->request("INSERT INTO articles (title,body,author,blogger_id) VALUES (?,?,?,?)","insert",[$title,$body,$author,$blogger_id]);
		return;
	}

	public function get($id){
		$res = $this->db->request("SELECT * FROM articles WHERE id= ? ","select",[$id]);
		return $res;
	}
	public function edit($id,$body){
		$this->db->request("UPDATE artciles SET body = ? WHERE id=?","update",[$body,$id]);
		return;
	}
	public function delete($id){
		$this->db->request("DELETE FROM artciles WHERE id=?","delete",[$id]);
		return;	
	}

}