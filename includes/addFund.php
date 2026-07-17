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
    if ( isset( $_POST[ 'editFund' ] ) ){
        // Collect data from the form
        //project
        $project_id = $_POST['project_id'];
        $fund_id = $_POST['fund_id'];
        $project_div = $_POST['div'];
        $total_fund = $_POST['total_fund'];
        $warRef_number = $_POST['warRef_number'];
        $project_type = 'LAPI';
        $warRef_date = date("Y-m-d",strtotime($_POST['warRef_date']));
        $remark = $_POST['remark'];
        $entry_by = $_POST['uid'];

        // Create a connection
        $conn = OpenCon();

        // Start transaction
        $conn->begin_transaction();

        try {

            $sql2 = "UPDATE fund SET fund_div = ?, war_num = ?, total_fund = ?, fund_type = ?, date_receive = ?, fund_remark = ? , entry_by = ?, fund_status = '0' WHERE fund_id = '$fund_id'";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->bind_param("sssssss", $project_div, $warRef_number, $total_fund, $project_type, $warRef_date, $remark, $entry_by);
            $stmt2->execute();


            // Commit transaction
            $conn->commit();

            //echo "New records created successfully";
            header ("Location: ../project_details_form.php?id=".$project_id."&success=updateEdit");
        } catch (Exception $e) {
            // Rollback transaction if something goes wrong
            $conn->rollback();
            echo "Error: " . $e->getMessage();
            header ("Location: ../project_details_form.php?id=".$project_id."&error");
        }

        // Close connection
        CloseCon($conn);
    } else {
        header ("Location: ../project_details_form.php?id=".$project_id."&error=wrong");
    }
}
?>
