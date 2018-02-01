<?php
set_time_limit(1200000);
/*-----------------
 * IMPORTANT!!!
 * configure these settings for your own purpose
 */

error_reporting(-1);
ini_set('display_errors', true);
define('DB_HOST', "localhost");
define('DB_NAME', "getcryptonow");
define('DB_USER', "root");
define('DB_PASS', "secret");
define("SITE_URL","http://".$_SERVER["HTTP_HOST"]."/BitCoin/");


/*
error_reporting(E_ERROR);
ini_set('display_errors', false);
define('DB_HOST', "localhost");
define('DB_NAME', "getcryptonow");
define('DB_USER', "matias");
define('DB_PASS', "garfunkel_1548A");
define("SITE_URL","https://".$_SERVER["HTTP_HOST"]."/");
*/
//------------------------------------------


define("HAS_SSL",false);

define("BITFINEX_API_KEY","<enter bitfinex api key>");
define("BITFINEX_API_SECRET","<enter bitfinex api secret>");

define("TX_FEE",0.001);
define("RATES",0.0005);

include_once("classes/Routes.php");
include_once("classes/PDOdb.php");
include_once("classes/Utils.php");
include_once("classes/Users.php");
include_once("classes/Bitfinex.php");
include_once("classes/jsonRPCClient.php");
include_once("classes/Litecoind.php");
include_once("classes/Links.php");
include_once("classes/Chats.php");
include_once("classes/Orders.php");
include_once("classes/Balances.php");
include_once("classes/Refs.php");




?>