<?php
include 'connect.php';

if (isset($_POST['save']) || isset($_POST['submit'])) {
    $sj_id = $_POST['sj_id'];
    $pj_id = $_POST['pj_id'];
    $approx_area = $_POST['targeta_area'];
    $unit = $_POST['target_unit'];

    if (empty($_POST['targeta_lot'])) {
        $approx_lot = '0';
    } else {
        $approx_lot = $_POST['targeta_lot'];
    }



    $inq = "SELECT * FROM survey_details WHERE project_id = '$pj_id'";
    $inr = $conn->query($inq);
    $row = $inr->fetch_assoc();
    $agency = $row['agency'];
    $district = $row['district'];
    $approved_year = $row['approved_year'];
    $approved_area = $row['approved_area'];
    $initiative_by = $row['initiative_by'];
    $parliament = $row['parliament'];
    $dun = $row['dun'];
    $rt_dialog = $row['rt_dialog'];
    $field_dialog = $row['field_dialog'];



    $sql5 = "INSERT INTO survey_details (sd_type, project_id, sj_id, district, agency, approved_year, initiative_by, approved_area, approx_area, unit, approx_lot, parliament, dun, rt_dialog, field_dialog, remark) VALUES ('SJ', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt5 = $conn->prepare($sql5);
    $stmt5->bind_param("sssssssssssssss", $pj_id, $sj_id, $district, $agency, $approved_year, $initiative_by, $approved_area, $approx_area, $unit, $approx_lot, $parliament, $dun, $rt_dialog, $field_dialog, $remark);
    $stmt5->execute();


    session_start();


    $_SESSION['status'] = 'success';
    $_SESSION['message'] = "Approximately Target has been set";


    header("Location: ../targetSettings.php?id=" . urlencode($pj_id) . "&sjid=" . urlencode($sj_id));
    exit();
}
