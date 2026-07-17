<?php
require 'connect.php';

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
            $sql = "SELECT * FROM user WHERE username=? OR email=?;";
            $stmt = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt, $sql)) {
                header("Location: ../login.php?sqlerror");
                exit();
            } else {
                mysqli_stmt_bind_param($stmt, "ss", $user, $user);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                if ($row = mysqli_fetch_assoc($result)) {
                    // Verify password
                    if (password_verify($password, $row['password'])) {
                        session_start();
                        $_SESSION['start'] = time(); // Taking now logged in time.
                        $_SESSION['expire'] = $_SESSION['start'] + (600 * 60); // Ending a session in 60 minutes from the starting time.
                        $_SESSION['user'] = $row['username'];
                        $_SESSION['system'] = "FRS";

                        header("Location: ../index.php");
                        exit();

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