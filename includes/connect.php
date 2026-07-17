<?php

require 'x.php';
$servername = $host;
$dBUsername = $user;
$dBPassword= $password;
$dBName = $db;

$conn = mysqli_connect($servername, $dBUsername, $dBPassword, $dBName);

if($conn->connect_error){
  die("Connection failed: ".mysqli_connect_error());
}

?>