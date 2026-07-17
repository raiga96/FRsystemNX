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
    $surveyor = $_POST['surveyor'];
    $date_assign = $_POST['date_assign'];
    $sj_id = $_POST['sj_id'];
    $project_id = $_POST['project_id'];
	
	$target_distance = $_POST['target_distance'];
	$target_area = $_POST['target_area'];
	$target_lot = $_POST['target_lot'];
    
    $target_field = date("Y-m-d",strtotime($_POST['target_field']));
    $target_comp = date("Y-m-d",strtotime($_POST['target_comp']));
    $target_chart = date("Y-m-d",strtotime($_POST['target_chart']));
    
    if(empty($_POST['distance'])){
        $distance = 0;
    }else{
        $distance = $_POST['distance'];
    }
    if(empty($_POST['target_area'])){
        $target_area = 0;
    }else{
        $target_area = $_POST['target_area'];
    }
    if(empty($_POST['target_lot'])){
        $target_lot = 0;
    }else{
        $target_lot = $_POST['target_lot'];
    }
    
    $remark = $_POST['remark'];

    // Create a connection
    $conn = OpenCon();

    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert into project_name table
        $sql1 = "INSERT INTO field_survey (sj_id, fs_target_complete) VALUES (?, ?)";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->bind_param("ss", $sj_id, $target_field);
        $stmt1->execute();

        // Insert into fund table
        $sql2 = "INSERT INTO computation (sj_id, cm_target_complete) VALUES (?, ?)";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("ss", $sj_id, $target_comp);
        $stmt2->execute();

        // Insert into survey_details table
        $sql3 = "INSERT INTO drawing (sj_id, dr_target_complete) VALUES (?, ?)";
        $stmt3 = $conn->prepare($sql3);
        $stmt3->bind_param("ss", $sj_id, $target_chart);
        $stmt3->execute();
		
        // Insert into connector table
        $sql4 ="UPDATE surveyjob SET sj_ta = '$surveyor', sj_assigned_on = '$date_assign' WHERE sj_id = '$sj_id'";
        $conn->query($sql4);
		
		$sql7 = "UPDATE connector SET main_status = '2' WHERE sj_id = '$sj_id'";
		$conn-> query($sql7);
		
		$inq = "SELECT * FROM survey_details WHERE project_id = '$project_id'";
		$inr = $conn -> query($inq);
		$row = $inr -> fetch_assoc();
		$agency = $row['agency'];
		$district = $row['district'];
		$approved_year = $row['approved_year'];
		$approved_area = $row['approved_area'];
		$initiative_by = $row['initiative_by'];
		$parliament = $row['parliament'];
		$dun = $row['dun'];
		$rt_dialog = $row['rt_dialog'];
		$field_dialog = $row['field_dialog'];
		
		$sql5 = "INSERT INTO survey_details (sd_type, project_id, sj_id, district, agency, approved_year, initiative_by, distance, approved_area, target_area, target_lot, parliament, dun, rt_dialog, field_dialog, remark) VALUES ('SJ', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		$stmt5 = $conn -> prepare($sql5);
		$stmt5 -> bind_param("sssssssssssssss", $project_id, $sj_id, $district, $agency, $approved_year, $initiative_by, $target_distance, $approved_area, $target_area, $target_lot, $parliament, $dun, $rt_dialog, $field_dialog, $remark);
		$stmt5 -> execute();

        // Commit transaction
        $conn->commit();

        //echo "New records created successfully";
        
        $_SESSION['status'] = 'success';
        $_SESSION['message'] = "Successfully assigned and update";

        header ("Location: ..//targetSettings.php?id=".$project_id."&sjid=".$sj_id."&success=update");
    } catch (Exception $e) {
        // Rollback transaction if something goes wrong
        $conn->rollback();
//        echo "Error: " . $e->getMessage();
//        header ("Location: ../targetSettings.php?id=".$project_id."&sjid=".$sj_id."&error=$e->");
		$errorMessage = urlencode($e->getMessage()); // URL-encode the error message
		header("Location: ../targetSettings.php?id=" . urlencode($project_id) . "&sjid=" . urlencode($sj_id) . "&error=" . $errorMessage);
		exit; 
    }

    // Close connection
    CloseCon($conn);
}
?>
