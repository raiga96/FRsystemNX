<?php

require 'connect.php';
$sjta = $_GET['sjta'];

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
    WHERE c.sj_ta = ?
");
$stmt->bind_param("s", $sjta);
$stmt->execute();
$result = $stmt->get_result();

$data = [];

while ($row = $result->fetch_assoc()) {
    $sj_id = $row['sj_id'];
    $uid = $row['sj_ta'];


    // Daily diary percentages
    $sqlP = "SELECT SUM(percentage) AS percent FROM daily_diary WHERE sj_id = '$sj_id'";
    $resP = $conn->query($sqlP);
    $rowP = $resP->fetch_assoc();
    $percent = $rowP['percent'];

    // Actual survey stats
    $sqlActual = "SELECT SUM(a.perimeter) as distance, SUM(keluasan) as area, SUM(lot) as lot
                  FROM daily_diary AS a
                  JOIN surveyjob AS b ON b.sj_id = a.sj_id
                  WHERE b.sj_ta = '$uid' AND b.sj_id = '$sj_id'";
    $resActual = $conn->query($sqlActual);
    $rowActual = $resActual->fetch_assoc();

    $distance = $rowActual['distance'];
    $area = $rowActual['area'] ;
    $lot = $rowActual['lot'] ;

    $tDistance = $row['distance'];
    $tLot = $row['target_lot'];
    $tArea = $row['target_area'];

    $bDistance = max(0, $tDistance - $distance);
    $bArea = max(0, $tArea - $area);
    $bLot = max(0, $tLot - $lot);

    $data[] = [
        "sj_id" => $sj_id,
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
        "fs_target_complete" => date("Y-m-d",strtotime($row['fs_target_complete'])),
        "cm_target_complete" => date("Y-m-d",strtotime($row['cm_target_complete'])),
        "dr_target_complete" => date("Y-m-d",strtotime($row['dr_target_complete'])),
        "fs_actual_complete" => date("Y-m-d",strtotime($row['fs_actual_complete'])),
        "cm_actual_complete" => date("Y-m-d",strtotime($row['cm_actual_complete'])),
        "dr_actual_complete" => date("Y-m-d",strtotime($row['dr_actual_complete'])),
        "fs_actual_start" => date("Y-m-d",strtotime($row['fs_actual_start'])),
        "cm_actual_start" => date("Y-m-d",strtotime($row['cm_actual_commence'])),
        "ch_actual_start" => date("Y-m-d",strtotime($row['dr_actual_commence'])),
        "projectId" => $row['project_id'],
        "bLot" => $bLot,
        "bDistance" => $bDistance,
        "bArea" => $bArea,
        "aDistance" => $distance,
        "aLot" => $lot,
        "aArea" => $area,
        "main_status" => $row['main_status'],
        "percent" => $percent,
        "sj_status" => $row['status']
    ];
}

header('Content-Type: application/json');
echo json_encode($data);
$conn->close();
?>
