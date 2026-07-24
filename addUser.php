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
                <div class="col-xl-12 col-md-12 col-sm-12 mt-4 mb-3">
                    <div class="card">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-dark shadow-info border-radius-lg pt-4 pb-3">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <h6 class="text-white text-capitalize ps-3">User List</h6>
                                        <p class="text-white text-sm ps-3 mb-0 ">from QREASY</p>
                                    </div>
                                    <div class="col-lg-6 text-end">
                                        <a href="addNewUser.php" class="btn bg-gradient-info mb-0 me-3"><i class="fa fa-add"></i> New User</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive" style="padding-left:2%; padding-right:2%">
                                <table class="table table-hover align-items-center mb-0 display" id="myTable">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7">UID</th>
                                            <th class="text-uppercase text-dark text-xxs font-weight-bolder opacity-7">Name</th>
                                            <th class="text-center text-uppercase text-dark text-xxs font-weight-bolder opacity-7">Division</th>
                                            <th class="text-center text-uppercase text-dark text-xxs font-weight-bolder opacity-7">Branch/Section</th>
                                            <th class="text-center text-uppercase text-dark text-xxs font-weight-bolder opacity-7 ps-2">Action</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $division = $div;
                                        $sqlProj = "SELECT * FROM users AS a JOIN userinfo AS b ON b.uid = a.uid JOIN division AS c ON c.DIV_ID = b.division JOIN brasecunit AS d ON d.BSU_id = b.section";
                                        $resProj = $conn1->query($sqlProj);
                                        if ($resProj->num_rows > 0) {
                                            while ($row = $resProj->fetch_assoc()) {
                                                $uid = $row['uid'];



                                                $fullname = $row['fullname'];
                                                $section = $row['section'];
                                                $division = $row['DIV_NAME'];
                                                $email = $row['user_email'];
                                                $brasect = $row['BSU'];
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
                                                    <td class="align-middle text-center text-sm">
                                                        <p class="text-sm font-weight-bold mb-0 text-weight-bold text-uppercase"><?php echo $division ?></p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <p class="text-sm font-weight-bold mb-0 text-weight-bold"><?php echo $brasect ?></p>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <div>
                                                            <?php
                                                            $sqlCheck = "SELECT * FROM system_access WHERE uid = '$uid'";
                                                            $resCheck = $conn->query($sqlCheck);
                                                            if ($resCheck->num_rows > 0) { ?>
                                                                <a href="accProfile.php?uid=<?php echo $uid ?>" class="btn btn-outline-warning p-2 mb-0 me-3" title="Edit User"><i class="fa fa-edit"></i> edit</a>
                                                            <?php } else { ?>
                                                                <form action="includes/addUser.php" method="post">
                                                                    <input type="hidden" name="uid" value="<?php echo $uid ?>">
                                                                    <button type="submit" class="btn btn-outline-info p-2 mb-0 me-3" title="Add User"><i class="fa fa-add"></i> add</button>
                                                                </form>

                                                            <?php }

                                                            ?>
                                                        </div>
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