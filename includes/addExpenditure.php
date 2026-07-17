<?php
include 'connect.php'; // Your DB connection

// Sanitize and assign form data
$sj_id              = $_POST['sjid'];
$pj_id = $_POST['pjid'];
$expenditure              = $_POST['expenditure'];
$exp_type              = $_POST['exp_type'];
$date               = date("Y-m-d", strtotime($_POST['date']));

// Prepare SQL insert
$stmt = $conn->prepare("INSERT INTO expenditure (
        sj_id, expenditure, exp_type , date_exp
    ) VALUES (?, ?, ?, ?)");

// Bind parameters (16 total)
$stmt->bind_param(
    "ssss",
    $sj_id,
    $expenditure,
    $exp_type,
    $date
);

// Execute and handle result
if ($stmt->execute()) {
    session_start();
    $_SESSION['status'] = "success";
    $_SESSION['message'] = "Expenditure updated";
} else {
    session_start();
    $_SESSION['status'] = "error";
    $_SESSION['message'] = "Failed to update : " . $stmt->error;
}

$stmt->close();
$conn->close();

// Redirect back (customize this URL as needed)
header("Location: ../project_details_formd.php?pages=update&id=$pj_id&sjid=$sj_id");
exit();
