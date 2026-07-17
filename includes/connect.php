<?php

require 'x.php';
$servername = $host;
$dBUsername = $user;
$dBPassword= $password;
$dBName = $db;
$dbName1 = "easy";

$conn = mysqli_connect($servername, $dBUsername, $dBPassword, $dBName);


if($conn->connect_error){
  die("Connection failed: ".mysqli_connect_error());
}
$conn1 = mysqli_connect($servername, $dBUsername, $dBPassword, $dbName1);

if($conn1->connect_error){
	die("Connection failed: ".mysqli_connect_error());
}

?>