<?php
require 'connect.php';

$sj_id = $_GET['sj_id'];
$nowdate = date("Y-m-d");
$curYear = date("Y");

$sqlZ = "SELECT * FROM field_survey WHERE sj_id = '$sj_id'";
$resZ = $conn -> query($sqlZ);
$rowZ = $resZ -> fetch_assoc();
$sdate = date("Y-m-d", strtotime($rowZ['fs_actual_start']));
$st = $rowZ['fs_actual_start'];

if(empty($rowZ['fs_actual_complete'])){
  $ut = $rowZ['fs_target_complete'];
}else{
  $ut = date("Y-m-d",strtotime($rowZ['fs_actual_complete']));
}
$ut = date("Y-m-d");

if($st == null){
    $dateData = array(
      'sj_id' => "$sj_id",
            'date' => $nowdate,
            'hari' => "0",
            'timeIn' => "no",
            'status' => "no"

        );

        $data[] = $dateData;
}else{
$sql = "SELECT DISTINCT calendar.datefield AS DATE, COALESCE(daily_diary.date, 0) AS absent
        FROM daily_diary RIGHT JOIN calendar ON (DATE(daily_diary.date) = calendar.datefield)
        WHERE (calendar.datefield BETWEEN (SELECT MIN(DATE('$sdate')) FROM field_survey) AND DATE('$ut'))
        AND calendar.datefield <= '$ut'
        ORDER BY DATE ASC";

$result = $conn->query($sql);

// Fetch data and store it in an array
$data = array();

if ($result->num_rows > 0) {
    while ($Y = $result->fetch_assoc()) {

        $sql2 = "SELECT DATE_FORMAT('" . $Y["DATE"] . "', '%a') as namahari FROM calendar ";
        $exc2 = mysqli_query($conn, $sql2);
        $result2 = $exc2->fetch_assoc();
        $hari = $result2['namahari'];

        $sqlCC = "SELECT * FROM daily_diary WHERE sj_id = '$sj_id' AND date LIKE '" . $Y["DATE"] . "'";
        $excCC = $conn->query($sqlCC);

        $timeIn = "no";

        if ($excCC->num_rows > 0) {
            while ($rowCC = $excCC->fetch_assoc()) {
                $timeIn = $rowCC['ddID'];

            }
        }

        $dateData = array(
          'sj_id' => "$sj_id",
            'date' => $Y['DATE'],
            'hari' => $hari,
            'timeIn' => $timeIn,
            'status' => "yes"

        );

        $data[] = $dateData;
    }
} else {
    $data = array();
    $data[] = ["error" => "No data found"];
}
}
// Close the database connection
$conn->close();

// Encode the data in JSON format and send it as the response
header('Content-Type: application/json');
echo json_encode($data);

?>
