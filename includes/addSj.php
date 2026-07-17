<?php
session_start();
function OpenCon()
{
    require 'x.php';
    $dbhost = $host;
    $dbuser = $user;
    $dbpass = $password;
    $dbname = $db;

    $conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname) or die("Connect failed: %s\n" . $conn->error);

    return $conn;
}

function CloseCon($conn)
{
    $conn->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect data from the form
    //project
    $division = $_POST['division'];
    $sj_type = $_POST['sj_type'];
    $project_id = $_POST['project_id'];
    $sj_number = $_POST['sj_number'];
    $sj_year = $_POST['sj_year'];
    $date_received = date("Y-m-d", strtotime($_POST['date_receive']));
    $section = $_POST['section'];

    // Create a connection
    $conn = OpenCon();

    // Start transaction
    $conn->begin_transaction();

    if (!preg_match('/^\d{4}$/', $sj_year)) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = "Year must be a 4-digit number";
        header("Location: ../project_details_form.php?id=" . $project_id . "&error=invalidYear");
        exit();
    } else {

        try {
            // Insert into project_name table
            $sql1 = "INSERT INTO surveyjob (sj_div, sj_number, sj_year, sj_type, sj_issue, sj_status) VALUES (?, ?, ?, ?, ?, '0')";
            $stmt1 = $conn->prepare($sql1);
            $stmt1->bind_param("sssss", $division, $sj_number, $sj_year, $sj_type, $date_received);
            $stmt1->execute();
            $sj_id = $stmt1->insert_id;

            // Update fund table
            //$sql2 ="UPDATE fund SET sj_number = '$sj_id' WHERE project_id = '$project_id'";
            //$conn->query($sql2);

            // Update survey_details table
            // $sql3 ="UPDATE survey_details SET sj_id = '$sj_id' WHERE project_id = '$project_id'";
            // $conn->query($sql3);


            // Update connector table
            //$sql4 = "UPDATE connector SET sj_id = '$sj_id', main_status = '101' WHERE project_id = '$project_id'";
            if ($section != '2') {
                $sql4 = "UPDATE connector SET sj_id = '$sj_id', main_status = '101' WHERE project_id = '$project_id'";
            } else {
                $sql4 = "UPDATE connector SET sj_id = '$sj_id', main_status = '1' WHERE project_id = '$project_id'";
            }
            $conn->query($sql4);

            // Commit transaction
            $conn->commit();

            //echo "New records created successfully";
            $_SESSION['status'] = 'success';
            $_SESSION['message'] = "Successfully add new survey job";

            header("Location: ../project_details_form.php?id=" . $project_id . "&success=update");
        } catch (Exception $e) {
            // Rollback transaction if something goes wrong
            $conn->rollback();
            echo "Error: " . $e->getMessage();
            header("Location: ../dataEntry.php?error");
        }

        // Close connection
        CloseCon($conn);
    }
}
