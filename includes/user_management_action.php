<?php
session_start();
require_once __DIR__ . '/connect.php';

header('Content-Type: application/json');

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'INVALID REQUEST METHOD.']);
    exit();
}

$action = trim((string)($_POST['action'] ?? ''));

// Action: Toggle LDAP
if ($action === 'toggle_ldap') {
    $username = trim((string)($_POST['username'] ?? ''));
    $ldapValue = trim((string)($_POST['ldap'] ?? 'N'));
    $ldapValue = strtoupper($ldapValue) === 'Y' ? 'Y' : 'N';

    if (empty($username)) {
        echo json_encode(['status' => 'error', 'message' => 'INVALID USERNAME.']);
        exit();
    }

    $stmt = $conn->prepare("UPDATE `user` SET `ldap` = ? WHERE `username` = ?");
    if ($stmt) {
        $stmt->bind_param("ss", $ldapValue, $username);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'USER LDAP STATUS UPDATED SUCCESSFULLY.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'FAILED TO UPDATE LDAP STATUS.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'DATABASE ERROR.']);
    }
    exit();
}

// Action: Update Division
if ($action === 'update_division') {
    $username = trim((string)($_POST['username'] ?? ''));
    $division = trim((string)($_POST['division'] ?? ''));

    if (empty($username) || empty($division)) {
        echo json_encode(['status' => 'error', 'message' => 'PLEASE PROVIDE VALID DIVISION AND USERNAME.']);
        exit();
    }

    $stmt = $conn->prepare("UPDATE `user` SET `Division` = ? WHERE `username` = ?");
    if ($stmt) {
        $stmt->bind_param("ss", $division, $username);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'USER DIVISION UPDATED SUCCESSFULLY.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'FAILED TO UPDATE USER DIVISION.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'DATABASE ERROR.']);
    }
    exit();
}

// Action: Toggle Active
if ($action === 'toggle_active') {
    $username = trim((string)($_POST['username'] ?? ''));
    $activeValue = trim((string)($_POST['active'] ?? 'N'));
    $activeValue = strtoupper($activeValue) === 'Y' ? 'Y' : 'N';

    if (empty($username)) {
        echo json_encode(['status' => 'error', 'message' => 'INVALID USERNAME.']);
        exit();
    }

    $stmt = $conn->prepare("UPDATE `user` SET `active` = ? WHERE `username` = ?");
    if ($stmt) {
        $stmt->bind_param("ss", $activeValue, $username);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'USER ACTIVE STATUS UPDATED SUCCESSFULLY.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'FAILED TO UPDATE USER ACTIVE STATUS.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'DATABASE ERROR.']);
    }
    exit();
}

echo json_encode(['status' => 'error', 'message' => 'INVALID ACTION.']);
exit();
?>
