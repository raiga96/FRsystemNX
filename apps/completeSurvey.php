<?php
require 'connect.php';
$sjid = $_POST['sj_id'];
$pj_id = $_POST['pj_id'];
$dateToday = date('Y-m-d H:i:s');
$status = $_POST['status'];
$ADA = 'false';

if($status == 'Field'){

                $sql5 = "UPDATE `field_survey` SET `fs_actual_complete`='$dateToday' WHERE `sj_id` = ? ";
                    $stmt5 = $conn->prepare($sql5);
                    $stmt5->bind_param("s", $sjid);
                    if ($stmt5->execute()) {

                    	$sql6 = "UPDATE `connector` SET `main_status`='4' WHERE `sj_id` = ? AND `project_id` = ?";
                    	$stmt6 = $conn->prepare($sql6);
                    	$stmt6->bind_param("ss", $sjid, $pj_id);

                    	if($stmt6-> execute()){

                    		$data = array(
	                        "status" => "success",
	                        "uid" => $sjid

	                        );
	                        echo json_encode($data);

	                        $conn -> commit();

	                        $conn -> close();
	                    	}

                    } else {
                        $conn -> close();
                        echo json_encode(array("status" => "error", "message" => "Error Occured Out"));

                    }

} else if ($status == 'Computation'){

  $sql5 = "UPDATE `computation` SET `cm_actual_complete`='$dateToday' WHERE `sj_id` = ? ";
      $stmt5 = $conn->prepare($sql5);
      $stmt5->bind_param("s", $sjid);
      if ($stmt5->execute()) {

        $sql6 = "UPDATE `connector` SET `main_status`='6' WHERE `sj_id` = ? AND `project_id` = ?";
        $stmt6 = $conn->prepare($sql6);
        $stmt6->bind_param("ss", $sjid, $pj_id);

        if($stmt6-> execute()){

          $data = array(
            "status" => "success",
            "uid" => $sjid

            );
            echo json_encode($data);

            $conn -> commit();

            $conn -> close();
          }

      } else {
          $conn -> close();
          echo json_encode(array("status" => "error", "message" => "Error Occured Out"));

      }
} else if ($status == 'Charting'){

  $sql5 = "UPDATE `drawing` SET `dr_actual_complete`='$dateToday' WHERE `sj_id` = ? ";
      $stmt5 = $conn->prepare($sql5);
      $stmt5->bind_param("s", $sjid);
      if ($stmt5->execute()) {

        $sql6 = "UPDATE `connector` SET `main_status`='8' WHERE `sj_id` = ? AND `project_id` = ?";
        $stmt6 = $conn->prepare($sql6);
        $stmt6->bind_param("ss", $sjid, $pj_id);

        if($stmt6-> execute()){

          $data = array(
            "status" => "success",
            "uid" => $sjid

            );
            echo json_encode($data);

            $conn -> commit();

            $conn -> close();
          }

      } else {
          $conn -> close();
          echo json_encode(array("status" => "error", "message" => "Error Occured Out"));

      }
} else if ($status == 'SBR'){
        $conn->begin_transaction();
        $sql5 = "INSERT INTO `sbr_log` (`sj_id`, `dateSubmit`) VALUES ( ?, ? )";
        $stmt5 = $conn->prepare($sql5);
        $stmt5->bind_param("ss", $sjid, $dateToday);
        if($stmt5-> execute()){
            $conn->commit();
        $sql6 = "UPDATE `connector` SET `main_status`='9' WHERE `sj_id` = ? AND `project_id` = ?";
        $stmt6 = $conn->prepare($sql6);
        $stmt6->bind_param("ss", $sjid, $pj_id);

        if($stmt6-> execute()){
          $conn->commit();
          $data = array(
            "status" => "success",
            "uid" => $sjid

            );
            echo json_encode($data);

            $conn -> commit();

            $conn -> close();
          } else {
            $conn->rollback(); // ❌ rollback on failure
              $conn -> close();
              echo json_encode(array("status" => "error", "message" => "Error Occured while update"));

          }
      } else {
        $conn->rollback(); // ❌ rollback on failure
          $conn -> close();
          echo json_encode(array("status" => "error", "message" => "Error Occured while Ins"));

      }

}
              ?>
