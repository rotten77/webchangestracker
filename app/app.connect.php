<?php
include dirname(__FILE__) . "/lib/NotORM.php";
try {
	$connection = new PDO("mysql:dbname=".MYSQL_DB.";host=".MYSQL_HOST.";", MYSQL_USER, MYSQL_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
	$connection->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
	// $connection->debug = true;
	$db = new NotORM($connection);
} catch (PDOException $e) {
	header('HTTP/1.1 503 Service Temporarily Unavailable');
	header('Status: 503 Service Temporarily Unavailable');
	header('Retry-After: 3600');
	header('Cache-Control: no-cache, must-revalidate');
	header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

	die('App is temporarily unavailable');
}