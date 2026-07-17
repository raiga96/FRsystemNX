<?php
session_start();
if (!isset($_POST['addGroup'])) {
    header('Location: ../addGroup.php?uid=' . ($_POST['uid'] ?? ''));
    exit;
}

require 'connect.php';

$uid = $_POST['uid'] ?? '';
$group_id = $_POST['group_id'] ?? '';

if (!ctype_digit((string)$uid) || !ctype_digit((string)$group_id)) {
    $_SESSION['status'] = 'error';
    $_SESSION['message'] = 'Invalid input.';
    header("Location: ../addGroup.php?uid=" . urlencode($uid));
    exit;
}

// Adjust this table/column names if your schema differs.
// This script assumes a mapping table named `user_group` with columns `uid` and `ug_id`.
$mapTable = 'user_group';
$uidCol = 'uid';
$ugCol = 'user_group';
$sesId =  'add_by';
$session = $_SESSION['uid']; // if you want to log session info

$conn->begin_transaction();
try {
    // check if mapping already exists
    $checkSql = "SELECT 1 FROM {$mapTable} WHERE {$uidCol} = ? AND {$ugCol} = ? LIMIT 1";
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param("ii", $uid, $group_id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->close();
        $conn->rollback();
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'User already in that group.';
        header("Location: ../addGroup.php?uid=" . urlencode($uid));
        exit;
    }
    $stmt->close();

    // insert mapping
    $insSql = "INSERT INTO {$mapTable} ({$uidCol}, {$ugCol}, {$sesId}) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insSql);
    $stmt->bind_param("iii", $uid, $group_id, $session);
    $stmt->execute();
    $stmt->close();

    $conn->commit();
    $_SESSION['status'] = 'success';
    $_SESSION['message'] = 'Group added to user.';
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['status'] = 'error';
    $_SESSION['message'] = 'Failed to add group.';
}

header("Location: ../accProfile.php?uid=" . urlencode($uid));
exit;
