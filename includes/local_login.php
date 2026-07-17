<?php
require 'connect.php';

// $sql = "SELECT * FROM users WHERE user_id=? OR user_email=?";
// $stmt = $conn1->prepare($sql);
// $stmt->bind_param("ss", $user, $user);
// $stmt->execute();
// $res = $stmt->get_result();

// if ($res->num_rows === 0) {
//     header("Location: ../login.php?error=nouser");
//     exit();
// }

// $row = $res->fetch_assoc();

// if (!password_verify($password, $row['user_password'])) {
//     header("Location: ../login.php?error=wrongpassword");
//     exit();
// }

// $_SESSION['uid'] = $row['uid'];
// $_SESSION['user'] = $row['user_id'];
// $_SESSION['start'] = time();
// $_SESSION['expire'] = $_SESSION['start'] + (600 * 60);

// // Check system access
// $sqlsys = "SELECT * FROM system_access WHERE uid=?";
// $stmt2 = $conn->prepare($sqlsys);
// $stmt2->bind_param("i", $_SESSION['uid']);
// $stmt2->execute();
// $ressys = $stmt2->get_result();

// if ($ressys->num_rows === 0) {
//     header("Location: ../login.php?error=noaccess");
//     exit();
// }

// header("Location: ../index.php");
// exit();
$user = $_POST['txtID'];
    $password = $_POST['txtPassword'];
    $honeypot = $_POST['RoboTest'];

    if (!empty($honeypot)) {
        // Honeypot trap triggered
        $errorMsg = "YOU ARE A ROBOT!";
    } else {
        if (empty($user) || empty($password)) {
            header("Location: ../login.php");
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'Wrong username or password';
            exit();
            
        } else {
            $sql = "SELECT * FROM users WHERE user_id=? OR user_email=?;";
            $stmt = mysqli_stmt_init($conn1);
            if (!mysqli_stmt_prepare($stmt, $sql)) {
                header("Location: ../login.php?sqlerror");
                exit();
            } else {
                mysqli_stmt_bind_param($stmt, "ss", $user, $user);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                if ($row = mysqli_fetch_assoc($result)) {
                    // Verify password
                    if (password_verify($password, $row['user_password'])) {
                        session_start();
                        $_SESSION['start'] = time(); // Taking now logged in time.
                        $_SESSION['expire'] = $_SESSION['start'] + (600 * 60); // Ending a session in 60 minutes from the starting time.
                        $_SESSION['uid'] = $row['uid'];
                        $_SESSION['user'] = $row['user_id'];

                        // Redirect to appropriate page based on system access
                        $sqlsys = "SELECT * FROM system_access WHERE uid = ?";
                        $ressys = mysqli_stmt_init($conn);
                        if (mysqli_stmt_prepare($ressys, $sqlsys)) {
                            mysqli_stmt_bind_param($ressys, "i", $_SESSION['uid']);
                            mysqli_stmt_execute($ressys);
                            if (mysqli_stmt_fetch($ressys)) {
                                header("Location: ../index.php");
                                exit();
                            } else {
                                header("Location: ../login.php?error=noaccess");
                                exit();
                            }
                        } else {
                            header("Location: ../login.php?error=noaccess");
                            exit();
                        }
                    } else {
                        // Incorrect password
                        header("Location: ../login.php");
                        $_SESSION['status'] = 'error';
                        $_SESSION['message'] = 'Wrong username or password';
                        $_SESSION['message2'] = 'Error Code: 11143';
                        exit();
                    }
                } else {
                    // User not found
                    header("Location: ../login.php");
                    $_SESSION['status'] = 'error';
                    $_SESSION['message'] = 'Wrong username or password';
                    $_SESSION['message2'] = 'Error Code: 11343';
                    exit();
                }
            }
        
    } }