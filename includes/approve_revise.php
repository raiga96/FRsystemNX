<?php
include 'connect.php';

if (isset($_POST['approve'])) {
    $rev_id = $_POST['rev_id'];
    $uid = $_POST['uid'];
    $id = $_POST['id'];
    $sjid = $_POST['sjid'];
    $appdate = date("Y-m-d");


    $sql7 = "UPDATE revise SET approve = 'Y', approve_date = '$appdate', approve_by = '$uid' WHERE rev_id = '$rev_id'";
    $conn->query($sql7);


    session_start();


    $_SESSION['status'] = 'success';
    $_SESSION['message'] = "Revise Date Approve";


    header("Location: ../project_details_formd.php?pages=update&id=" . $id . "&sjid=" . $sjid);
    exit();
} else if (isset($_POST['reject'])) {

    $rev_id = $_POST['rev_id'];
    $uid = $_POST['uid'];
    $id = $_POST['id'];
    $sjid = $_POST['sjid'];
    $appdate = date("Y-m-d");


    $sql7 = "UPDATE revise SET approve = 'Y', approve_date = '$appdate', approve_by = '$uid' WHERE rev_id = '$rev_id'";
    $conn->query($sql7);


    session_start();


    $_SESSION['status'] = 'success';
    $_SESSION['message'] = "Revise Date Rejected";


    header("Location: ../project_details_formd.php?pages=update&id=" . $id . "&sjid=" . $sjid);
    exit();
}
