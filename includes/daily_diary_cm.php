<?php
include 'connect.php'; // Your DB connection

if (isset($_POST['submit'])) {
    // Sanitize and assign form data
    $sj_id              = $_POST['sj_id'];
    $pj_id              = $_POST['pj_id'];
    $date               = $_POST['date'];
    $status             = $_POST['status'];
    $alasan             = $_POST['alasan'];
    $perolehan_harian   = $_POST['perolehan_harian'];
    $perimeter          = $_POST['perimeter'];
    $keluasan           = $_POST['keluasan'];
    $lot                = $_POST['lot'];
    $percentage         = $_POST['percentage'];
    $sarapeg_diguna     = $_POST['sarapeg_diguna'];
    $cassini_upgraded   = $_POST['cassini_upgraded'];
    $pc_upgraded        = $_POST['pc_upgraded'];
    $c2c                = $_POST['c2c'];
    $remarks            = $_POST['remarks'];

    $dateDBCreated      = date("Y-m-d H:i:s"); // Current timestamp

    // Prepare SQL insert
    $stmt = $conn->prepare("INSERT INTO daily_diary (
        sj_id, date, status, alasan, perolehan_harian, perimeter, keluasan, lot,
        percentage, sarapeg_diguna, cassini_upgraded, pc_upgraded, c2c, remarks, 
        dateRecorded, dateDBCreated
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    // Bind parameters (16 total)
    $stmt->bind_param(
        "ssssssssssssssss",
        $sj_id,
        $date,
        $status,
        $alasan,
        $perolehan_harian,
        $perolehan_harian,
        $area,
        $lot,
        $percentage,
        $sarapeg_diguna,
        $cassini_upgraded,
        $pc_upgraded,
        $c2c,
        $remarks,
        $date,
        $dateDBCreated
    );

    // Execute and handle result
    if ($stmt->execute()) {
        session_start();
        $_SESSION['status'] = "success";
        $_SESSION['message'] = "Daily diary entry has been successfully added.";
    } else {
        session_start();
        $_SESSION['status'] = "error";
        $_SESSION['message'] = "Failed to insert daily diary entry: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

    // Redirect back (customize this URL as needed)
    header("Location: ../dailyDiary.php?pages=lapi&id=$pj_id&sjid=$sj_id");
    exit();
}
