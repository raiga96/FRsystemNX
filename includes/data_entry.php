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
    $project_type = 'LAPI';
    $lapi_type = $_POST['lapi_type'];
    $project_target = $_POST['project_target'];
    $project_div = $_POST['project_div'];
    $project_date = date("Y-m-d",strtotime($_POST['project_date']));
    $project_year = date("Y",strtotime($_POST['project_date']));

    if(empty($_POST['total_fund'])){
        $total_fund = null;
    }else{
        $total_fund = $_POST['total_fund'];
    }
    if(empty($_POST['warRef_number'])){
        $warRef_number = null;
    }else{
        $warRef_number = $_POST['warRef_number'];
    }
    $warRef_date = date("Y-m-d",strtotime($_POST['warRef_date']));

    $agency = $_POST['agency'];

    if(empty($_POST['distance'])){
        $distance = '0';
    }else{
        $distance = $_POST['distance'];
    }
    if(empty($_POST['target_lot'])){
        $target_lot = '0';
    }else{
        $target_lot = $_POST['target_lot'];
    }
    if(empty($_POST['target_area'])){
        $target_area = '0';
    }else{
        $target_area = $_POST['target_area'];
    }
    $remark = $_POST['remark'];

    // Create a connection
    $conn = OpenCon();

    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert into project_name table
        $sql1 = "INSERT INTO project_name (project_div, project_name, project_year, project_target, project_type, lapi_type, project_date, entry_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->bind_param("ssssssss", $project_div, $project_name, $project_year, $project_target, $project_type, $lapi_type, $project_date, $uid);
        $stmt1->execute();
        $project_id = $stmt1->insert_id;

        // Insert into fund table
        $sql2 = "INSERT INTO fund (fund_div, project_id, war_num, total_fund, fund_type, fund_type_1, date_receive, fund_status) VALUES (?, ?, ?, ?, ?, 'P', ?, '0')";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("ssssss", $project_div, $project_id, $warRef_number, $total_fund, $project_type, $warRef_date);
        $stmt2->execute();
        $sj_id = $stmt2->insert_id;

        // Insert into survey_details table
        $sql3 = "INSERT INTO survey_details (project_id, agency, distance, target_lot, target_area, remark) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt3 = $conn->prepare($sql3);
        $stmt3->bind_param("isssss", $project_id, $agency, $distance, $target_lot, $target_area, $remark);
        $stmt3->execute();

        // Insert into connector table
        $sql4 = "INSERT INTO connector (project_id, sj_id, main_status) VALUES (?, '0', '0')";
        $stmt4 = $conn->prepare($sql4);
        $stmt4->bind_param("i", $project_id);
        $stmt4->execute();

        // Commit transaction
        $conn->commit();

        //echo "New records created successfully";
        header ("Location: ../savedProject.php?success=update");
    } catch (Exception $e) {
        // Rollback transaction if something goes wrong
        $conn->rollback();
        echo "Error: " . $e->getMessage();
        header ("Location: ../dataEntry.php?error");
    }

    // Close connection
    CloseCon($conn);
}
?>
