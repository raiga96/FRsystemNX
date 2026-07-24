<?php
session_start();
$page = 'sys_acc';

require 'includes/connect.php';
require 'includes/line.php';
require 'includes/login_function.php';
require 'includes/controller/role_controller.php';

$targetUsername = trim((string)($_GET['usr'] ?? $_POST['username'] ?? ''));
$alertMsg = '';
$alertType = '';
$userData = null;

if (empty($targetUsername)) {
    header("Location: accManagement.php");
    exit();
}

if (isset($conn) && $conn instanceof mysqli) {
    $uSafe = $conn->real_escape_string($targetUsername);

    // Form submission processing
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_edit_user'])) {
        $newName = trim((string)($_POST['name'] ?? ''));
        $newEmail = trim((string)($_POST['email'] ?? ''));
        $newRole = strtoupper(trim((string)($_POST['Role'] ?? 'NU')));
        $newDivision = trim((string)($_POST['Division'] ?? 'Headquarters'));
        $newActive = strtoupper(trim((string)($_POST['active'] ?? 'Y'))) === 'Y' ? 'Y' : 'N';
        $newLdap = strtoupper(trim((string)($_POST['ldap'] ?? 'N'))) === 'Y' ? 'Y' : 'N';
        $newPassword = trim((string)($_POST['password'] ?? ''));

        if (!empty($newName)) {
            $nameEsc = $conn->real_escape_string($newName);
            $emailEsc = $conn->real_escape_string($newEmail);
            $roleEsc = $conn->real_escape_string($newRole);
            $divEsc = $conn->real_escape_string($newDivision);

            if (!empty($newPassword)) {
                $hashedPass = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmtUp = $conn->prepare("UPDATE `user` SET `name` = ?, `email` = ?, `Role` = ?, `Division` = ?, `active` = ?, `ldap` = ?, `password` = ? WHERE `username` = ?");
                if ($stmtUp) {
                    $stmtUp->bind_param("ssssssss", $newName, $newEmail, $newRole, $newDivision, $newActive, $newLdap, $hashedPass, $targetUsername);
                    if ($stmtUp->execute()) {
                        $alertMsg = "USER DETAILS FOR " . strtoupper($targetUsername) . " UPDATED SUCCESSFULLY!";
                        $alertType = "success";
                    } else {
                        $alertMsg = "FAILED TO UPDATE USER ACCOUNT: " . $stmtUp->error;
                        $alertType = "danger";
                    }
                    $stmtUp->close();
                }
            } else {
                $stmtUp = $conn->prepare("UPDATE `user` SET `name` = ?, `email` = ?, `Role` = ?, `Division` = ?, `active` = ?, `ldap` = ? WHERE `username` = ?");
                if ($stmtUp) {
                    $stmtUp->bind_param("sssssss", $newName, $newEmail, $newRole, $newDivision, $newActive, $newLdap, $targetUsername);
                    if ($stmtUp->execute()) {
                        $alertMsg = "USER DETAILS FOR " . strtoupper($targetUsername) . " UPDATED SUCCESSFULLY!";
                        $alertType = "success";
                    } else {
                        $alertMsg = "FAILED TO UPDATE USER ACCOUNT: " . $stmtUp->error;
                        $alertType = "danger";
                    }
                    $stmtUp->close();
                }
            }
        } else {
            $alertMsg = "FULL NAME CANNOT BE EMPTY.";
            $alertType = "warning";
        }
    }

    // Fetch user details safely using Prepared Statement
    $stmtFetch = $conn->prepare("SELECT * FROM `user` WHERE `username` = ? LIMIT 1");
    if ($stmtFetch) {
        $stmtFetch->bind_param("s", $targetUsername);
        $stmtFetch->execute();
        $resU = $stmtFetch->get_result();
        if ($resU && $resU->num_rows > 0) {
            $userData = $resU->fetch_assoc();
        } else {
            header("Location: accManagement.php?error=usernotfound");
            exit();
        }
        $stmtFetch->close();
    }
}

// Predefined Divisions
$divisionsList = [
    'Headquarters', 'Kuching', 'Samarahan', 'Serian', 'Sri Aman', 'Betong',
    'Sarikei', 'Sibu', 'Mukah', 'Kapit', 'Bintulu', 'Miri', 'Limbang'
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="./assets/img/favicon.svg">
  <link rel="icon" type="image/png" href="./assets/img/favicon.svg">
  <title>EDIT USER - FRSYSTEM</title>

  <!-- Fonts and icons -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" />
  <link href="./assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="./assets/css/nucleo-svg.css" rel="stylesheet" />
  <link rel="stylesheet" href="./assets/fontawesome/css/all.css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
  <link id="pagestyle" href="./assets/css/material-dashboard.css?v=3.0.0" rel="stylesheet" />
</head>

<body class="g-sidenav-show bg-gray-200">
  <?php include("sidebar.php"); ?>

  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <?php include("navbar.php"); ?>

    <div class="container-fluid py-4">
      <!-- Header Banner -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card bg-gradient-dark border-0 shadow-lg position-relative overflow-hidden p-3" style="border-radius: 1rem; background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
            <div class="row align-items-center">
              <div class="col-lg-8 col-md-7">
                <span class="badge bg-white text-dark mb-2 text-uppercase tracking-wider px-3 py-2 font-weight-bolder"><i class="fas fa-user-gear me-1"></i> User Administration</span>
                <h2 class="text-white font-weight-bolder mb-1">EDIT USER ACCOUNT: <?php echo htmlspecialchars(strtoupper($userData['username'] ?? '')); ?></h2>
                <p class="text-white opacity-9 text-sm mb-0 text-uppercase">UPDATE PROFILE INFORMATION, SYSTEM ROLE, DIVISION, AND RESET PASSWORD.</p>
              </div>
              <div class="col-lg-4 col-md-5 text-end">
                <a href="accManagement.php" class="btn btn-light text-dark font-weight-bold text-uppercase mb-0 shadow-sm">
                  <i class="fas fa-arrow-left me-1"></i> BACK TO USER LIST
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <?php if (!empty($alertMsg)) { ?>
        <div class="alert alert-<?php echo $alertType; ?> alert-dismissible text-white text-uppercase font-weight-bold fade show mb-4" role="alert">
          <span class="alert-icon align-middle me-2"><i class="fas fa-info-circle"></i></span>
          <span class="alert-text"><?php echo htmlspecialchars($alertMsg); ?></span>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      <?php } ?>

      <!-- Edit User Form Card -->
      <div class="row justify-content-center">
        <div class="col-lg-10">
          <div class="card border-0 shadow-sm" style="border-radius: 1rem; background: #ffffff;">
            <div class="card-header bg-transparent pb-0 p-3">
              <h6 class="mb-0 font-weight-bolder text-dark text-uppercase"><i class="fas fa-user-pen me-2 text-success"></i> ACCOUNT DETAILS & PERMISSIONS</h6>
              <p class="text-xs text-muted mb-0 text-uppercase">MODIFY USER ACCOUNT SETTINGS BELOW</p>
            </div>
            <div class="card-body p-4 text-uppercase">
              <form method="POST" action="editUser.php">
                <input type="hidden" name="username" value="<?php echo htmlspecialchars($userData['username'] ?? ''); ?>">

                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label font-weight-bold text-xs text-muted">USERNAME (READ-ONLY):</label>
                    <input type="text" class="form-control border px-3 py-2 bg-light font-weight-bold" value="<?php echo htmlspecialchars($userData['username'] ?? ''); ?>" readonly style="border-radius: 0.5rem;">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label font-weight-bold text-xs text-muted">FULL NAME:</label>
                    <input type="text" name="name" class="form-control border px-3 py-2 font-weight-bold" value="<?php echo htmlspecialchars($userData['name'] ?? ''); ?>" required style="border-radius: 0.5rem;">
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label font-weight-bold text-xs text-muted">EMAIL ADDRESS:</label>
                    <input type="email" name="email" class="form-control border px-3 py-2 font-weight-bold" value="<?php echo htmlspecialchars($userData['email'] ?? ''); ?>" style="border-radius: 0.5rem;">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label font-weight-bold text-xs text-muted">SYSTEM ROLE:</label>
                    <select name="Role" class="form-select border px-3 py-2 font-weight-bold" style="border-radius: 0.5rem;">
                      <?php 
                      $currRole = strtoupper(trim((string)($userData['Role'] ?? 'NU')));
                      ?>
                      <option value="NU" <?php echo ($currRole === 'NU') ? 'selected' : ''; ?>>NORMAL USER (NU)</option>
                      <option value="FP" <?php echo ($currRole === 'FP') ? 'selected' : ''; ?>>FOCAL PERSON (FP)</option>
                      <option value="SE" <?php echo ($currRole === 'SE') ? 'selected' : ''; ?>>SUPPORT ENGINEER / SYSTEM EXECUTIVE (SE)</option>
                      <option value="SPV" <?php echo ($currRole === 'SPV') ? 'selected' : ''; ?>>SUPERVISOR (SPV)</option>
                    </select>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label font-weight-bold text-xs text-muted">ASSIGNED DIVISION:</label>
                    <select name="Division" class="form-select border px-3 py-2 font-weight-bold" style="border-radius: 0.5rem;">
                      <?php 
                      $currDiv = htmlspecialchars($userData['Division'] ?? 'Headquarters');
                      foreach ($divisionsList as $dOpt) { 
                      ?>
                        <option value="<?php echo $dOpt; ?>" <?php echo ($dOpt === $currDiv) ? 'selected' : ''; ?>>
                          <?php echo strtoupper($dOpt); ?>
                        </option>
                      <?php } ?>
                    </select>
                  </div>

                  <div class="col-md-3 mb-3">
                    <label class="form-label font-weight-bold text-xs text-muted">ACCOUNT ACTIVE STATUS:</label>
                    <select name="active" class="form-select border px-3 py-2 font-weight-bold" style="border-radius: 0.5rem;">
                      <option value="Y" <?php echo (strtoupper((string)($userData['active'] ?? 'Y')) === 'Y') ? 'selected' : ''; ?>>ACTIVE (Y)</option>
                      <option value="N" <?php echo (strtoupper((string)($userData['active'] ?? 'Y')) === 'N') ? 'selected' : ''; ?>>INACTIVE (N)</option>
                    </select>
                  </div>

                  <div class="col-md-3 mb-3">
                    <label class="form-label font-weight-bold text-xs text-muted">LDAP AUTHENTICATION:</label>
                    <select name="ldap" class="form-select border px-3 py-2 font-weight-bold" style="border-radius: 0.5rem;">
                      <option value="Y" <?php echo (strtoupper((string)($userData['ldap'] ?? 'N')) === 'Y') ? 'selected' : ''; ?>>ENABLED (Y)</option>
                      <option value="N" <?php echo (strtoupper((string)($userData['ldap'] ?? 'N')) === 'N') ? 'selected' : ''; ?>>DISABLED (N)</option>
                    </select>
                  </div>
                </div>

                <hr class="my-4">

                <div class="row">
                  <div class="col-12 mb-3">
                    <h6 class="font-weight-bolder text-dark text-uppercase"><i class="fas fa-key me-2 text-warning"></i> RESET PASSWORD (OPTIONAL)</h6>
                    <p class="text-xs text-muted mb-2">LEAVE BLANK IF YOU DO NOT WISH TO CHANGE THE USER'S CURRENT PASSWORD.</p>
                    <input type="password" name="password" class="form-control border px-3 py-2 font-weight-bold" placeholder="ENTER NEW PASSWORD TO RESET..." style="border-radius: 0.5rem;">
                  </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-3">
                  <a href="accManagement.php" class="btn btn-outline-secondary font-weight-bold text-uppercase">CANCEL</a>
                  <button type="submit" name="submit_edit_user" class="btn bg-gradient-success font-weight-bold text-uppercase px-4">SAVE CHANGES</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      <?php if (file_exists("footer.php")) { include("footer.php"); } ?>
    </div>
  </main>

  <!-- Core JS -->
  <script src="./assets/js/core/popper.min.js"></script>
  <script src="./assets/js/core/bootstrap.min.js"></script>
  <script src="./assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="./assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script src="./assets/js/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="./assets/js/material-dashboard.min.js?v=3.0.0"></script>

  <?php if (!empty($alertMsg)) { ?>
    <script>
      document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
          title: "<?php echo ($alertType === 'success') ? 'SUCCESS!' : 'ERROR!'; ?>",
          text: "<?php echo htmlspecialchars($alertMsg); ?>",
          icon: "<?php echo $alertType; ?>",
          confirmButtonColor: "#4caf50"
        }).then(function() {
          <?php if ($alertType === 'success') { ?>
            window.location = "accManagement.php";
          <?php } ?>
        });
      });
    </script>
  <?php } ?>
</body>

</html>
