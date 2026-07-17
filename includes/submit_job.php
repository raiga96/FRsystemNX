<?php
include 'connect.php';

$sj_id = $_POST['sj_id'];
$pj_id = $_POST['pj_id'];
$uid = $_POST['uid'];


$sql7 = "UPDATE connector SET main_status = '1' WHERE sj_id = '$sj_id'";
$conn->query($sql7);

$sql3 = "INSERT INTO sj_submit_log (sj_id, submit_by) VALUES (?, ?)";
$stmt3 = $conn->prepare($sql3);
$stmt3->bind_param("ss", $sj_id, $uid);
$stmt3->execute();


session_start();


$_SESSION['status'] = 'success';
$_SESSION['message'] = "Submitted to Survey Branch";


header("Location: ../submit2sb.php");
exit();
