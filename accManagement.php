<!--
=========================================================
* Material Dashboard 2 - User Management Module
=========================================================
-->
<?php
session_start();
$page = 'sys_acc';

require 'includes/connect.php';
require 'includes/line.php';
require 'includes/login_function.php';
// Controller for Role and permission
require 'includes/controller/role_controller.php';

// Access Control: Only Headquarters users can access User Management
if (($divName ?? '') !== 'Headquarters') {
    header("Location: index.php?error=unauthorized");
    exit();
}

// List of divisions available in system
$divisionsList = ['Headquarters', 'Kuching', 'Samarahan', 'Sri Aman', 'Betong', 'Sarikei', 'Sibu', 'Mukah', 'Bintulu', 'Miri', 'Limbang', 'Kapit'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="./assets/img/favicon.svg">
  <link rel="icon" type="image/png" href="./assets/img/favicon.svg">
  <title>User Management - FRSystem</title>
  
  <!-- Fonts and icons -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" />
  <link href="./assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="./assets/css/nucleo-svg.css" rel="stylesheet" />
  <link rel="stylesheet" href="./assets/fontawesome/css/all.css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
  <link id="pagestyle" href="./assets/css/material-dashboard.css?v=3.0.0" rel="stylesheet" />
  <link rel="stylesheet" href="./assets/datatables/dataTables.dataTables.css">

  <style>
    .form-switch .form-check-input {
      width: 2.5em;
      height: 1.25em;
      cursor: pointer;
    }
    .badge-ldap-active {
      background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
      color: #fff;
    }
    .badge-ldap-local {
      background: linear-gradient(135deg, #FF416C 0%, #FF4B2B 100%);
      color: #fff;
    }
  </style>
</head>

<body class="g-sidenav-show bg-gray-200">
  <?php include("sidebar.php"); ?>
  
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <?php include("navbar.php"); ?>
    
    <div class="container-fluid py-4">
      <?php
      // Statistics for User Management (Filtered by Division for non-HQ)
      $totalUsers = 0;
      $ldapUsers = 0;
      $localUsers = 0;
      $activeUsers = 0;
      $inactiveUsers = 0;

      $cardDivWhere = "";
      $currUserDiv = $divName ?? '';
      $isHQUser = (strtoupper(trim((string)$currUserDiv)) === 'HEADQUARTERS');
      
      if (!$isHQUser && !empty($currUserDiv)) {
          $divSafeStat = $conn->real_escape_string($currUserDiv);
          $cardDivWhere = " WHERE (Division = '$divSafeStat' OR Division LIKE '%$divSafeStat%') ";
      }

      if (isset($conn) && $conn instanceof mysqli) {
          $resT = $conn->query("SELECT COUNT(*) as t FROM `user` " . $cardDivWhere);
          if ($resT && $rT = $resT->fetch_assoc()) $totalUsers = (int)$rT['t'];

          $ldapCond = $cardDivWhere ? $cardDivWhere . " AND UPPER(ldap) = 'Y' " : " WHERE UPPER(ldap) = 'Y' ";
          $resL = $conn->query("SELECT COUNT(*) as t FROM `user` " . $ldapCond);
          if ($resL && $rL = $resL->fetch_assoc()) $ldapUsers = (int)$rL['t'];

          $localUsers = max(0, $totalUsers - $ldapUsers);

          $activeCond = $cardDivWhere ? $cardDivWhere . " AND UPPER(active) = 'Y' " : " WHERE UPPER(active) = 'Y' ";
          $resA = $conn->query("SELECT COUNT(*) as t FROM `user` " . $activeCond);
          if ($resA && $rA = $resA->fetch_assoc()) $activeUsers = (int)$rA['t'];

          $inactiveUsers = max(0, $totalUsers - $activeUsers);
      }
      ?>

      <!-- Header Cards -->
      <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
          <div class="card border-0 shadow-sm" style="border-radius: 1rem;">
            <div class="card-body p-3">
              <div class="d-flex align-items-center">
                <div class="icon icon-shape bg-gradient-dark shadow-dark border-radius-xl p-3 me-3 text-center d-flex align-items-center justify-content-center" style="width:48px; height:48px;">
                  <i class="fas fa-users text-white"></i>
                </div>
                <div>
                  <p class="text-xs text-uppercase font-weight-bold text-muted mb-0">TOTAL ACCOUNTS</p>
                  <h4 class="font-weight-bolder mb-0"><?php echo number_format($totalUsers); ?></h4>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xl-3 col-md-6">
          <div class="card border-0 shadow-sm" style="border-radius: 1rem;">
            <div class="card-body p-3">
              <div class="d-flex align-items-center">
                <div class="icon icon-shape bg-gradient-success shadow-success border-radius-xl p-3 me-3 text-center d-flex align-items-center justify-content-center" style="width:48px; height:48px;">
                  <i class="fas fa-user-check text-white"></i>
                </div>
                <div>
                  <p class="text-xs text-uppercase font-weight-bold text-muted mb-0">ACTIVE USERS</p>
                  <h4 class="font-weight-bolder mb-0 text-success" id="card-active-count"><?php echo number_format($activeUsers); ?></h4>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xl-3 col-md-6">
          <div class="card border-0 shadow-sm" style="border-radius: 1rem;">
            <div class="card-body p-3">
              <div class="d-flex align-items-center">
                <div class="icon icon-shape bg-gradient-danger shadow-danger border-radius-xl p-3 me-3 text-center d-flex align-items-center justify-content-center" style="width:48px; height:48px;">
                  <i class="fas fa-user-slash text-white"></i>
                </div>
                <div>
                  <p class="text-xs text-uppercase font-weight-bold text-muted mb-0">INACTIVE USERS</p>
                  <h4 class="font-weight-bolder mb-0 text-danger" id="card-inactive-count"><?php echo number_format($inactiveUsers); ?></h4>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xl-3 col-md-6">
          <div class="card border-0 shadow-sm" style="border-radius: 1rem;">
            <div class="card-body p-3">
              <div class="d-flex align-items-center">
                <div class="icon icon-shape bg-gradient-info shadow-info border-radius-xl p-3 me-3 text-center d-flex align-items-center justify-content-center" style="width:48px; height:48px;">
                  <i class="fas fa-network-wired text-white"></i>
                </div>
                <div>
                  <p class="text-xs text-uppercase font-weight-bold text-muted mb-0">SARAWAKNET LDAP</p>
                  <h4 class="font-weight-bolder mb-0 text-info"><?php echo number_format($ldapUsers); ?></h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Main User Management Table Card -->
      <div class="row">
        <div class="col-12">
          <div class="card border-0 shadow-sm" style="border-radius: 1rem;">
            <div class="card-header bg-gradient-dark p-3" style="border-radius: 1rem 1rem 0 0;">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h5 class="text-white mb-0 font-weight-bolder text-uppercase"><i class="fas fa-user-cog me-2"></i> USER MANAGEMENT</h5>
                  <p class="text-white text-xs opacity-8 mb-0 text-uppercase">UPDATE ACTIVE STATUS, LDAP LOGIN MODE & CHANGE USER DIVISION</p>
                </div>
                <a href="addUser.php" class="btn btn-sm btn-info mb-0 text-uppercase"><i class="fas fa-user-plus me-1"></i> ADD USER</a>
              </div>
            </div>

            <div class="card-body p-4">
              <div class="table-responsive">
                <table class="table table-hover align-items-center mb-0 text-uppercase" id="userManagementTable">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">USER / EMAIL</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">ROLE</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">DIVISION</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">ACTIVE STATUS</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">LDAP MODE</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">CHANGE DIVISION</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">ACTION</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    if (isset($conn) && $conn instanceof mysqli) {
                        $userDivFilter = "";
                        $currUserDiv = $divName ?? '';
                        $isHQUser = (strtoupper(trim((string)$currUserDiv)) === 'HEADQUARTERS');
                        
                        if (!$isHQUser && !empty($currUserDiv)) {
                            $divSafeUser = $conn->real_escape_string($currUserDiv);
                            $userDivFilter = " WHERE (Division = '$divSafeUser' OR Division LIKE '%$divSafeUser%') ";
                        }

                        $resUsers = $conn->query("SELECT * FROM `user` " . $userDivFilter . " ORDER BY username ASC");
                        if ($resUsers && $resUsers->num_rows > 0) {
                            while ($uRow = $resUsers->fetch_assoc()) {
                                $uName = htmlspecialchars($uRow['username']);
                                $fullName = htmlspecialchars($uRow['name'] ?? $uName);
                                $email = htmlspecialchars($uRow['email'] ?? '-');
                                $roleCode = strtoupper(trim((string)($uRow['Role'] ?? 'NU')));
                                $roleLabel = 'NORMAL';
                                if ($roleCode === 'SPV') {
                                    $roleLabel = 'SUPERVISOR';
                                } elseif ($roleCode === 'SE') {
                                    $roleLabel = 'SUPPORT ENGINEER';
                                } elseif ($roleCode === 'FP') {
                                    $roleLabel = 'FOCAL PERSON';
                                } elseif ($roleCode === 'NU') {
                                    $roleLabel = 'NORMAL';
                                } else {
                                    $roleLabel = $roleCode !== '' ? $roleCode : 'NORMAL';
                                }
                                $currDiv = htmlspecialchars($uRow['Division'] ?? 'Headquarters');
                                $isLdap = strtoupper((string)($uRow['ldap'] ?? 'N')) === 'Y';
                                $isActive = strtoupper((string)($uRow['active'] ?? 'Y')) === 'Y';
                    ?>
                                <tr>
                                  <td>
                                    <div class="d-flex px-2 py-1 align-items-center">
                                      <div class="icon icon-shape icon-xs me-3 bg-gradient-secondary text-white border-radius-md d-flex align-items-center justify-content-center">
                                        <i class="fas fa-user"></i>
                                      </div>
                                      <div class="d-flex flex-column justify-content-center">
                                        <h6 class="mb-0 text-sm font-weight-bold text-uppercase"><?php echo $fullName; ?></h6>
                                        <span class="text-xxs text-muted text-uppercase"><?php echo $uName; ?> &bull; <?php echo $email; ?></span>
                                      </div>
                                    </div>
                                  </td>
                                  <td class="align-middle text-center">
                                    <span class="badge bg-light text-dark border text-xxs font-weight-bolder text-uppercase"><?php echo htmlspecialchars($roleLabel); ?></span>
                                  </td>
                                  <td class="align-middle text-center">
                                    <span class="badge bg-gradient-info text-xxs font-weight-bold text-uppercase" id="div-badge-<?php echo $uName; ?>"><?php echo $currDiv; ?></span>
                                  </td>
                                  <td class="align-middle text-center">
                                    <div class="d-flex justify-content-center align-items-center mb-0">
                                      <span class="text-xxs font-weight-bolder <?php echo !$isActive ? 'text-danger' : 'text-muted'; ?> me-2" id="active-off-<?php echo $uName; ?>">OFF</span>
                                      <div class="form-check form-switch mb-0 ps-0">
                                        <input class="form-check-input toggle-active-btn ms-0" type="checkbox" data-username="<?php echo $uName; ?>" <?php echo $isActive ? 'checked' : ''; ?>>
                                      </div>
                                      <span class="text-xxs font-weight-bolder <?php echo $isActive ? 'text-success' : 'text-muted'; ?> ms-2" id="active-on-<?php echo $uName; ?>">ON</span>
                                    </div>
                                  </td>
                                  <td class="align-middle text-center">
                                    <div class="d-flex justify-content-center align-items-center mb-0">
                                      <span class="text-xxs font-weight-bolder <?php echo !$isLdap ? 'text-danger' : 'text-muted'; ?> me-2" id="ldap-off-<?php echo $uName; ?>">OFF</span>
                                      <div class="form-check form-switch mb-0 ps-0">
                                        <input class="form-check-input toggle-ldap-btn ms-0" type="checkbox" data-username="<?php echo $uName; ?>" <?php echo $isLdap ? 'checked' : ''; ?>>
                                      </div>
                                      <span class="text-xxs font-weight-bolder <?php echo $isLdap ? 'text-success' : 'text-muted'; ?> ms-2" id="ldap-on-<?php echo $uName; ?>">ON</span>
                                    </div>
                                  </td>
                                  <td class="align-middle text-center">
                                    <select class="form-select form-select-sm select-division-btn border px-2 py-1 text-uppercase" style="border-radius: 0.5rem; font-size: 0.8rem;" data-username="<?php echo $uName; ?>">
                                      <?php foreach ($divisionsList as $dOpt) { ?>
                                        <option value="<?php echo $dOpt; ?>" <?php echo ($dOpt === $currDiv) ? 'selected' : ''; ?>>
                                          <?php echo strtoupper($dOpt); ?>
                                        </option>
                                      <?php } ?>
                                    </select>
                                  </td>
                                  <td class="align-middle text-center">
                                    <a href="editUser.php?usr=<?php echo urlencode($uRow['username']); ?>" class="btn btn-xs btn-outline-primary mb-0 text-uppercase">
                                      <i class="fas fa-edit me-1"></i> EDIT
                                    </a>
                                  </td>
                                </tr>
                    <?php
                            }
                        }
                    }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <?php
      if (file_exists("footer.php")) {
          include("footer.php");
      }
      ?>
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
  <script src="./assets/datatables/dataTables.js"></script>

  <script>
    $(document).ready(function() {
      $('#userManagementTable').DataTable({
        "pageLength": 10,
        "language": {
          "search": "SEARCH USER:",
          "lengthMenu": "SHOW _MENU_ RECORDS",
          "info": "SHOWING _START_ TO _END_ OF _TOTAL_ USERS"
        }
      });

      // Toggle Active Switch Event
      $(document).on('change', '.toggle-active-btn', function() {
        var username = $(this).data('username');
        var isChecked = $(this).is(':checked');
        var activeVal = isChecked ? 'Y' : 'N';

        $.ajax({
          url: 'includes/user_management_action.php',
          type: 'POST',
          dataType: 'json',
          data: {
            action: 'toggle_active',
            username: username,
            active: activeVal
          },
          success: function(res) {
            if (res.status === 'success') {
              var labelOff = $('#active-off-' + username);
              var labelOn = $('#active-on-' + username);
              var activeCard = $('#card-active-count');
              var inactiveCard = $('#card-inactive-count');
              var currentActive = parseInt(activeCard.text().replace(/,/g, '')) || 0;
              var currentInactive = parseInt(inactiveCard.text().replace(/,/g, '')) || 0;

              if (isChecked) {
                labelOff.removeClass('text-danger').addClass('text-muted');
                labelOn.removeClass('text-muted').addClass('text-success');
                activeCard.text((currentActive + 1).toLocaleString());
                inactiveCard.text(Math.max(0, currentInactive - 1).toLocaleString());
              } else {
                labelOff.removeClass('text-muted').addClass('text-danger');
                labelOn.removeClass('text-success').addClass('text-muted');
                activeCard.text(Math.max(0, currentActive - 1).toLocaleString());
                inactiveCard.text((currentInactive + 1).toLocaleString());
              }
              Swal.fire({
                title: 'SUCCESS!',
                text: res.message,
                icon: 'success',
                confirmButtonColor: '#e91e63'
              });
            } else {
              Swal.fire('ERROR', res.message, 'error');
            }
          },
          error: function() {
            Swal.fire('ERROR', 'FAILED TO CONNECT TO SERVER.', 'error');
          }
        });
      });

      // Toggle LDAP Switch Event
      $(document).on('change', '.toggle-ldap-btn', function() {
        var username = $(this).data('username');
        var isChecked = $(this).is(':checked');
        var ldapVal = isChecked ? 'Y' : 'N';
        var badge = $('#ldap-status-' + username);

        $.ajax({
          url: 'includes/user_management_action.php',
          type: 'POST',
          dataType: 'json',
          data: {
            action: 'toggle_ldap',
            username: username,
            ldap: ldapVal
          },
          success: function(res) {
            if (res.status === 'success') {
              var labelOff = $('#ldap-off-' + username);
              var labelOn = $('#ldap-on-' + username);
              if (isChecked) {
                labelOff.removeClass('text-danger').addClass('text-muted');
                labelOn.removeClass('text-muted').addClass('text-success');
              } else {
                labelOff.removeClass('text-muted').addClass('text-danger');
                labelOn.removeClass('text-success').addClass('text-muted');
              }
              Swal.fire({
                title: 'SUCCESS!',
                text: res.message,
                icon: 'success',
                confirmButtonColor: '#e91e63'
              });
            } else {
              Swal.fire('ERROR', res.message, 'error');
            }
          },
          error: function() {
            Swal.fire('ERROR', 'FAILED TO CONNECT TO SERVER.', 'error');
          }
        });
      });

      // Select Division Change Event
      $(document).on('change', '.select-division-btn', function() {
        var username = $(this).data('username');
        var newDiv = $(this).val();
        var badgeDiv = $('#div-badge-' + username);

        $.ajax({
          url: 'includes/user_management_action.php',
          type: 'POST',
          dataType: 'json',
          data: {
            action: 'update_division',
            username: username,
            division: newDiv
          },
          success: function(res) {
            if (res.status === 'success') {
              badgeDiv.text(newDiv);
              Swal.fire({
                title: 'SUCCESS!',
                text: res.message,
                icon: 'success',
                confirmButtonColor: '#e91e63'
              });
            } else {
              Swal.fire('ERROR', res.message, 'error');
            }
          },
          error: function() {
            Swal.fire('ERROR', 'FAILED TO UPDATE DIVISION.', 'error');
          }
        });
      });
    });
  </script>
</body>

</html>