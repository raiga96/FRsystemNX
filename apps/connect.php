<?php

$servername = "localhost";
$dBUsername = "root";
$dBPassword= "KataLaluan4kik@JTS";
//$dBPassword= "";
$dBName = "commands";
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