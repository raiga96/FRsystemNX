<?php

require 'connect.php';

// Get POST data
$sjid        = $_POST['sj_id'];
$date        = $_POST['date'];
$status      = $_POST['status'];
$excuse      = $_POST['excuse']; // alasan
$output      = $_POST['output']; // perolehan_harian
$area        = $_POST['areaDaily']; // perimeter
$lot         = $_POST['lotDaily'];
$c2c         = $_POST['c2c'];
$sarapeg     = $_POST['sarapegUsed'];
$cassini     = $_POST['cassiniUpgrade'];
$pcUpgrade   = $_POST['pcUpgrade'];
$percentage  = $_POST['percentage']; // not used in SQL — optional
$remark      = $_POST['remark'];


$output1 = empty($output) ? ($area ?: $lot) : $output;
$output    = empty($output) ? 0 : $output;
$excuse    = empty($excuse) ? "Tiada": $excuse;
$area      = empty($area) ? 0 : $area;
$lot       = empty($lot) ? 0 : $lot;
$c2c       = empty($c2c) ? 0 : $c2c;
$sarapeg   = empty($sarapeg) ? 0 : $sarapeg;
$pcUpgrade = empty($pcUpgrade) ? 0 : $pcUpgrade;
$cassini   = empty($cassini) ? 0 : $cassini;
$remark    = empty($remark) ? "Tiada" : $remark;


$dateRecorded  = date('Y-m-d H:i:s');

// Check if required fields are filled (basic validation)
if (!empty($sjid) && !empty($date) && !empty($status)) {

    // Build the SQL query
    $insert = "INSERT INTO daily_diary
        (sj_id, date, status, alasan, perolehan_harian, perimeter, keluasan, lot, percentage, sarapeg_diguna, cassini_upgraded, pc_upgraded, c2c, remarks, dateRecorded)
        VALUES
        ('$sjid', '$date', '$status', '$excuse', '$output1', '$output', '$area', '$lot', '$percentage', '$sarapeg', '$cassini', '$pcUpgrade', '$c2c', '$remark', '$dateRecorded')";

    // Execute the query
    if (mysqli_query($conn, $insert)) {
        $response = [
            'status' => 'success',
            'message' => 'You have successfully submitted your Daily Diary. Thank you.'
        ];
    } else {
        $response = [
            'status' => 'error',
            'message' => 'Database insert failed: ' . mysqli_error($conn)
        ];
    }

} else {
    $response = [
        'status' => 'error',
        'message' => 'Some required fields are empty.'
    ];
}

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);

// Close the connection
$conn->close();
?>
