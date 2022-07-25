<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ob_start();
session_start();

//header("Content-Type: text/html;charset=UTF-8");

date_default_timezone_set('Asia/Kolkata');

if ($_SERVER['HTTP_HOST'] == "localhost" or $_SERVER['HTTP_HOST'] == "192.168.29.85" or $_SERVER['HTTP_HOST'] == "192.168.1.105") {
	//local  

	DEFINE('DB_USER', 'root');
	DEFINE('DB_PASSWORD', '');
	DEFINE('DB_HOST', 'localhost'); //host name depends on server
	DEFINE('DB_NAME', 'db_lableiz');
} else {
	//local live 

	DEFINE('DB_USER', 'delifood_dblabeliz');
	DEFINE('DB_PASSWORD', 'csvsV2%xKDvY');
	DEFINE('DB_HOST', 'localhost'); //host name depends on server
	DEFINE('DB_NAME', 'delifood_dblabeliz');
}

//DEFINE('APP_GCM_KEY', 'AAAAi3Wpuwc:APA91bFEd0OGdT9EAUt70bkhEFV_CLjNvxZbAPTyVgsWKKuectjEOgKWLBDNEAN1xvgTUjCTj59PWdJLGmQKuVupeKTnG7XshRZcKohb7zj3acW3T7Ll2zlAGeNK2fQ6BakbUHpajIVI');


$mysqli = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if ($mysqli->connect_errno) {
	echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
} else {
	//echo "connected";
}

mysqli_query($mysqli, "SET NAMES 'utf8'");
