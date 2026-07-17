<?php

require 'connect.php';
$uid = $_GET['uid'];


    $sql = "SELECT * FROM connector AS a
            JOIN project_name AS b ON b.project_id = a.project_id
            JOIN surveyjob AS c ON c.sj_id = a.sj_id
            JOIN survey_details AS d ON d.sj_id = c.sj_id
            JOIN division AS e ON e.DIV_ID = b.project_div
            JOIN field_survey AS f ON f.sj_id = c.sj_id
            JOIN computation AS g ON g.sj_id = c.sj_id
            JOIN drawing AS h ON h.sj_id = c.sj_id
            WHERE c.sj_ta = '$uid'";

    $result = $conn->query($sql);

    // Fetch data and store it in an array
    $data = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }

    // Close the database connection
    $conn->close();

    // Encode the data in JSON format and send it as the response
    header('Content-Type: application/json');
    echo json_encode($data);


?>
