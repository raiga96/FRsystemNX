<?php
session_start();
if (isset($_POST['addNewSJ'])) {

        require 'connect.php';

        $division = $_POST['division'];
        $sj_type = $_POST['sj_type'];
        $project_id = $_POST['project_id'];
        $sj_number = $_POST['sj_number'];
        $sj_year = $_POST['sj_year'];
        $date_received = date("Y-m-d", strtotime($_POST['date_receive']));
        $section = $_POST['section'];

        if (!preg_match('/^\d{4}$/', $sj_year)) {
                $_SESSION['status'] = 'error';
                $_SESSION['message'] = "Year must be a 4-digit number";
                header("Location: ../project_details_form.php?id=" . $project_id . "&error=invalidYear");
                exit();
        } else {
                // Insert into project_name table
                $sql1 = "INSERT INTO surveyjob (sj_div, sj_number, sj_year, sj_type, sj_issue, sj_status) VALUES (?, ?, ?, ?, ?, '0')";
                $stmt1 = $conn->prepare($sql1);
                $stmt1->bind_param("sssss", $division, $sj_number, $sj_year, $sj_type, $date_received);
                $stmt1->execute();
                $sj_id = $stmt1->insert_id;


                // Update connector table
                if ($section != '2') {
                        $sql2 = "INSERT INTO connector (project_id, sj_id, main_status) VALUES ( ?, ?, '101' )";
                } else {
                        $sql2 = "INSERT INTO connector (project_id, sj_id, main_status) VALUES ( ?, ?, '1' )";
                }
                $stmt2 = $conn->prepare($sql2);
                $stmt2->bind_param("ss", $project_id, $sj_id);
                $stmt2->execute();

                // Commit transaction
                $conn->commit();

                //echo "New records created successfully";
                $_SESSION['status'] = 'success';
                $_SESSION['message'] = "Survey Job added";
                header("Location: ../project_details_form.php?id=" . $project_id . "&success=addNewSJ");
        }
} else {
        echo 'UPPPPSSS';
}
