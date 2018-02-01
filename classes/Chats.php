<?php

    define("ROOM_SIZE",2);
    define("FILE_TYPE",".html");
    define("CIPHER_PASSWORD" ,"ASdajghq3h_1234jh13dafwEF");    
    define("CIPHER_IV",substr(hash("sha256","AS325sdf_Adfaqe3_ADf-234"),0,16));
    define("CIPHER_METHOD","aes-256-cbc");
class Chats {
	public $db;
    private $iv;
    private $password;
    
	function __construct(){
		$this->db = new PDOdb();
        $this->iv = "";
        $this->password="";
	}

    public function createRoom(){
        $key=$this->generateRoomKey();
        $this->createRoomFile($key);
        return $key;
    }

    public function logIntoRoom($key,$userName){
        $username = $userName;
        $room = $this->roomExists($key);
        if ($room){
            $names = $this->getRoomUserNames($key);
            $flag = false;
            for ($i=0;$i<count($names);$i++){
                if ($names[$i]==$username) $flag = true;
            }
            if ($flag) return "Error: That username is already taken for this room";
            $res = $this->db->request("SELECT num_users FROM chats WHERE room_key = ? LIMIT 1","select",[$key],true);

            if (!empty($res)){
                $res = $res['num_users'];
                if ($res>=ROOM_SIZE) return "That room is full.";
                if ($res==1) $username = ",".$username;
            }
            $result = $this->db->request("UPDATE chats SET user_names = CONCAT(user_names, ?) , num_users = num_users +1 WHERE room_key=?","update",[$username,$key]);
            if ($result){
                //perform room login:
                $_SESSION['room_key'] = $key;
                $_SESSION['room_username'] = $userName;
                return "ok";    
            }else{
                return "Error: 507";
            }
            

        }else{
            return "Error: That room doesn't exist";
        }
    }

    public function writeToChat($key,$content,$decrypt=true){
        $chat = "";
        if ($decrypt == true) $chat = $this->decryptChat($key);
        $chat = $chat.$content;
        $chat = $this->encryptChat($chat);
        if($chat != false){
            $flag=file_put_contents("../chatfiles/".$key.FILE_TYPE,$chat);
            return $flag;
        }else{
            return false;
        }
        
    }
    public function readChat($key){
        $chat = $this->decryptChat($key);
        return $chat;
    }

    public function roomExists($key){
        $result = $this->db->request("SELECT * FROM chats WHERE room_key = ? LIMIT 1","select",[$key],true);
        if (!empty($result)) return true;
        return false;
    }

    public function addRoomUserName($key,$username){
        $names = $this->db->request("SELECT user_names FROM chats WHERE room_key = ? LIMIT 1","select",[$key],true);
        //if (empty($names))
    }

    public function getRoomUserNames($key){
        $result = $this->db->request("SELECT user_names FROM chats WHERE room_key=? LIMIT 1","select",[$key],true);
        if (!empty($result)){
            $result = $result['user_names'];
            $result = explode ( "," , $result); 
            return $result;    
        }else{
            return false;
        }
        
    }

	public function generateRoomKey(){
		$key=$this->randKey();
		$res=$this->db->request("INSERT INTO chats (room_key,num_users) VALUES (?,0)","insert",[$key]);
		return $key;
	}

    public function createRoomFile($key){
        $fp = fopen("../chatfiles/".$key.FILE_TYPE,"w");
        fclose($fp);
        //$this->createCredentials();
        $content = "<b>Room Key: ".$key."</b><br />";
        $flag=$this->writeToChat($key,$content,false);
        
        return $flag;
    }

	public function randKey(){
        $randKey=Utils::getRandKey();
        $res=$this->db->request("SELECT * FROM chats WHERE room_key=? LIMIT 1","select",[$randKey],true);
        while(!empty($res)){
            $randKey=Utils::getRandKey();
            $res=$this->request("SELECT * FROM chats WHERE room_key=? LIMIT 1","select",[$randKey],true);
        }
        return $randKey;
    }
        
    
    public function encryptChat($chat){
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(CIPHER_METHOD));
        
        return openssl_encrypt ( $chat , CIPHER_METHOD , hash('sha256',CIPHER_PASSWORD) , 0  , CIPHER_IV);
        
    }

    public function decryptChat($key){
        $data = file_get_contents("../chatfiles/".$key.FILE_TYPE);
        $iv_size = openssl_cipher_iv_length(CIPHER_METHOD);
        //$iv_size = 16;
        $iv = substr($data,0,$iv_size);
        //return "IV: ".$iv . " PASSWORD: ".hash("sha256",CIPHER_PASSWORD);
        $chat = openssl_decrypt ( $data , CIPHER_METHOD ,hash('sha256',CIPHER_PASSWORD), 0 , CIPHER_IV );
        
        if (!$chat){
            return false;
        }else{
            return $chat;
        }
        
    }


}