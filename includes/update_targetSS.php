<?php
include 'connect.php';

$uid = $_POST['uid'];
$id = $_POST['id'];
$sjid = $_POST['sjid'];

//
$distance = $_POST['distance'];
$area = $_POST['area'];
$lot = $_POST['lot'];


$sql7 = "UPDATE survey_details SET distance = '$distance', target_lot = '$lot', target_area = '$area' WHERE project_id = '$id' AND sj_id = '$sjid' AND sd_type = 'SJ'";
$conn->query($sql7);


session_start();


$_SESSION['status'] = 'success';
$_SESSION['message'] = "Target Output Updated";


header("Location: ../project_details_formd.php?pages=update&id=" . $id . "&sjid=" . $sjid);
exit();
