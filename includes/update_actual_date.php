<?php
include 'connect.php';

if (isset($_POST['save']) || isset($_POST['submit'])) {
    $sj_id = $_POST['sj_id'];
    $pj_id = $_POST['pj_id'];
    $fs_actual_complete = $_POST['fs_actual_complete'] ?? null;
    $cm_actual_complete = $_POST['cm_actual_complete'] ?? null;
    $dr_actual_complete = $_POST['dr_actual_complete'] ?? null;

    $updatedFields = [];

    if ($fs_actual_complete !== null && $fs_actual_complete !== '') {
        $stmt = $conn->prepare("UPDATE `field_survey` SET `fs_actual_complete` = ? WHERE sj_id = ?");
        $stmt->bind_param("ss", $fs_actual_complete, $sj_id);
        if ($stmt->execute()) {
            $updatedFields[] = "Field Survey Actual Complete date";
        }
        $stmt->close();
    }

    if ($cm_actual_complete !== null && $cm_actual_complete !== '') {
        $stmt1 = $conn->prepare("UPDATE `computation` SET `cm_actual_complete` = ? WHERE sj_id = ?");
        $stmt1->bind_param("ss", $cm_actual_complete, $sj_id);
        if ($stmt1->execute()) {
            $updatedFields[] = "Computation Actual Complete date";
        }
        $stmt1->close();
    }

    if ($dr_actual_complete !== null && $dr_actual_complete !== '') {
        $stmt2 = $conn->prepare("UPDATE `drawing` SET `dr_actual_complete` = ? WHERE sj_id = ?");
        $stmt2->bind_param("ss", $dr_actual_complete, $sj_id);
        if ($stmt2->execute()) {
            $updatedFields[] = "Charting Actual Complete date";
        }
        $stmt2->close();
    }

    session_start();

    if (!empty($updatedFields)) {
        // Join the updated fields with commas, with "and" before last one
        if (count($updatedFields) == 1) {
            $message = $updatedFields[0] . " has been updated.";
        } else {
            $last = array_pop($updatedFields);
            $message = implode(", ", $updatedFields) . " and " . $last . " have been updated.";
        }
        $_SESSION['status'] = 'success';
        $_SESSION['message'] = $message;
    } else {
        $_SESSION['status'] = 'info';
        $_SESSION['message'] = "No actual complete dates were updated.";
    }

    header("Location: ../project_details_formd.php?pages=lapi&id=" . urlencode($pj_id) . "&sjid=" . urlencode($sj_id));
    exit();
}
