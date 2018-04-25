<?php
require_once('conf/conf.main.php');

session_start();

require_once("lib/mysql.php");
$db = new DB(MYSQL_HOST, MYSQL_USERNAME, MYSQL_PASSWORD, MYSQL_DATABASE);
$db->connect();
$username = $db->cleanup($_SESSION['username']);
$uSq = "UPDATE admins set sessionid = '' where username = '$username'";
$uRes = $db->update($uSq);

//setcookie('PHPSESSID', '', -3600, '/');
session_destroy();

ob_start();
header('Location: /login.php');
ob_end_flush();

