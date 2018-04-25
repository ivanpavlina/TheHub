<?php

require_once("conf/conf.main.php");
require_once('inc/audit.php');
require_once('lib/mysql.php');

function authorize_session() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    $sessionid = $_COOKIE['PHPSESSID'];
    $db = new DB(MYSQL_HOST, MYSQL_USERNAME, MYSQL_PASSWORD, MYSQL_DATABASE);
    $db->connect();

    logMessage("Connected to DB, authorizing sessionid [".$sessionid."]");

    $sessionid = $db->cleanup(md5($sessionid));

    $sq = "SELECT username FROM admins WHERE sessionid='$sessionid'";
    $result = $db->query($sq);

    logMessage("Auth session result >> " . "username => ". $result[0]->username);

    if (count($result) == 1) {
        $_SESSION['username'] = $result[0]->username;
        return True;
    }

    return False;
}