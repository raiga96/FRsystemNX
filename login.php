<!--
=========================================================
* Material Dashboard 2 - v3.0.0
=========================================================

* Product Page: https://www.creative-tim.com/product/material-dashboard
* Copyright 2021 Creative Tim (https://www.creative-tim.com)
* Licensed under MIT (https://www.creative-tim.com/license)
* Coded by Creative Tim

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->
<?php
session_start();

include("includes/connect.php");
if (isset($_SESSION["uid"])) {
  header("Location: index.php");
  exit;
} else {

?>
  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="./assets/img/favicon.svg">
    <link rel="icon" type="image/png" href="./assets/img/favicon.svg">
    <title>
      FRsystem
    </title>
    <!--     Fonts and icons     -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" />
    <!-- Nucleo Icons -->
    <link href="./assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="./assets/css/nucleo-svg.css" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <!-- CSS Files -->
    <link id="pagestyle" href="./assets/css/material-dashboard.css?v=3.0.0" rel="stylesheet" />
    <style>
    </style>
  </head>

  <body style="">
    <main class="main-content  mt-0">
      <div class="page-header align-items-start min-vh-100 bg-gradient-dark">
        <span class="mask bg-gradient-dark opacity-6"></span>
        <div class="container my-auto">
          <div class="row">

            <div class="col-lg-4 col-md-8 col-12 mx-auto ">
              <div class="card z-index-0 fadeIn3 fadeInBottom">
                <div class="card-body">
                  <div class="justify-content-center" align="center">
                    <img src="./images/MainBanner.png" alt="" class="mt-2 mb-2 pe-3 p-3 w-100">
                  </div>

                  
                  <form role="form" class="text-start" action="includes/login.php" method="POST">
                    <div class="input-group input-group-outline my-3">
                      <!-- <span><i class="fa fa-user" style="color:black"></i></span> -->
                      <input type="text" class="form-control shadow" name="txtID" placeholder="Username">
                    </div>
                    <div class="input-group input-group-outline mb-3">
                      <input type="password" class="form-control shadow" name="txtPassword" placeholder="Password">
                    </div>
                    
                    <div class="d-flex align-items-justify justify-content-between">
                    <div class="form-check form-switch d-flex align-items-center mb-3">
                      <input class="form-check-input" type="checkbox" id="rememberMe">
                      <label class="form-check-label mb-0 ms-2 text-dark" for="rememberMe"><small class="font-weight-bolder">Remember me</small></label>
                    </div>
                    <div class="mb-3">
                    <a href='https://fim2.sarawak.gov.my/login.php?redirect=%2Fadmin%2F'><small class="font-weight-bolder">Forgot Password?</small></a>
                    </div>
                    </div>
                    <div class="text-center">
                      <button type="submit" class="btn w-100 my-2 mb-2 shadow" name="cmdLogin" style="color: white; background-color:rgb(36, 36, 36);">Sign in</button>
                    </div>
                    <p class="mt-4 text-sm text-center">
                    <div class="copyright text-center text-sm text-dark text-lg-center" align="center">
                      All Right Reserved by
                      <a href="#" class="font-weight-bold" style="color: #2d22c5ff;" target="_blank">ISB HQ Team</a>
                    </div>
                    </p>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
        <footer class="footer position-absolute bottom-2 py-2 w-100">
          <div class="container">
            <div class="row">
              <div class="col-12 col-lg-12 col-md-12" align="center">
                <div class="copyright text-center text-sm text-white text-lg-center" align="center">
                  Copyright © 2025
                  <a href="#" class="font-weight-bold text-white" target="_blank">FRSystem V5</a>
                </div>
              </div>
            </div>
          </div>
        </footer>
      </div>
    </main>
    <!--   Core JS Files   -->
    <script src="assets/js/core/popper.min.js"></script>
    <script src="assets/js/core/bootstrap.min.js"></script>
    <script src="assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="assets/js/plugins/smooth-scrollbar.min.js"></script>
    <script>
      var win = navigator.platform.indexOf('Win') > -1;
      if (win && document.querySelector('#sidenav-scrollbar')) {
        var options = {
          damping: '0.5'
        }
        Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
      }
    </script>
    <!-- Github buttons -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="../assets/js/material-dashboard.min.js?v=3.0.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <?php
    // Check if there is a status message in the session
    if (isset($_SESSION['status'])) {
        $status = $_SESSION['status'];   // 'success' or 'error'
        $message = $_SESSION['message']; // Error or success message
        $message2 = $_SESSION['message2']; // Additional message (optional)

        // Clear session message after use
        unset($_SESSION['status']);
        unset($_SESSION['message']);
        unset($_SESSION['message2']);

        if ($status == 'success') {
            echo '<script>
                            document.addEventListener("DOMContentLoaded", function() {
                                Swal.fire({
                                    title: "' . $message . '",
                                    text: "",
                                    icon: "success",
                                });
                            });
                        </script>';
        } else {
            echo '<script>
                            document.addEventListener("DOMContentLoaded", function() {
                                Swal.fire({
                                    title: "' . $message . '",
                                    text: "' . $message2 . '",
                                    icon: "error",
                                });
                            });
                        </script>';
        }
    }
    ?>
  </body>

  </html>
<?php } ?>