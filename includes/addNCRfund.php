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
    $project_div = $_POST['div'];
    $total_fund = $_POST['total_fund'];
    $warRef_number = $_POST['war_num'];
    $project_type = 'NCR';
    $warRef_date = date("Y-m-d",strtotime($_POST['received_date']));
    $remark = $_POST['remark'];

    // Create a connection
    $conn = OpenCon();

    // Start transaction
    $conn->begin_transaction();

    try {

        // Insert into fund table
        $sql2 = "INSERT INTO fund (fund_div, war_num, total_fund, fund_type, fund_type_1, date_receive, fund_remark, fund_status) VALUES (?, ?, ?, ?, 'P', ?, ?, '0')";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("ssssss", $project_div, $warRef_number, $total_fund, $project_type, $warRef_date, $remark);
        $stmt2->execute();


        // Commit transaction
        $conn->commit();

        //echo "New records created successfully";
        header ("Location: ../addFundNCR.php?success=update");
    } catch (Exception $e) {
        // Rollback transaction if something goes wrong
        $conn->rollback();
        echo "Error: " . $e->getMessage();
        header ("Location: ../addFundNCR.php?error");
    }

    // Close connection
    CloseCon($conn);
}
?>
