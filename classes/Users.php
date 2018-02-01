<?php
class Users {
	public $db;
	function __construct(){
		$this->db = new PDOdb();
	}

	//$args["name"=>x,"pass"=>x,"email"=>x]
    public function create ($args){
        $name = $args["name"];
        $pass = md5($args["pass"]);
        $email = $args["email"];
        $res=$this->db->request("INSERT INTO users (name,password,email) VALUES (?,?,?)","insert",[$name,$pass,$email]);
        return $res;
    }
    
    
    //$args["email"=>x,"pass"=>x]
    public function login($args){
        $email=$args["email"];
        $pass=md5($args["pass"]);
        $res=$this->db->request("SELECT * FROM users WHERE email = ? AND password = ?","select",[$email,$pass]);
        return $res;
    }
    
    
    public function setRandPass($email){
        $randPass=$this->randKey();
        $this->db->request("UPDATE users SET randPass=?,password=? WHERE email='$email'","update",[$randPass,$randPass]);
        //TODO SEND EMAIL WITH randPass
    }

    //TODO make func to log with randkey

    public function changePassword($pass){
        $pass = md5($pass);
        $id = $_SESSION['id'];
        $this->db->request("UPDATE users SET randPass='',password=? WHERE id=?","update",[$pass,$id]);
    }


    public function randKey(){
        $randKey=Utils::getRandKey();
        $res=$this->request("SELECT * FROM users WHERE (password=$randKey OR randPass=$randKey)","select");
        while(!empty($res)){
            $randKey=Utils::getRandKey();
            $res=$this->request("SELECT * FROM users WHERE (password=$randKey OR randPass=$randKey)","select");
        }
        return $randKey;
    }
        
    

	public function validateUserName($name){
		$error = null;
		$res = $this->db->request("SELECT * FROM users WHERE name=?","select",[$name]);
		if (!empty($res)){
			$error = "That name is already taken.";
			return $error;
		}
        if (ctype_alnum($name)){
            if (strlen($name) <= 15 && strlen($name) >= 8){
                return $error;
            }else{
                $error="Name lenght must be between 8 and 15 characters.";
            }
        }else{
            $error="Name must be alphanumeric.";
            
        }
        return $error;
		
	}

	public function validateEmail($email){
		$error = null;

		$flag=filter_var($email, FILTER_VALIDATE_EMAIL);
		if ($flag==false){
			$error = "Email not valid";
			return $error;
		}

		$res = $this->db->request("SELECT * FROM users WHERE email=?","select",[$email]);
		if (!empty($res)){
			$error = "That email is already taken.";
			return $error;
		}

		return $error;  //IF NULL, EMAIL IS VALID.
	}
	
	public function validatePassword($pass) {
	    $errors = null;

	    if (strlen($pass) < 8) {
	        $errors[] = "Password too short!";
	    }

	    if (!preg_match("#[0-9]+#", $pass)) {
	        $errors[] = "Password must include at least one number!";
	    }

	    if (!preg_match("#[a-zA-Z]+#", $pass)) {
	        $errors[] = "Password must include at least one letter!";
	    }     

	    return $errors;
	}

	public function resetPassword($email){
		$res = $this->db->request("SELECT * FROM users WHERE email = ?","select",[$email]);
		if (!empty($res)){
			$user = new User();
			$user->setRandPass($email);
			return true;
		}else{
			return false;
		}	
	}

	public function setNewPassword($pass){
		if (!checkSession()) return false;
		if (checkPassword($pass)){
			$user = new User();
			$user->changePassword($pass);
			return true;
		}else{
			return false;
		}
	}

	public function checkSession(){
		if (isset($_SESSION)) return true;
		return false;
	}

	public function getUsers(){
        $res = $this->db->request("SELECT * FROM users","select");
        return $res;
    }

    public function getUserById($id){
        $res = $this->db->request("SELECT * FROM users WHERE id = ?","select",[$id]);
        return $res;
    }

}



?>