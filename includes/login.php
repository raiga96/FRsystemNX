<?php
session_start();
require 'connect.php';   // provides $conn and $conn1

if (!isset($_POST['txtID']) || !isset($_POST['txtPassword'])) {
    header("Location: ../login.php?error=empty");
    exit();
}

$user = $_POST['txtID'];
$password = $_POST['txtPassword'];

// 1) Get uid from userinfo database
$sql = "SELECT * FROM users WHERE user_id=? OR user_email=?";
$stmt = $conn1->prepare($sql);
$stmt->bind_param("ss", $user, $user);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    header("Location: ../login.php");
    $_SESSION['status'] = 'error';
    $_SESSION['message'] = 'Wrong username or password';
    exit();
}else {
    $row = $res->fetch_assoc();
    $uid = $row['uid'];
}

// 2) Check whether user has access to the system
$sqlAccess = "SELECT * FROM system_access WHERE uid = ?";
$stmtAccess = $conn->prepare($sqlAccess);
$stmtAccess->bind_param("i", $uid);
$stmtAccess->execute();
$resAccess = $stmtAccess->get_result();
if ($resAccess->num_rows === 0) {
    header("Location: ../login.php");
    $_SESSION['status'] = 'error';
    $_SESSION['message'] = 'No system access. Please contact administrator.';
    exit();
}

// 3) Check Commands DB first
$sql = "SELECT uid, ldap FROM `userinfo` WHERE uid = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $uid);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: ../login.php");
    $_SESSION['status'] = 'error';
    $_SESSION['message'] = 'Login failed. Please contact administrator.';
    $_SESSION['message2'] = 'Error Code: 11244';
    exit();
}

$row = $result->fetch_assoc();
$ldap_flag = $row['ldap'];

// 2) If LDAP user
if ($ldap_flag === 'Y') {
    require 'ldap_login.php';
    exit();
}

// 3) If LOCAL user
require 'local_login.php';
exit();
?>