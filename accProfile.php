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
            $user_div = $row['division'] ?? null;

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
                                                <h6 align="left">Internal User | Edit</h6>
                                                <hr>
                                                <div class="col-lg-12">
                                                    <form action="includes/account_edit.php" method="POST">
                                                        <input type="text" name="uid" value="<?php echo $uid ?>" hidden>
                                                        <div class="row">
                                                            <div class="row mb-3">
                                                                <div class="col-md-4" align="left">
                                                                    <label class="font-weight-bolder text-dark py-2">User ID <small class="text-danger">*</small></label>
                                                                </div>
                                                                <div class="col-md-8" align='left'>
                                                                    <div class="input-group input-group-outline">
                                                                        <input type="text" name="user_id" class="form-control" value="<?php echo $user_id ?>" required>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-3">
                                                                <div class="col-md-4" align="left">
                                                                    <label class="font-weight-bolder text-dark py-2">User's Full Name <small class="text-danger">*</small></label>
                                                                </div>
                                                                <div class="col-md-8" align='left'>
                                                                    <div class="input-group input-group-outline">
                                                                        <input type="text" name="fullname" class="form-control" value="<?php echo $fullname ?>" required>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-3">
                                                                <div class="col-md-4" align="left">
                                                                    <label class="font-weight-bolder text-dark py-2">LDAP Login ?<small class="text-danger">*</small></label>
                                                                </div>
                                                                <div class="col-md-8" align='left'>
                                                                    <div class="input-group input-group-outline">
                                                                        <select name="ldap_login" class="form-select form-control" id="">
                                                                            <?php
                                                                            $sqlL = "SELECT * FROM userinfo WHERE uid = '$uid'";
                                                                            $resL = $conn->query($sqlL);
                                                                            $rowL = $resL->fetch_assoc();
                                                                            $ldap = $rowL['ldap'];

                                                                            if ($resL->num_rows > 0) {
                                                                                if ($ldap == 'Y') {
                                                                                    echo '<option value="Y" selected>Yes</option>';
                                                                                    echo '<option value="N">No</option>';
                                                                                } else {
                                                                                    echo '<option value="Y">Yes</option>';
                                                                                    echo '<option value="N" selected>No</option>';
                                                                                }
                                                                            } else {
                                                                                echo '<option value="Y">Yes</option>';
                                                                                echo '<option value="N">No</option>';
                                                                            }
                                                                            ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-3">
                                                                <div class="col-md-4" align="left">
                                                                    <label class="font-weight-bolder text-dark py-2">Account Status <small class="text-danger">*</small></label>
                                                                </div>
                                                                <div class="col-md-8" align='left'>
                                                                    <div class="input-group input-group-outline">
                                                                        <select name="acc_status" class="form-select form-control" id="">
                                                                            <option value="Y">Active</option>
                                                                            <option value="N">Inactive</option>
                                                                            <option value="L">Locked</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-3">
                                                                <div class="col-md-4" align="left">
                                                                    <label class="font-weight-bolder text-dark py-2">Email Address <small class="text-danger">*</small></label>
                                                                </div>
                                                                <div class="col-md-8" align='left'>
                                                                    <div class="input-group input-group-outline">
                                                                        <input type="text" name="user_email" class="form-control" value="<?php echo $email ?>" required>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-3">
                                                                <div class="col-md-4" align="left">
                                                                    <label class="font-weight-bolder text-dark py-2">Branch <small class="text-danger">*</small></label>
                                                                </div>
                                                                <div class="col-md-8" align='left'>
                                                                    <div class="input-group input-group-outline">
                                                                        <select name="user_branch" class="form-select form-control text-uppercase" id="">
                                                                            <?php if ($branch == null) { ?>
                                                                                <option value="" selected disabled>- - -please select- - -</option>
                                                                            <?php } else { ?>
                                                                                <option value="" disabled>- - -please select- - -</option>
                                                                                <option value="<?php echo $branch_id ?>" selected><?php echo $branch ?></option>
                                                                            <?php }
                                                                            $sqlB = "SELECT * FROM brasecunit WHERE BSU_name LIKE '%branch%' AND BSU_id NOT IN ('$branch_id')";
                                                                            $resB = $conn->query($sqlB);
                                                                            while ($rowB = $resB->fetch_assoc()) {
                                                                            ?>
                                                                                <option value="<?php echo $rowB['BSU_id'] ?>"><?php echo $rowB['BSU_name'] ?></option>
                                                                            <?php } ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-3">
                                                                <div class="col-md-4" align="left">
                                                                    <label class="font-weight-bolder text-dark py-2">Section <small class="text-danger">*</small></label>
                                                                </div>
                                                                <div class="col-md-8" align='left'>
                                                                    <div class="input-group input-group-outline">
                                                                        <select name="user_section" class="form-select form-control text-uppercase" id="">

                                                                            <!-- Default placeholder -->
                                                                            <option value="" disabled <?php echo ($section == null ? 'selected' : ''); ?>>
                                                                                - - - please select - - -
                                                                            </option>

                                                                            <!-- If section exists, show it -->
                                                                            <?php if ($section != null) { ?>
                                                                                <option value="<?php echo $rowB['BSU_id']; ?>" selected>
                                                                                    <?php echo $section_name; ?>
                                                                                </option>
                                                                            <?php } ?>

                                                                            <!-- Load all available sections -->
                                                                            <?php
                                                                            $sqlB = "SELECT * FROM brasecunit WHERE BSU_name NOT LIKE '%branch%' AND BSU_id != '$section'";
                                                                            $resB = $conn->query($sqlB);

                                                                            while ($rowB = $resB->fetch_assoc()) { ?>
                                                                                <option value="<?php echo $rowB['BSU_id']; ?>">
                                                                                    <?php echo $rowB['BSU_name']; ?>
                                                                                </option>
                                                                            <?php } ?>

                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row mb-3">
                                                                <div class="col-md-4" align="left">
                                                                    <label class="font-weight-bolder text-dark py-2">Division <small class="text-danger">*</small></label>
                                                                </div>
                                                                <div class="col-md-8" align='left'>
                                                                    <div class="input-group input-group-outline">
                                                                        <select name="user_division" class="form-select form-control text-uppercase" id="">
                                                                            <?php
                                                                            $sqlB = "SELECT * FROM division WHERE DIV_ID = '$user_div'";
                                                                            $resB = $conn->query($sqlB);
                                                                            $rowB = $resB->fetch_assoc();
                                                                            $div_name = $rowB['DIV_NAME'] ?? null;
                                                                            ?>
                                                                            <?php if ($user_div == null) { ?>
                                                                                <option value="" selected disabled>- - -please select- - -</option>
                                                                            <?php } ?>
                                                                            <option value="" disabled>- - -please select- - -</option>
                                                                            <?php
                                                                            $sqlB = "SELECT * FROM division ";
                                                                            $resB = $conn->query($sqlB);
                                                                            while ($rowB = $resB->fetch_assoc()) {
                                                                                $divID = $rowB['DIV_ID'];
                                                                            ?>

                                                                                <option value="<?php echo $rowB['DIV_ID']; ?>"
                                                                                    <?php if ($divID == $user_div) {
                                                                                        echo 'selected';
                                                                                    } ?>><?php echo $rowB['DIV_NAME'] ?></option>
                                                                            <?php } ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <?php
                                                            $sql1 = "SELECT * FROM system_role AS a JOIN system_access AS b ON b.role_id = a.role_id WHERE b.uid = '$uid'";
                                                            $res1 = $conn->query($sql1);
                                                            $row1 = $res1->fetch_assoc();
                                                            $role = $row1['role_name'] ?? null;
                                                            $role_id = $row1['role_id'] ?? null;
                                                            ?>
                                                            <div class="row mb-3">
                                                                <div class="col-md-4" align="left">
                                                                    <label class="font-weight-bolder text-dark py-2">Role <small class="text-danger">*</small></label>
                                                                </div>
                                                                <div class="col-md-8" align='left'>
                                                                    <div class="input-group input-group-outline">
                                                                        <select name="user_role" class="form-select form-control text-uppercase" id="">
                                                                            <?php if ($role_id == null) { ?>
                                                                                <option value="" selected disabled>- - -please select- - -</option>
                                                                            <?php } else { ?>
                                                                                <option value="" selected disabled>- - -please select- - -</option>
                                                                                <option value="<?php echo $role_id ?>" selected><?php echo $role ?></option>
                                                                            <?php }
                                                                            $sqlB = "SELECT * FROM system_role WHERE role_id != '$role_id'";
                                                                            $resB = $conn->query($sqlB);
                                                                            while ($rowB = $resB->fetch_assoc()) {
                                                                            ?>
                                                                                <option value="<?php echo $rowB['role_id'] ?>"><?php echo $rowB['role_name'] ?></option>
                                                                            <?php } ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row mb-3">
                                                                <div class="col-md-4" align="left">
                                                                    <label class="font-weight-bolder text-dark py-2">Last Logged On</label>
                                                                </div>
                                                                <div class="col-md-8" align='left'>
                                                                    <b class="text-sm text-weight-bolder py-3"><?php echo date("d M Y : H:i:s a") ?></b>
                                                                </div>
                                                            </div>
                                                            <div align="right">
                                                                <button class="btn btn-primary shadow">Save Changes</button>
                                                                <button class="btn btn-outline-primary shadow">Cancel</button>
                                                            </div>

                                                        </div>
                                                    </form>
                                                    <hr>
                                                    <h6 align="left">Assign Group</h6>
                                                    <div align="left">
                                                        <a href="addGroup.php?uid=<?php echo $uid ?>"><button class="btn btn-outline-secondary"><i class="fa fa-add"></i> Add User Group</button></a>
                                                        <a href="removeGroup.php?uid=<?php echo $uid ?>"><button class="btn btn-outline-danger"><i class="fa fa-trash"></i> Delete User Group</button></a>
                                                    </div>

                                                    <div class="table-responsive p-0 mt-3 mb-5" align="left">
                                                        <table class="table table-hover align-items-left mb-0">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-left text-dark text-sm font-weight-bolder">Group Code</th>
                                                                    <th class="text-left text-dark text-sm font-weight-bolder">Group Name</th>
                                                                    <th class="text-left text-dark text-sm font-weight-bolder">Group System</th>
                                                                    <th class="text-left text-dark text-sm font-weight-bolder">Group Division</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $sqlG = "SELECT * FROM user_group AS a JOIN group_desc AS b ON a.user_group = b.ug_id WHERE a.uid = '$uid'";
                                                                $resG = $conn->query($sqlG);
                                                                if ($resG->num_rows > 0) {
                                                                    while ($rowG = $resG->fetch_assoc()) {
                                                                ?>
                                                                        <tr>
                                                                            <td class="text-left text-secondary text-sm font-weight-bolder px-4"><?php echo $rowG['group_code'] ?></td>
                                                                            <td class="text-left text-secondary text-sm font-weight-bolder px-4"><?php echo $rowG['group_name'] ?></td>
                                                                            <td class="text-left text-secondary text-sm font-weight-bolder px-4"><?php echo $rowG['group_system'] ?></td>
                                                                            <td class="text-left text-secondary text-sm font-weight-bolder px-4">
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
                                                        </table>
                                                    </div>
                                                    <hr>
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