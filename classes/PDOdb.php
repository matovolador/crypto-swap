<?php

class PDOdb {
	private $pdo = null;
	private $stm= null;
	function __construct(){
		$dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8";
		$opt = array(
		    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
		    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
		);
		$this->pdo = new PDO($dsn,DB_USER,DB_PASS, $opt);
	}

	public function request($sql,$type,$params=FALSE,$oneRow=FALSE){
		if ($params == FALSE){
			$this->stm = $this->pdo->prepare($sql);
			$this->stm->execute();
			if ($type!="select") return $this->affectedRows();
			if ($oneRow) {
				$res= $this->stm->fetch();		
			}else{
				$res= $this->stm->fetchAll();	
			}
			if (empty($res)) return false;
			return $res;
			
			
			
		}else{
			$this->stm = $this->pdo->prepare($sql);
			$this->stm->execute($params);
			if ($type!="select") return $this->affectedRows();
			if ($oneRow) {
				$res= $this->stm->fetch();		
			}else{
				$res= $this->stm->fetchAll();	
			}
			if (empty($res)) return false;
			return $res;
		}
		
	}

	public function affectedRows(){
		if($this->stm->rowCount()>0) return true;
		return false;
	}

	public function close(){
		
	}

}

