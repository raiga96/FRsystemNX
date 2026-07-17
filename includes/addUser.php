<?php
require 'connect.php'; // your DB file
session_start();

// ---------------------------------------------------------------------
// 1. Validate POST
// ---------------------------------------------------------------------
if (!isset($_POST['uid'])) {
    die("Invalid request.");
}

// Get POST values safely
$uid            = $_POST['uid'];


// Basic validation
$sqlUserCheck = "SELECT * FROM userinfo WHERE uid = '$uid'";
$resUserCheck = $conn1->query($sqlUserCheck);
$rows = $resUserCheck->fetch_assoc();
$branch = $rows['section'];

// ---------------------------------------------------------------------
// 2. UPDATE userinfo table inside COMMANDS database
// ---------------------------------------------------------------------
$sql = "INSERT INTO userinfo (uid, branch) 
        VALUES (?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $uid, $branch);
$stmt->execute();
$stmt->close();


// ---------------------------------------------------------------------
// 3. CREATE system_access entry inside COMMANDS database
// ---------------------------------------------------------------------
$sqlAccess = "INSERT INTO system_access (uid, role_id, permission, access_level) 
              VALUES (?, null, '1', 1)";
$stmtAccess = $conn->prepare($sqlAccess);
$stmtAccess->bind_param("i", $uid);
$stmtAccess->execute();
$stmtAccess->close();

// ---------------------------------------------------------------------
// 4. Redirect back with success message
// ---------------------------------------------------------------------
// $_SESSION['success'] = "Account updated successfully!";
$_SESSION['status'] = 'success';
$_SESSION['message'] = 'Account added successfully!';
header("Location: ../accManagement.php");
exit;
