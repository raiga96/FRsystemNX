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
$name           = $_POST['user_id'];
$fullname       = $_POST['fullname'];
$ldap_login     = $_POST['ldap_login'];
$acc_status     = $_POST['acc_status'];
$email          = $_POST['user_email'];
$branch_id      = $_POST['user_branch'];
$section_id     = $_POST['user_section'] ?? null;
$role_id        = $_POST['user_role'];
$user_division  = $_POST['user_division'];

// Basic validation
if ($uid == "" || $fullname == "" || $email == "") {
    die("Missing required fields.");
}

// ---------------------------------------------------------------------
// 2. UPDATE userinfo table inside COMMANDS database
// ---------------------------------------------------------------------
$sql = "UPDATE userinfo 
        SET role_id = ?, branch = ?, section = ?, ldap = ?
        WHERE uid = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("issss", $role_id, $branch_id, $section_id, $ldap_login, $uid);
$stmt->execute();
$stmt->close();

// ---------------------------------------------------------------------
// 3. UPDATE account details in userinfo table EASY database
// ---------------------------------------------------------------------
$sql1 = "UPDATE userinfo 
         SET fullname = ?, section = ?, division = ?
         WHERE uid = ?";
$stmt = $conn1->prepare($sql1);
$stmt->bind_param("ssss", $fullname, $branch_id, $user_division, $uid);
$stmt->execute();
$stmt->close();

$sql2 = "UPDATE users SET user_email = ?, status = ? WHERE uid = ?";
$stmt = $conn1->prepare($sql2);
$stmt->bind_param("sss", $email, $acc_status, $uid);
$stmt->execute();
$stmt->close();

// Update existing role
$sqlRoleUpdate = "UPDATE system_access SET role_id = ? WHERE uid = ?";
$stmt = $conn->prepare($sqlRoleUpdate);
$stmt->bind_param("is", $role_id, $uid);
$stmt->execute();
$stmt->close();

// ---------------------------------------------------------------------
// 4. Redirect back with success message
// ---------------------------------------------------------------------
// $_SESSION['success'] = "Account updated successfully!";
$_SESSION['status'] = 'success';
$_SESSION['message'] = 'Account updated successfully!';
header("Location: ../accProfile.php?uid=" . $uid);
exit;

?>
