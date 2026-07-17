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
    $target = $_POST['target'];
    $perimeter = $_POST['perimeter'];
    $area = $_POST['area'];
    $lot = $_POST['lot'];
    $target_type = $_POST['target_type'];
    $target_type_1 = $_POST['target_type_1'];
    $target_type_2 = $_POST['target_type_2'];
    $division = $_POST['division'];
    $add_by = $_POST['add_by'];

    // Create a connection
    $conn = OpenCon();

    // Start transaction
    $conn->begin_transaction();

    try {

        // Insert into fund table
        $sql2 = "INSERT INTO survey_target (target, perimeter, area, lot, division, target_type, target_type_1, target_type_2, add_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("sssssssss", $target, $perimeter, $area, $lot, $division, $target_type, $target_type_1, $target_type_2, $add_by);
        $stmt2->execute();


        // Commit transaction
        $conn->commit();

        //echo "New records created successfully";
        session_start();
        $_SESSION['status'] = 'success';
        $_SESSION['message'] = "Set survey target successfully";
        header ("Location: ../setSurveyTarget.php?success=update");
    } catch (Exception $e) {
        // Rollback transaction if something goes wrong
        $conn->rollback();
        echo "Error: " . $e->getMessage();
        header ("Location: ../setSurveyTarget.php?error=".$e);
    }

    // Close connection
    CloseCon($conn);
}
?>
