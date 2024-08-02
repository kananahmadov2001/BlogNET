<?php
// Content of database.php

$servername = "localhost";
$username = "kanan";
$password = "kanan2001";
$dbname = "news_db";

$mysqli = new mysqli($servername, $username, $password, $dbname);

if($mysqli->connect_errno) {
	printf("Connection Failed: %s\n", $mysqli->connect_error);
	exit;
}
?>