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

$uid = $_GET['uid'] ?? '';
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
        <div class="container-fluid">
            <?php
            $sqlD = "SELECT * FROM division WHERE DIV_NAME = '$div'";
            $resD = $conn->query($sqlD);
            $rowD = $resD->fetch_assoc();
            $div_id = $rowD['DIV_ID'];

            $sql = "SELECT * FROM userinfo AS a JOIN users AS b ON b.uid = a.uid WHERE a.uid = '$uid'";
            $res = $conn1->query($sql);
            $row = $res->fetch_assoc();
            $fullname = $row['fullname'];
            $user_id = $row['user_id'];
            $status = $row['status'];
            $email = $row['user_email'];

            $sql1 = "SELECT * FROM userinfo AS a JOIN brasecunit AS b ON a.branch = b.BSU_id WHERE a.uid = '$uid'";
            $res1 = $conn->query($sql1);
            $row1 = $res1->fetch_assoc();
            $branch_id = $row1['branch'] ?? null;
            $branch = $row1['BSU_name'] ?? null;
            $section = $row1['section'] ?? null;

            $sqlS = "SELECT * FROM brasecunit WHERE BSU_id = '$section'";
            $resS = $conn->query($sqlS);
            $rowS = $resS->fetch_assoc();
            $section_name = $rowS['BSU_name'] ?? null;
            ?>
            <div class="col-12 text-center">
                <div class="multisteps-form mb-5">
                    <div class="row">
                        <div class="col-12 col-lg-8 mx-auto my-5">
                            <div class="card">
                                <div class="card-body">
                                    <div class="multisteps-form__panel js-active" data-animation="FadeIn">
                                        <div class="multisteps-form__content">
                                            <div class="row">
                                                <h6 align="left">Add User Group</h6>
                                                <hr>
                                                <div class="col-lg-12">
                                                    <div class="table-responsive p-0 mt-3 mb-5" align="left">
                                                        <table class="table table-hover align-items-left mb-0" id="myTable">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-left text-dark text-sm font-weight-bolder">Action</th>
                                                                    <th class="text-left text-dark text-sm font-weight-bolder">Group Code</th>
                                                                    <th class="text-left text-dark text-sm font-weight-bolder">Group Name</th>
                                                                    <th class="text-left text-dark text-sm font-weight-bolder">Group System</th>
                                                                    <th class="text-left text-dark text-sm font-weight-bolder">Group Division</th>
                                                                </tr>
                                                            <tbody>
                                                                <?php
                                                                $sqlG = "SELECT * FROM group_desc WHERE ug_id IN (SELECT user_group FROM user_group WHERE uid = '$uid')";
                                                                $resG = $conn->query($sqlG);
                                                                if ($resG->num_rows > 0) {
                                                                    while ($rowG = $resG->fetch_assoc()) {
                                                                ?>
                                                                        <tr>
                                                                            <td>
                                                                                <div>
                                                                                    <form method="post" action="includes/removeGroup_inc.php">
                                                                                        <input type="hidden" name="uid" value="<?php echo $uid ?>">
                                                                                        <input type="hidden" name="group_id" value="<?php echo $rowG['ug_id'] ?>">
                                                                                        <button type="submit" name="removeGroup" class="btn btn-outline-danger mb-0 px-3"><i class="fa fa-trash"></i></button>
                                                                                    </form>

                                                                                </div>
                                                                            </td>
                                                                            <td class="pt-3 align-item-center text-left text-secondary text-sm font-weight-bolder"><?php echo $rowG['group_code'] ?></td>
                                                                            <td class="pt-3 text-left text-secondary text-sm font-weight-bolder"><?php echo $rowG['group_name'] ?></td>
                                                                            <td class="pt-3 text-left text-secondary text-sm font-weight-bolder"><?php echo $rowG['group_system'] ?></td>
                                                                            <td class="pt-3 text-left text-secondary text-sm font-weight-bolder">
                                                                                <?php
                                                                                $sqlDiv = "SELECT * FROM division WHERE DIV_ID = '" . $rowG['group_division'] . "'";
                                                                                $resDiv = $conn->query($sqlDiv);
                                                                                $rowDiv = $resDiv->fetch_assoc();
                                                                                echo $rowG['DIV_NAME'] = $rowDiv['DIV_NAME'];
                                                                                ?>
                                                                            </td>
                                                                        </tr>
                                                                <?php
                                                                    }
                                                                }
                                                                ?>

                                                            </tbody>
                                                            </thead>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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