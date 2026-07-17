<?php
require 'connect.php';

$user = $_POST['txtID'];
    $password = $_POST['txtPassword'];
// Extract login username
$loginID = $user;
$sLogin = explode("@", $loginID)[0];

// Connect LDAP
$ldapconn = ldap_connect("ldap.sarawak.gov.my");
ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);

$dn = "o=Sarawaknet";
$filter = "uid=" . ldap_escape($sLogin, "", LDAP_ESCAPE_FILTER);

$sr = ldap_search($ldapconn, $dn, $filter);
$info = ldap_get_entries($ldapconn, $sr);

if ($info["count"] !== 1) {
    header("Location: ../login.php");
    $_SESSION['status'] = 'error';
    $_SESSION['message'] = 'Wrong username or password';
    exit();
}

$ldaprdn = $info[0]["dn"];
$ldapbind = ldap_bind($ldapconn, $ldaprdn, $password);

if (!$ldapbind) {
    header("Location: ../login.php");
    $_SESSION['status'] = 'error';
    $_SESSION['message'] = 'Wrong username or password';
    exit();
}

// Extract department
$posStart = strpos($ldaprdn, "o=");
$deptStr = substr($ldaprdn, $posStart);
$posEnd = strpos($deptStr, ",");
$agency = substr($deptStr, 2, $posEnd - 2);

// Session assign
$_SESSION['USERID'] = $sLogin;
$_SESSION['USERNAME'] = $info[0]["cn"][0];
$_SESSION['EMAIL'] = $info[0]["mail"][0];
$_SESSION['AGENCY'] = $agency;
$_SESSION['start'] = time();
$_SESSION['expire'] = $_SESSION['start'] + (60 * 60);

// Bind ldap and system userinfo
$sqlA = "SELECT uid, user_email FROM users WHERE user_email=?";
$stmtA = $conn1->prepare($sqlA);
$stmtA->bind_param("s", $_SESSION['EMAIL']);
$stmtA->execute();
$resA = $stmtA->get_result();

if ($resA->num_rows === 0) {
    header("Location: ../login.php");
    $_SESSION['status'] = 'error';
    $_SESSION['message'] = 'Wrong username or password';
    exit();
}

$userA = $resA->fetch_assoc();
$_SESSION['uid'] = $userA['uid'];


header("Location: ../index");
exit();
