<?php
include("../_config.php");
session_start();
$_SESSION[$_POST['link']]['page_index']=$_POST['index'];
if (isset($_POST['backIndex']) && $_POST['backIndex']!=null) $_SESSION[$_POST['link']]['backIndex'] = intval($_POST['backIndex']);
unset($_SESSION['script_status']);
unset($_SESSION['script_message']);
exit();