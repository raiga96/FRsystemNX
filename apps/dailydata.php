<?php

require 'connect.php';
$sj_id = $_GET['sjid'];
$date = $_GET['date'];

// Use a prepared statement to prevent SQL injection
$stmt = $conn->prepare("SELECT * FROM daily_diary WHERE sj_id = ? AND date = ?");
$stmt->bind_param("ss", $sj_id, $date);
$stmt->execute();
$result = $stmt->get_result();

// Fetch data and store it in an array
$data = array();
$row = $result->fetch_assoc();

     $data = array(
        "sj_id" => "$sj_id",
        "date" => $date,
        "status" => $row['status'],
        "alasan" => $row['alasan'],
        "perolehan" => $row['perolehan_harian'],
        "perimeter" => $row['perimeter'],
        "keluasan" => $row['keluasan'],
        "lot" => $row['lot'],
        "percentage" => $row['percentage'],
        "sarapeg" => $row['sarapeg_diguna'],
        "cassini" => $row['cassini_upgraded'],
        "pc_up" => $row['pc_upgraded'],
        "c2c" => $row['c2c'],
        "remark" => $row['remarks'],
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
