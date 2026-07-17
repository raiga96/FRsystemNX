<?php 
//NCR Section 18 Individual
$sqls18 = "SELECT COUNT(*) AS count FROM surveyjob WHERE sj_type = 'S18'";
$res18 = $conn -> query($sqls18);
$rows18 = $res18 -> fetch_assoc();
$count_s18 = $rows18['count'];

//NCR Section 6 Perimeter
$sqls6 = "SELECT COUNT(*) AS count FROM surveyjob WHERE sj_type = 'S6'";
$res6 = $conn -> query($sqls6);
$rows6 = $res6 -> fetch_assoc();
$count_s6 = $rows6['count'];
?>