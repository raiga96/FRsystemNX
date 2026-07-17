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
$page = 'sys_acc';
//$div =

// Connection for database
require 'includes/connect.php';
// Auto Select Sarawaknet Line or Not
require 'includes/line.php';
// Login function
require 'includes/login_function.php';


//Controller for Role and permission
require 'includes/controller/role_controller.php';


//If Get Division is empty
if (isset($_GET['division']) && !empty($_GET['division'])) {
  // $div is set and not empty
  $div = $_GET['division'];
} else {
  $div = $divName;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="./assets/img/favicon.svg">
  <link rel="icon" type="image/png" href="./assets/img/favicon.svg">
  <title>
    COMMANDS
  </title>
  <!--     Fonts and icons     -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Roboto+Slab:400,700" />
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

  <!-- DataTable -->
  <link rel="stylesheet" href="./assets/datatables/dataTables.dataTables.css">


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
    <div class="container-fluid py-4">
      <?php
      $sqlD = "SELECT * FROM division WHERE DIV_NAME = '$div'";
      $resD = $conn->query($sqlD);
      $rowD = $resD->fetch_assoc();
      $div_id = $rowD['DIV_ID'];

      $sql = "SELECT COUNT(*) AS total_user FROM system_access";
      $res = $conn->query($sql);
      $row = $res->fetch_assoc();
      $total_user = $row['total_user'];
      ?>
      <div class="row mt-4">

        <div class="col-xl-5 col-sm-6 mb-xl-0 mb-4 ">
          <div class="card bg-gradient-dark">
            <div class="card-header p-3 pt-2 bg-gradient-dark">
              <div class="text-start pt-1">
                <p class="text-lg mb-3 text-capitalize text-white font-weight-bold">Total User</p>
                <p class="text-sm mb-0 text-white">total</p>
                <h1 class="mb-0 text-white"><span class="count p-3"><?php echo round($total_user, 2) ?></span><span class="text-lg ms-n1"></span></h1>
              </div>
            </div>
            <div class="card-footer pt-2 pb-2 m-2" align="center">
              <div class="row align-items-center">
                <div class="col-sm-3 col-xl-3">
                  <h6 class="mb-0 text-white">Survey</h6>
                  <p class="mb-0"><span class="text-white text-sm font-weight-bolder">0</span></p>
                </div>
                <div class="col-sm-3 col-xl-3">
                  <h6 class="mb-0 text-white">Land</h6>
                  <p class="mb-0"><span class="text-white text-sm font-weight-bolder">0</span></p>
                </div>
                <div class="col-sm-3 col-xl-3">
                  <h6 class="mb-0 text-white">Valuation</h6>
                  <p class="mb-0"><span class="text-white text-sm font-weight-bolder">0</span></p>
                </div>
                <div class="col-sm-3 col-xl-3">
                  <h6 class="mb-0 text-white">Managements</h6>
                  <p class="mb-0"><span class="text-white text-sm font-weight-bolder">0</span></p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-5 col-sm-6 mb-xl-0 mb-4 ">
          <div class="card bg-gradient-dark">
            <div class="card-header p-3 pt-2 bg-gradient-dark">
              <div class="text-start pt-1">
                <p class="text-lg mb-3 text-capitalize text-white font-weight-bold">Survey User</p>
                <p class="text-sm mb-0 text-white">percentage</p>
                <h1 class="mb-0 text-white"><span class="count p-3"><?php echo round(0, 2) ?></span><span class="text-lg ms-n1"></span></h1>
              </div>
            </div>
            <div class="card-footer pt-2 pb-2 m-2" align="center">
              <div class="row align-items-center">
                <div class="col-sm-2 col-xl-2">
                  <h6 class="mb-0 text-white">SS</h6>
                  <p class="mb-0"><span class="text-white text-sm font-weight-bolder">0</span></p>
                </div>
                <div class="col-sm-2 col-xl-2">
                  <h6 class="mb-0 text-white">ASS</h6>
                  <p class="mb-0"><span class="text-white text-sm font-weight-bolder">0</span></p>
                </div>
                <div class="col-sm-2 col-xl-2">
                  <h6 class="mb-0 text-white">FI</h6>
                  <p class="mb-0"><span class="text-white text-sm font-weight-bolder">0</span></p>
                </div>
                <div class="col-sm-2 col-xl-2">
                  <h6 class="mb-0 text-white">SD</h6>
                  <p class="mb-0"><span class="text-white text-sm font-weight-bolder">0</span></p>
                </div>
                <div class="col-sm-2 col-xl-2">
                  <h6 class="mb-0 text-white">SC</h6>
                  <p class="mb-0"><span class="text-white text-sm font-weight-bolder">0</span></p>
                </div>
                <div class="col-sm-2 col-xl-2">
                  <h6 class="mb-0 text-white">TA</h6>
                  <p class="mb-0"><span class="text-white text-sm font-weight-bolder">0</span></p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row mt-4">
        <div class="col-xl-12 col-md-12 col-sm-12 mt-4 mb-3">
          <div class="card">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-dark shadow-info border-radius-lg pt-4 pb-3">
                <div class="row">
                  <div class="col-lg-6">
                    <h6 class="text-white text-capitalize ps-3">User List</h6>
                    <p class="text-white text-sm ps-3 mb-0 ">by division</p>
                  </div>
                  <div class="col-lg-6 text-end">
                    <a href="addUser.php" class="btn bg-gradient-info mb-0 me-3"><i class="fa fa-add"></i> Add User</a>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-body px-0 pb-2">
              <div class="table-responsive" style="padding-left:2%; padding-right:2%">
                <table class="table table-hover align-items-center mb-0 display" id="myTable">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">UID</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Name</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Role</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Division</th>

                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $division = $div;
                    $sqlProj = "SELECT * FROM system_access AS a LEFT JOIN system_role AS b ON b.role_id = a.role_id";
                    $resProj = $conn->query($sqlProj);
                    if ($resProj->num_rows > 0) {
                      while ($row = $resProj->fetch_assoc()) {
                        $uid = $row['uid'];
                        $role = $row['role_name'];

                        $sqlUser = "SELECT * FROM userinfo AS a JOIN division AS b ON b.DIV_ID = a.division JOIN users AS c ON c.uid = a.uid WHERE a.uid = '$uid'";
                        $resUser = $conn1->query($sqlUser);
                        $rowUser = $resUser->fetch_assoc();

                        $fullname = $rowUser['fullname'];
                        $section = $rowUser['section'];
                        $division = $rowUser['DIV_NAME'];
                        $email = $rowUser['user_email'];
                    ?>
                        <tr>
                          <td class="align-middle text-center">
                            <p class="text-sm font-weight-bold mb-0 text-weight-bold"><?php echo $uid ?></p>
                          </td>
                          <td>
                            <a href="accProfile.php?uid=<?php echo $uid ?>" class="text-secondary font-weight-bold text-sm">
                              <div class="d-flex px-2 py-1">
                                <div class="d-flex flex-column justify-content-center">
                                  <h6 class="mb-0 text-sm opacity-10 text-uppercase"><?php echo $fullname ?></h6>
                                  <p class="text-xs mb-0"><?php echo $email ?></p>
                                </div>
                              </div>
                            </a>
                          </td>
                          <td class="align-middle text-center">
                            <p class="text-sm font-weight-bold mb-0 text-weight-bold"><?php echo $role ?></p>
                          </td>
                          <td class="align-middle text-center text-sm">
                            <p class="text-sm font-weight-bold mb-0 text-weight-bold text-uppercase"><?php echo $division ?></p>
                          </td>


                        </tr>
                    <?php }
                    } ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php
      include("footer.php");
      ?>
    </div>
  </main>


  <!--   Core JS Files   -->
  <script src="./assets/js/core/popper.min.js"></script>
  <script src="./assets/js/core/bootstrap.min.js"></script>
  <script src="./assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="./assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script src="./assets/js/plugins/chartjs.min.js"></script>
  <script src="./assets/js/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <?php
  // Check if there is a status message in the session
  if (isset($_SESSION['status'])) {
    $status = $_SESSION['status'];   // 'success' or 'error'
    $message = $_SESSION['message']; // Error or success message

    // Clear session message after use
    unset($_SESSION['status']);
    unset($_SESSION['message']);

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
                                    title: "An error occured!",
                                    text: "' . $message . '",
                                    icon: "error",
                                });
                            });
                        </script>';
    }
  }
  ?>
  <!-- Script for count animation -->
  <script>
    $('.count1').each(function() {
      $(this).prop('Counter', 0).animate({
        Counter: $(this).text().replace(/,/g, '')
      }, {
        duration: 4000,
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
        duration: 4000,
        easing: 'swing',
        step: function(now) {
          $(this).text(Math.ceil(now).toLocaleString());
        }
      });
    });
  </script>
  <!-- End Script for count animation -->


  <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="./assets/js/material-dashboard.min.js?v=3.0.0"></script>

  <script src="./assets/datatables/dataTables.js"></script>
  <script>
    $(document).ready(function() {
      $('#myTable').DataTable();
    });
  </script>
</body>

</html>