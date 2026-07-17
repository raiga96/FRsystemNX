<?php 
$sqlHR = "SELECT COUNT(*) as COUNT FROM userinfo WHERE section IN ('2','9','8','18','17')";
$resHR = $conn1 -> query($sqlHR);
$rowHR = $resHR -> fetch_assoc();
$hr_count = $rowHR['COUNT'];
?>