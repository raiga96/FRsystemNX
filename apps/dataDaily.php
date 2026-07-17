<?php

require 'connect.php';
$sjid = $_GET['sjid'];

$stmt = $conn->prepare("
    SELECT * FROM daily_diary
    WHERE sj_id = ?
");
$stmt->bind_param("s", $sjid);
$stmt->execute();
$result = $stmt->get_result();

$data = [];

while ($row = $result->fetch_assoc()) {


    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);
$conn->close();
?>
