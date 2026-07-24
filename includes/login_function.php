<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Semak kewujudan sesi pengguna (sama ada uid, username, atau USERID)
$sessionUid = $_SESSION['uid'] ?? $_SESSION['USERID'] ?? $_SESSION['user'] ?? null;

if (!$sessionUid) {
    header("Location: login.php?error=nouser");
    exit();
}

// Semak masa luapan sesi (Session Expiry Check)
$now = time();
if (isset($_SESSION['expire']) && $now > $_SESSION['expire']) {
    session_unset();
    session_destroy();
    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                title: "Sesi Tamat",
                text: "Sesi anda telah tamat. Sila log masuk semula.",
                icon: "warning",
                confirmButtonText: "OK"
            }).then(function() {
                window.location = "login.php";
            });
        });
    </script>';
    exit();
}

// Kemaskini masa pengaktifan sesi terbaharu
$_SESSION['last_activity'] = $now;

// Semak data pengguna dari database secara selamat mengguna Prepared Statement
if (isset($conn) && $conn instanceof mysqli) {
    $stmt = $conn->prepare("SELECT * FROM `user` WHERE username = ? OR email = ? OR uid = ? LIMIT 1");
    if ($stmt) {
        $searchKey = (string)$sessionUid;
        $stmt->bind_param("sss", $searchKey, $searchKey, $searchKey);
        $stmt->execute();
        $resUser = $stmt->get_result();
        $currentUser = $resUser->fetch_assoc();
        $stmt->close();

        if (!$currentUser) {
            // Jika rekod pengguna tidak wujud lagi dalam database
            session_unset();
            session_destroy();
            header("Location: login.php?error=nouser");
            exit();
        }
    }
}
?>