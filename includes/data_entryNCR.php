<?php
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
    $uid = $_POST['uid'];
    $project_name = $_POST['project_name'];
    $project_type = 'NCR';
    $ncr_type = $_POST['project_type'];
    $project_div = $_POST['project_div'];
    $file_ref = $_POST['file_ref'];
    $project_date = date("Y-m-d", strtotime($_POST['project_date']));
    $project_year = date("Y", strtotime($_POST['project_date']));
    $approve_year = date("Y", strtotime($_POST['approved_year']));
    $district = $_POST['district'];
    $locality = $_POST['locality'];
    $initiateby = $_POST['initiative_by'];
    $approved_area = $_POST['approved_area'];
    $parliament = $_POST['parliament'];
    $dun = $_POST['dun'];

    if (empty($_POST['rt_dialog'])) {
        $rt_dialog = null;
    } else {
        $rt_dialog = $_POST['rt_dialog'];
    }
    if (empty($_POST['field_dialog'])) {
        $field_dialog = null;
    } else {
        $field_dialog = $_POST['field_dialog'];
    }



    $remark = $_POST['remark'];

    // Create a connection
    $conn = OpenCon();


    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert into project_name table
        $sql1 = "INSERT INTO project_name (project_div, project_name, project_year, project_target, file_ref, project_type, lapi_type, project_date, entry_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->bind_param("sssssssss", $project_div, $project_name, $project_year, $project_target, $file_ref, $project_type, $ncr_type, $project_date, $uid);
        $stmt1->execute();
        $project_id = $stmt1->insert_id;

        // Insert into fund table
        // $sql2 = "INSERT INTO ncr_fund (project_id, division, refrence_id, war_num, ref_date) VALUES (?, ?, ?, ?, ?)";
        // $stmt2 = $conn->prepare($sql2);
        // $stmt2->bind_param("sssss", $project_id, $project_div, $pairs, $pairs, $warRef_date);
        // $stmt2->execute();

        // Insert into survey_details table
        $sql3 = "INSERT INTO survey_details (project_id, district, agency, approved_year, initiative_by, approved_area, parliament, dun, rt_dialog, field_dialog, remark) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt3 = $conn->prepare($sql3);
        $stmt3->bind_param("issssssssss", $project_id, $district, $agency, $approve_year, $initiateby, $approved_area, $parliament, $dun, $rt_dialog, $field_dialog, $remark);
        $stmt3->execute();

        // Insert into connector table
        $sql4 = "INSERT INTO connector (project_id, sj_id, main_status) VALUES (?, '0', '0')";
        $stmt4 = $conn->prepare($sql4);
        $stmt4->bind_param("i", $project_id);
        $stmt4->execute();

        // Commit transaction
        $conn->commit();

        //echo "New records created successfully";
        session_start();
        $_SESSION['status'] = 'success';
        $_SESSION['message'] = "Successfully added project";
        header("Location: ../project_details_form.php?id=" . $project_id);
    } catch (Exception $e) {
        // Rollback transaction if something goes wrong
        $conn->rollback();
        echo "Error: " . $e->getMessage();
        header("Location: ../dataEntry.php?error");
    }

    // Close connection
    CloseCon($conn);
}
