<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$dbConn = $conn1 ?? $conn ?? null;

$divName = '';
$section = '';
$userDiv = null;
$sect = null;

$uid = $_SESSION['uid'] ?? $_SESSION['USERID'] ?? $_SESSION['user'] ?? $_SESSION['username'] ?? null;

if ($dbConn && $dbConn instanceof mysqli && $uid) {
    // 1. Try finding User in `userinfo` first
    try {
        $stmtRole = $dbConn->prepare("SELECT division, section FROM userinfo WHERE uid = ? OR username = ? LIMIT 1");
        if ($stmtRole) {
            $stmtRole->bind_param("ss", $uid, $uid);
            $stmtRole->execute();
            $resRole = $stmtRole->get_result();
            if ($rowRole = $resRole->fetch_assoc()) {
                $userDiv = $rowRole["division"] ?? null;
                $sect = $rowRole["section"] ?? null;
            }
            $stmtRole->close();
        }
    } catch (mysqli_sql_exception $e) {
        // Fallback logic handled below
    }

    // 2. Fallback to `user` table if $userDiv is still empty or `userinfo` failed
    if (empty($userDiv)) {
        try {
            $stmtUser = $dbConn->prepare("SELECT Division, brasec, Role FROM `user` WHERE username = ? OR email = ? OR uid = ? LIMIT 1");
            if ($stmtUser) {
                $stmtUser->bind_param("sss", $uid, $uid, $uid);
                $stmtUser->execute();
                $resUser = $stmtUser->get_result();
                if ($rowUser = $resUser->fetch_assoc()) {
                    $userDiv = $rowUser["Division"] ?? null;
                    $sect = $rowUser["brasec"] ?? null;
                    $userRole = strtoupper(trim((string)($rowUser["Role"] ?? 'NU')));
                    if ($userDiv) {
                        $divName = $userDiv;
                    }
                    if ($sect) {
                        $section = $sect;
                    }
                }
                $stmtUser->close();
            }
        } catch (Exception $ex) {
            // Ignore schema errors
        }
    }

    // 3. Determine user Division name from `division` table if $userDiv is an ID
    if ($userDiv && empty($divName)) {
        try {
            $stmtDiv = $dbConn->prepare("SELECT DIV_NAME FROM division WHERE DIV_ID = ? OR DIV_NAME = ? LIMIT 1");
            if ($stmtDiv) {
                $stmtDiv->bind_param("ss", $userDiv, $userDiv);
                $stmtDiv->execute();
                $resDiv = $stmtDiv->get_result();
                if ($rowDiv = $resDiv->fetch_assoc()) {
                    $divName = $rowDiv["DIV_NAME"] ?? $userDiv;
                } else {
                    $divName = $userDiv;
                }
                $stmtDiv->close();
            }
        } catch (mysqli_sql_exception $e) {
            $divName = (string)$userDiv;
        }
    }

    // 4. Determine user branch / section from `brasecunit` table if $sect is an ID
    if ($sect && empty($section)) {
        try {
            $stmtSect = $dbConn->prepare("SELECT BSU FROM brasecunit WHERE BSU_id = ? OR BSU = ? LIMIT 1");
            if ($stmtSect) {
                $stmtSect->bind_param("ss", $sect, $sect);
                $stmtSect->execute();
                $resSect = $stmtSect->get_result();
                if ($rowSect = $resSect->fetch_assoc()) {
                    $section = $rowSect["BSU"] ?? $sect;
                } else {
                    $section = $sect;
                }
                $stmtSect->close();
            }
        } catch (mysqli_sql_exception $e) {
            $section = (string)$sect;
        }
    }
}
?>