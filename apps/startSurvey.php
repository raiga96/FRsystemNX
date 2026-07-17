<?php
require 'connect.php';
$sjid = $_POST['sj_id'];
$pj_id = $_POST['pj_id'];
$status = $_POST['status'];
$dateToday = date('Y-m-d H:i:s');
$ADA = 'false';

if($status == 'fs'){
                $sql5 = "UPDATE `field_survey` SET `fs_actual_start`='$dateToday' WHERE `sj_id` = ? ";
                    $stmt5 = $conn->prepare($sql5);
                    $stmt5->bind_param("s", $sjid);
                    if ($stmt5->execute()) {

                    	$sql6 = "UPDATE `connector` SET `main_status`='3' WHERE `sj_id` = ? AND `project_id` = ?";
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
} else if ($status == 'comp'){

  $sql5 = "UPDATE `computation` SET `cm_actual_commence`='$dateToday' WHERE `sj_id` = ? ";
      $stmt5 = $conn->prepare($sql5);
      $stmt5->bind_param("s", $sjid);
      if ($stmt5->execute()) {

        $sql6 = "UPDATE `connector` SET `main_status`='5' WHERE `sj_id` = ? AND `project_id` = ?";
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
} else if ($status == 'chart'){

  $sql5 = "UPDATE `drawing` SET `dr_actual_commence`='$dateToday' WHERE `sj_id` = ? ";
      $stmt5 = $conn->prepare($sql5);
      $stmt5->bind_param("s", $sjid);
      if ($stmt5->execute()) {

        $sql6 = "UPDATE `connector` SET `main_status`='7' WHERE `sj_id` = ? AND `project_id` = ?";
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
}
?>
