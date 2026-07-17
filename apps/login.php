<?php

require 'connect.php';

$uname = $_POST['uname'];
$pwd = $_POST['pwd'];



$sql = "SELECT * FROM users WHERE user_id='" . $uname . "' OR SUBSTRING_INDEX(user_email, '@', 1) = '" .$uname. "'";
$result = $conn1->query($sql);
$row = $result->fetch_assoc();

if ($row) {
    $pwdC = password_verify($pwd, $row['user_password']);

    if ($row['status'] == 'Y') {
        if ($pwdC) {
            // Generate a secure session token
            $token = md5(uniqid());
            $uid = $row['uid'];
            $email = $row['user_email'];
            $role = $row['access_level'];
            $response = array(
                "status" => "success",
                "uid" => $uid,
                "email" => $email,
                "role" => $role,
                "sessionToken" => $token
            );
            echo json_encode($response);
        } else {
            echo json_encode(array("status" => "error", "message" => "Invalid Id / Password!"));
        }
    } else if ($row['status'] == 'R') {
        if ($pwdC) {
            $uid = $row['UID'];
            $response = array(
                "status" => "new_register",
                "uid" => $uid
            );
            echo json_encode($response);
        } else {
            echo json_encode(array("status" => "error", "message" => "Invalid Id / Password!"));
        }
    }
} else {
    echo json_encode(array("status" => "error", "message" => "User not found!"));
}
?>