<?php
$servername = "localhost";
$username = "u148988291_ctrike"; // Change username
$password = "CTRIKE21ab."; // Change password
$dbname = "u148988291_ctrike"; // Change database name

$mysqli = new mysqli($servername, $username, $password, $dbname);

if ($mysqli->connect_error) {
	die("Connection failed: " . $mysqli->connect_error);
}
