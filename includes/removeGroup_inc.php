<?php
session_start();
if (!isset($_POST['removeGroup'])) {
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

$conn->begin_transaction();
try {
    // delete mapping
    $delSql = "DELETE FROM user_group WHERE uid = ? AND user_group = ?";
    $stmt = $conn->prepare($delSql);
    $stmt->bind_param("ii", $uid, $group_id);
    $stmt->execute();
    $affected = $stmt->affected_rows;
    $stmt->close();

    $conn->commit();

    if ($affected > 0) {
        $_SESSION['status'] = 'success';
        $_SESSION['message'] = 'Group removed from user.';
    } else {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Mapping not found.';
    }
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['status'] = 'error';
    $_SESSION['message'] = 'Failed to remove group.';
}

header("Location: ../removeGroup.php?uid=" . urlencode($uid));
exit;
