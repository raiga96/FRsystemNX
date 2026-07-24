<?php
session_start();
require_once __DIR__ . '/connect.php'; 

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header("Location: ../login.php");
    exit();
}

$user = trim((string)($_POST['txtID'] ?? ''));
$password = (string)($_POST['txtPassword'] ?? '');
$honeypot = $_POST['RoboTest'] ?? '';

if (!empty($honeypot)) {
    header("Location: ../login.php");
    $_SESSION['status'] = 'error';
    $_SESSION['message'] = 'Bot detected!';
    exit();
}

if ($user === '' || $password === '') {
    header("Location: ../login.php");
    $_SESSION['status'] = 'error';
    $_SESSION['message'] = 'Sila isi nama pengguna dan kata laluan.';
    exit();
}

// 1. Semak maklumat pengguna dari pangkalan data
$sql = "SELECT * FROM `user` WHERE username = ? OR email = ? LIMIT 1";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    header("Location: ../login.php");
    $_SESSION['status'] = 'error';
    $_SESSION['message'] = 'Ralat pangkalan data. Sila hubungi pentadbir.';
    $_SESSION['message2'] = 'Error Code: 11001';
    exit();
}

$stmt->bind_param("ss", $user, $user);
$stmt->execute();
$res = $stmt->get_result();
$userData = $res->fetch_assoc();
$stmt->close();

if (!$userData) {
    // Jalankan dummy verification untuk mengelakkan timing attack
    password_verify($password, '$2y$10$usesomesillystringforsalttocreateadummyhashvalue.abcdefg');
    header("Location: ../login.php");
    $_SESSION['status'] = 'error';
    $_SESSION['message'] = 'Nama pengguna atau kata laluan salah.';
    $_SESSION['message2'] = 'Error Code: 11343';
    exit();
}

$isLdap = isset($userData['ldap']) && strtoupper((string)$userData['ldap']) === 'Y';
$loginSuccess = false;

// 2. Proses pengesahan (LDAP vs Local)
if ($isLdap) {
    // Log masuk SarawakNet LDAP
    $sLogin = explode('@', $user)[0];

    if (function_exists('ldap_connect')) {
        $ldapconn = @ldap_connect('ldap.sarawak.gov.my');
        if ($ldapconn) {
            @ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
            $dn = 'o=Sarawaknet';

            $escapeFilterConst = defined('LDAP_ESCAPE_FILTER') ? LDAP_ESCAPE_FILTER : 2;
            $escapedFilter = function_exists('ldap_escape')
                ? ldap_escape($sLogin, '', $escapeFilterConst)
                : addcslashes($sLogin, ',+\\"<> ;=');

            $filter = 'uid=' . $escapedFilter;
            $sr     = @ldap_search($ldapconn, $dn, $filter);
            $info   = $sr ? @ldap_get_entries($ldapconn, $sr) : false;

            if ($info && isset($info['count']) && (int)$info['count'] === 1) {
                $ldaprdn  = $info[0]['dn'];
                $ldapbind = @ldap_bind($ldapconn, $ldaprdn, $password);
                if ($ldapbind) {
                    $loginSuccess = true;

                    // Set sesi LDAP tambahan
                    if (isset($info[0]['cn'][0])) {
                        $_SESSION['USERNAME'] = $info[0]['cn'][0];
                    }
                    if (isset($info[0]['mail'][0])) {
                        $_SESSION['EMAIL'] = $info[0]['mail'][0];
                    }
                    $posStart = strpos($ldaprdn, 'o=');
                    if ($posStart !== false) {
                        $deptStr = substr($ldaprdn, $posStart);
                        $posEnd  = strpos($deptStr, ',');
                        if ($posEnd !== false) {
                            $_SESSION['AGENCY'] = substr($deptStr, 2, $posEnd - 2);
                        }
                    }
                    $_SESSION['USERID'] = $sLogin;
                }
            }
            @ldap_unbind($ldapconn);
        }
    }
} else {
    // Log masuk tempatan (Local Password Check)
    $hash = $userData['password'] ?? '';
    if (password_verify($password, $hash)) {
        $loginSuccess = true;
    }
}

if (!$loginSuccess) {
    header("Location: ../login.php");
    $_SESSION['status'] = 'error';
    $_SESSION['message'] = 'Nama pengguna atau kata laluan salah.';
    $_SESSION['message2'] = $isLdap ? 'Error Code: 11244' : 'Error Code: 11143';
    exit();
}

// 3. Log masuk berjaya: Set Sesi Utama Aplikasi
session_regenerate_id(true);
$_SESSION['start']      = time();
$_SESSION['expire']     = $_SESSION['start'] + (600 * 60); // 10 jam
$_SESSION['uid']        = $userData['uid'] ?? $userData['id'] ?? null;
$_SESSION['user']       = $userData['username'];
$_SESSION['username']   = $userData['username'];
$_SESSION['system']     = "FRS";
$_SESSION['is_ldap']    = $isLdap ? 1 : 0;

header("Location: ../index.php");
exit();