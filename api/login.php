<?php
session_start();

require_once("../conf/conf.main.php");
require_once("lib/mysql.php");
$db = new DB(MYSQL_HOST, MYSQL_USERNAME, MYSQL_PASSWORD, MYSQL_DATABASE);
$db->connect();
$result = array();

$username = $db->cleanup($_GET['username']);
$password = $db->cleanup($_GET['password']);
$sessionid = md5($_COOKIE['PHPSESSID']);

$sq = "SELECT id FROM admins WHERE username = '$username' and password = '$password' and active=1";
$sqRes = $db->query($sq);

if (count($sqRes) == 1) {
    $uSq = "UPDATE admins set sessionid = '$sessionid' where username = '$username'";
    $uRes = $db->update($uSq);
    $_SESSION['username'] = $username;
    $result = array('username'=>$username);
}

echo json_encode($result, JSON_NUMERIC_CHECK);
?>