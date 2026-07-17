<?php
session_start();
require 'connect.php'; 

if (!isset($_POST['txtID']) || !isset($_POST['txtPassword'])) {
    header("Location: ../login.php?error=empty");
    exit();
}

$user = $_POST['txtID'];
$password = $_POST['txtPassword'];

// 1) Get uid from userinfo database
$sql = "SELECT * FROM `user` WHERE username=? OR email=?";
$stmt = $conn->prepare($sql);
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
    $username = $row['username'];
}

// 2) Check Commands DB first
$sql = "SELECT ldap FROM `user` WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
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