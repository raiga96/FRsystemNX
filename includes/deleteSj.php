<?php
session_start();
if (!isset($_POST['deleteSJ'])) {
    header('Location: ../project_details_form.php?id=' . ($_POST['project_id'] ?? ''));
    exit;
}

require 'connect.php';

$project_id = $_POST['project_id'] ?? '';
$sj_id = $_POST['sj_id'] ?? '';

if (!ctype_digit((string)$sj_id)) {
    $_SESSION['status'] = 'error';
    $_SESSION['message'] = 'Invalid Survey Job ID';
    header("Location: ../project_details_form.php?id=" . $project_id);
    exit;
}

$conn->begin_transaction();
try {
    // delete from connector
    $stmt = $conn->prepare("DELETE FROM connector WHERE sj_id = ? AND project_id = ?");
    $stmt->bind_param("ss", $sj_id, $project_id);
    $stmt->execute();
    $stmt->close();

    // delete related details (safe to include; adjust tables as needed)
    $stmt = $conn->prepare("DELETE FROM survey_details WHERE sj_id = ?");
    $stmt->bind_param("s", $sj_id);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM locations WHERE sj_id = ?");
    $stmt->bind_param("s", $sj_id);
    $stmt->execute();
    $stmt->close();

    // finally delete surveyjob
    $stmt = $conn->prepare("DELETE FROM surveyjob WHERE sj_id = ?");
    $stmt->bind_param("s", $sj_id);
    $stmt->execute();
    $stmt->close();

    $conn->commit();
    $_SESSION['status'] = 'success';
    $_SESSION['message'] = 'Survey Job deleted';
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['status'] = 'error';
    $_SESSION['message'] = 'Deletion failed';
}

header("Location: ../project_details_form.php?id=" . $project_id);
exit;
