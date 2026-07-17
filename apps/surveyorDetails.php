<?php

require 'connect.php';
$uid = $_GET['uid'];

// Use a prepared statement to prevent SQL injection

$sqlTot = "SELECT COUNT(*) as total FROM survey_details AS a JOIN surveyjob AS b ON b.sj_id = a.sj_id JOIN connector AS c ON c.sj_id = a.sj_id WHERE b.sj_ta = '$uid' AND c.main_status != '11'";
$resTot = $conn -> query($sqlTot);
$rowTot = $resTot -> fetch_assoc();
$total_pro = $rowTot['total'];

$sqlTot = "SELECT COUNT(*) as total FROM survey_details AS a JOIN surveyjob AS b ON b.sj_id = a.sj_id JOIN connector AS c ON c.sj_id = a.sj_id WHERE b.sj_ta = '$uid' AND c.main_status = '11'";
$resTot = $conn -> query($sqlTot);
$rowTot = $resTot -> fetch_assoc();
$total_completed = $rowTot['total'];


$sqlTarget = "SELECT SUM(a.distance) as tdistance, SUM(target_area) as tarea, SUM(target_lot) as tlot FROM survey_details AS a JOIN surveyjob AS b ON b.sj_id = a.sj_id WHERE b.sj_ta = '$uid'";
$resTarget = $conn -> query($sqlTarget);
$rowTarget = $resTarget -> fetch_assoc();
$tdistance = $rowTarget['tdistance'];
$tarea = round($rowTarget['tarea'],2);
$tlot = $rowTarget['tlot'];


$sqlActual = "SELECT SUM(a.perolehan_harian) as distance, SUM(keluasan) as area, SUM(lot) as lot FROM daily_diary AS a JOIN surveyjob AS b ON b.sj_id = a.sj_id WHERE b.sj_ta = '$uid'";
$resActual = $conn -> query($sqlActual);
$rowActual = $resActual -> fetch_assoc();
$distance = $rowActual['distance'];
$area = $rowActual['area'];
$lot = $rowActual['lot'];

$sqlExp = "SELECT SUM(expenditure) as expand FROM expenditure AS a JOIN fund AS b ON b.fund_id = a.fund_id JOIN surveyjob AS c ON c.sj_id = a.sj_id WHERE c.sj_ta = '$uid'";
$resExp = $conn -> query($sqlExp);
$rowExp = $resExp -> fetch_assoc();
$expanded = $rowExp['expand'];
if($expanded == null){
   $exp = '0';
}else{
   $exp = $expanded;
}

$sqlUser = "SELECT * FROM userinfo AS a JOIN division AS b ON b.DIV_ID = a.division WHERE a.uid = '$uid'";
$resUser = $conn1 -> query($sqlUser);
$rowUser = $resUser -> fetch_assoc();
$fullname = $rowUser['fullname'];
$division = $rowUser['DIV_NAME'];


$bdistance = max(0,$tdistance - $distance);
$barea = max(0,$tarea - $area);
$blot = max(0,$tlot - $lot);

// Fetch data and store it in an array
$data = array();
     $data = array(
         "fullname" => "$fullname",
         "division" => "$division",
        "total_project" => "$total_pro",
        "total_complete" => "$total_completed",
        "tdistance" => "$tdistance",
        "tarea" => "$tarea",
        "tlot" => "$tlot",
        "distance" => "$distance",
        "area" => "$area",
        "lot" => "$lot",
        "bdistance" => "$bdistance",
        "barea" => "$barea",
        "blot" => "$blot",
        "expand" => "$exp"
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
