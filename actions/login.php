<?php
include("../_config.php");
session_start();
$email = $_POST["email"];
$pass = $_POST["password"];
$users = new Users();
$args=["email"=>$email,"pass"=>$pass];
$res=$users->login($args);
if ($row = mysqli_fetch_assoc($res)){
	$_SESSION['name']=$row['name'];
    $_SESSION['id']=$row['id'];
    $_SESSION['email']=$row['email'];
	header("Location: ".SITE_URL);
}else{
	echo mysqli_fetch_assoc($res);
    header("Location :".SITE_URL."<enterbadloginview>");
    
		
}



?>