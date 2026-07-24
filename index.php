<!--
=========================================================
* Material Dashboard 2 - v3.0.0
=========================================================

* Original Product Page: https://www.creative-tim.com/product/material-dashboard
* Copyright 2021 Creative Tim (https://www.creative-tim.com)
* Licensed under MIT (https://www.creative-tim.com/license)
* Coded by Creative Tim
* Modified by Darmizi & Hillary for Survey System

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->
<?php
session_start();
$page = 'index';

// Connection for database
require 'includes/connect.php';
// Auto Select Sarawaknet Line or Not
require 'includes/line.php';
// Login function
require 'includes/login_function.php';


//Controller for Role and permission
require 'includes/controller/role_controller.php';

// Role & Division check handled inside index.php template (hq_dashboard vs div_dashboard)
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="./assets/img/favicon.svg">
  <link rel="icon" type="image/png" href="./assets/img/favicon.svg">
  <title>
    FRSystem
  </title>
  <!--     Fonts and icons     -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" />
  <!-- Nucleo Icons -->
  <link href="./assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="./assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="./assets/fontawesome/css/all.css">
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <!-- Material Icons -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
  <!-- CSS Files -->
  <link id="pagestyle" href="./assets/css/material-dashboard.css?v=3.0.0" rel="stylesheet" />

  <!--3D CHART JS & CSS-->
  <!--<script src="https://cdn.anychart.com/releases/8.11.0/js/anychart-base.min.js"></script>-->
  <script src="./assets/js/anychart/anychart-base.min.js"></script>
  <!--<script src="https://cdn.anychart.com/releases/8.11.0/js/anychart-exports.min.js"></script>-->
  <script src="./assets/js/anychart/anychart-exports.min.js"></script>
  <!--<script src="https://cdn.anychart.com/releases/8.11.0/js/anychart-ui.min.js"></script>-->
  <script src="./assets/js/anychart/anychart-ui.min.js"></script>
  <!--<link rel="stylesheet" href="https://cdn.anychart.com/releases/8.11.0/css/anychart-ui.min.css" />-->
  <link rel="stylesheet" href="./assets/js/anychart/anychart-ui.min.css" />

  <!--<script src="https://cdn.anychart.com/releases/8.11.0/js/anychart-core.min.js"></script>-->
  <script src="./assets/js/anychart/anychart-core.min.js"></script>
  <!--<script src="https://cdn.anychart.com/releases/8.11.0/js/anychart-cartesian-3d.min.js"></script>-->
  <script src="./assets/js/anychart/anychart-cartesian-3d.min.js"></script>

  <!--<script src="https://cdn.anychart.com/releases/8.11.0/js/anychart-pie.min.js"></script>-->
  <script src="./assets/js/anychart/anychart-pie.min.js"></script>

</head>

<body class="g-sidenav-show  bg-gray-200">
  <?php
  include("sidebar.php");
  ?>
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->
    <?php
    include("navbar.php");
    ?>
    <!-- End Navbar -->
    <?php
    if ($divName == 'Headquarters') {

      include("hq_dashboard.php");
    } else {

      include("div_dashboard.php");
    }
    ?>

    <!--   Core JS Files   -->
    <script src="./assets/js/core/popper.min.js"></script>
    <script src="./assets/js/core/bootstrap.min.js"></script>
    <script src="./assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="./assets/js/plugins/smooth-scrollbar.min.js"></script>
    <script src="./assets/js/plugins/chartjs.min.js"></script>
    <script src="./assets/js/jquery.min.js"></script>
    <!-- Script for count animation -->
    <script>
      $('.count1').each(function() {
        $(this).prop('Counter', 0).animate({
          Counter: $(this).text().replace(/,/g, '')
        }, {
          duration: 0,
          easing: 'swing',
          step: function(now) {
            $(this).text(Math.abs(now).toLocaleString(undefined, {
              minimumFractionDigits: 2
            }));
          }
        });
      });
    </script>
    <script>
      $('.count').each(function() {
        $(this).prop('Counter', 0).animate({
          Counter: $(this).text().replace(/,/g, '')
        }, {
          duration: 0,
          easing: 'swing',
          step: function(now) {
            $(this).text(Math.ceil(now).toLocaleString());
          }
        });
      });
    </script>
    <!-- End Script for count animation -->
    <script src="./assets/js/material-dashboard.min.js?v=3.0.0"></script>
</body>

</html>