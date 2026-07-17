<?php 
//Finding User
$sqlRole = "SELECT * FROM userinfo WHERE uid = '".$_SESSION['uid']."'";
$resultRole = $conn1 -> query($sqlRole);
$rowRole = $resultRole -> fetch_assoc();
$userDiv = $rowRole["division"];
$sect = $rowRole["section"];

//Determine user Division
$sqlDiv = "SELECT * FROM division WHERE DIV_ID = '$userDiv'";
$resultDiv = $conn1 -> query($sqlDiv);
$rowDiv = $resultDiv -> fetch_assoc();
$divName = $rowDiv["DIV_NAME"];


//Determine user branch / section
$sqlSect = "SELECT * FROM brasecunit WHERE BSU_id = '$sect'";
$resultSect = $conn1 -> query($sqlSect);
$rowSect = $resultSect -> fetch_assoc();
$section = $rowSect["BSU"];
?>