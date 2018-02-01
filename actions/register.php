<?php
include("../_config.php");
session_start();
$name = $_POST['name'];
$email = $_POST["email"];
$pass = $_POST["password"];

$users = new Users();
$flag = $users->validateUserName($name);
$flag2 = $users->validateEmail($email);
$flag3 = $users->validatePassword($pass);

if ($flag == null and $flag2 == null and $flag3 == null){
	$users->create(["name"=> $name,"email"=> $email,"pass" => $pass]);
	$args=["email"=>$email,"pass"=>$pass];
	$res=$users->login($args);
	if ($row = $res->fetch_assoc()){
	    $_SESSION['name']=$row['name'];
    	$_SESSION['id']=$row['id'];
    	$_SESSION['email']=$row['email'];
	    header("Location: ".SITE_URL);
	}else{
		echo "BAD LOGIN IN Users.php";
	}
}else{
	echo "Errors: ". print_r($flag) ." - " . print_r($flag2) . " - " . print_r($flag3);
	//TODO HANDLE ERRORS VIEW:
	//header("Location: ......");
}

?>