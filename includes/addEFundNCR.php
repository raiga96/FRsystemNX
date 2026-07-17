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
    if( isset ( $_POST[ 'editFundNCR']) ){

        $project_id = $_POST['project_id'];
        $fund_id = $_POST['fund_id'];
        $project_div = $_POST['div'];
        $warRef_number = $_POST['warRef_number'];
        $warRef_date = date("Y-m-d",strtotime($_POST['warRef_date']));
        $entry_by = $_POST['uid'];

        // Create a connection
        $conn = OpenCon();

        // Start transaction
        $conn->begin_transaction();

        try {

            $sql2 = "UPDATE ncr_fund SET refrence_id = ?, war_num = ?, ref_date = ?, entry_by = ? WHERE fncrID = '$fund_id'";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->bind_param("ssss", $warRef_number, $warRef_number, $warRef_date, $entry_by);
            $stmt2->execute();


            // Commit transaction
            $conn->commit();

            //echo "New records created successfully";
            header ("Location: ../project_details_form.php?id=".$project_id."&success=updateEditNCR");
        } catch (Exception $e) {
            // Rollback transaction if something goes wrong
            $conn->rollback();
            echo "Error: " . $e->getMessage();
            header ("Location: ../project_details_form.php?id=".$project_id."&error");
        }

        // Close connection
        CloseCon($conn);
    } else {
        header ("Location: ../project_details_form.php?id=".$project_id."&error=wrongNCR");
    }
}
?>
