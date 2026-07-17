<?php

require 'connect.php';
$sj_id = $_GET['sjid'];

// Use a prepared statement to prevent SQL injection
$stmt = $conn->prepare("
    SELECT * FROM connector AS a
    JOIN project_name AS b ON b.project_id = a.project_id
    JOIN surveyjob AS c ON c.sj_id = a.sj_id
    JOIN survey_details AS d ON d.sj_id = c.sj_id
    JOIN division AS e ON e.DIV_ID = b.project_div
    JOIN field_survey AS f ON f.sj_id = c.sj_id
    JOIN computation AS g ON g.sj_id = c.sj_id
    JOIN drawing AS h ON h.sj_id = c.sj_id
    JOIN pubstatus AS i ON i.id = a.main_status
    WHERE c.sj_id = ?
");
$stmt->bind_param("s", $sj_id);
$stmt->execute();
$result = $stmt->get_result();




// Fetch data and store it in an array
$data = array();
$row = $result->fetch_assoc();
$sj_status = $row['status'];
$actual_start1 = date("d F Y", strtotime($row['fs_actual_start']));
$actual_start2 = date("d F Y", strtotime($row['cm_actual_commence']));
$actual_start3 = date("d F Y", strtotime($row['dr_actual_commence']));
$actual_fs = date("d F Y", strtotime($row['fs_actual_complete']));
$actual_cm = date("d F Y", strtotime($row['cm_actual_complete']));
$actual_dr = date("d F Y", strtotime($row['dr_actual_complete']));

if($actual_start1 == '01 January 1970'){
    $actual_start = 'null';
}else{
    $actual_start = $actual_start1;
}

if($actual_start2 == '01 January 1970'){
    $cm2 = 'null';
}else{
    $cm2 = $actual_start2;
}

if($actual_start3 == '01 January 1970'){
    $ch2 = 'null';
}else{
    $ch2 = $actual_start2;
}

if($actual_fs == '01 January 1970'){
    $afs = 'null';
}else{
    $afs = $actual_fs;
}

if($actual_cm == '01 January 1970'){
    $acm = 'null';
}else{
    $acm = $actual_cm;
}

if($actual_dr == '01 January 1970'){
    $ach = 'null';
}else{
    $ach = $actual_dr;
}

$uid = $row['sj_ta'];

$sqlP = "SELECT SUM(percentage) AS percent FROM daily_diary WHERE sj_id = '$sj_id'";
$resP = $conn -> query($sqlP);
$rowP = $resP -> fetch_assoc();
$percent = $rowP['percent'];

$sqlActual = "SELECT SUM(a.perimeter) as distance, SUM(keluasan) as area, SUM(lot) as lot FROM daily_diary AS a JOIN surveyjob AS b ON b.sj_id = a.sj_id WHERE b.sj_ta = '$uid' AND b.sj_id = '$sj_id'";
$resActual = $conn -> query($sqlActual);
$rowActual = $resActual -> fetch_assoc();
$distance = $rowActual['distance'];
$area = round($rowActual['area'],2);
$lot = $rowActual['lot'];


$tDistance = $row['distance'];
$tLot = $row['target_lot'];
$tArea = $row['target_area'];

// $bDistance = $tDistance - $distance;
// $bLot = $tLot - $lot;
// $bArea = $tArea - $area;

$bDistance = max(0,$tDistance - $distance);
$bArea = max(0,$tArea - $area);
$bLot = max(0,$tLot - $lot);

if($distance == null){
    $adistance = "0";
}else{
    $adistance = $distance;
}

if($lot == null){
    $alot = "0";
}else{
   $alot = $lot;
}

if($area == null){
    $aarea = "0";
}else{
    $aarea = $area;
}
$main_status = $row['main_status'];
$sj_id = $row['sj_id'];

$pjid = $row['project_id'];
     $data = array(
       "sj_id" => "$sj_id",
        "project_name" => $row['project_name'],
        "project_type" => $row['project_type'],
        "project_year" => $row['project_year'],
        "sj_number" => $row['sj_number'],
        "sj_year" => $row['sj_year'],
        "sj_type" => $row['project_type'],
        "distance" => $row['distance'],
        "target_lot" => $row['target_lot'],
        "target_area" => $row['target_area'],
        "approved_area" => $row['approved_area'],
        "parliament" => $row['parliament'],
        "dun" => $row['dun'],
        "district" => $row['district'],
        "fs_target_complete" => date("d F Y", strtotime($row['fs_target_complete'])),
        "cm_target_complete" => date("d F Y", strtotime($row['cm_target_complete'])),
        "dr_target_complete" => date("d F Y", strtotime($row['dr_target_complete'])),
        "fs_actual_complete" => "$afs",
        "cm_actual_complete" => "$acm",
        "dr_actual_complete" => "$ach",
        "fs_actual_start" => "$actual_start",
        "cm_actual_start" => "$cm2",
        "ch_actual_start" => "$ch2",
        "projectId" => "$pjid",
        "bLot" => "$bLot",
        "bDistance" => "$bDistance",
        "bArea" => "$bArea",
        "aDistance" => "$adistance",
        "aLot" => "$alot",
        "aArea" => "$aarea",
        "main_status" => "$main_status",
        "percent" => "$percent",
        "sj_status" => "$sj_status"

    );

// Convert the data to JSON format
$jsonData = json_encode($data);

// Set the Content-Type header to indicate that the response contains JSON data
header('Content-Type: application/json');

// Close connection
$conn->close();

// Output the JSON data
echo $jsonData;

?>
