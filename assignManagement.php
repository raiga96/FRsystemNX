<?php
session_start();
$page = 'assign_mgmt';

require 'includes/connect.php';
require 'includes/line.php';
require 'includes/login_function.php';
require 'includes/controller/role_controller.php';

// Form Submit Handler for New Assign
$alertMsg = '';
$alertType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_assign'])) {
    $assfrno = trim((string)($_POST['assfrno'] ?? ''));
    $assign_to = trim((string)($_POST['assign_to'] ?? ''));
    $remarks = trim((string)($_POST['remarks'] ?? 'Assigned for action'));
    $act_status = 'assign';

    if (!empty($assfrno) && !empty($assign_to)) {
        if (isset($conn) && $conn instanceof mysqli) {
            $frnoEsc = $conn->real_escape_string($assfrno);
            $toEsc = $conn->real_escape_string($assign_to);
            $remEsc = $conn->real_escape_string($remarks);
            $nowDate = date("Y-m-d H:i:s");

            // Insert new assignment record
            $insertSql = "INSERT INTO `assign` (Assfrno, assign_to, assign_date, act_status, remarks)
                          VALUES ('$frnoEsc', '$toEsc', '$nowDate', '$act_status', '$remEsc')";

            if ($conn->query($insertSql)) {
                $alertMsg = "FR NO. $frnoEsc SUCCESSFULLY ASSIGNED TO $assign_to!";
                $alertType = "success";
            } else {
                $alertMsg = "FAILED TO ASSIGN FR: " . $conn->error;
                $alertType = "danger";
            }
        }
    } else {
        $alertMsg = "PLEASE SELECT BOTH FAULT REPORT NO. AND ASSIGNED OFFICER.";
        $alertType = "warning";
    }
}

// Get logged in user details
$currUserFull = trim((string)($fullname ?? ''));
$currUserUid = trim((string)($_SESSION['uid'] ?? $_SESSION['user'] ?? $_SESSION['username'] ?? ''));

if ($currUserFull === 'User' || $currUserFull === 'Pengguna') {
    $currUserFull = '';
}

// Fetch Assign Records strictly assigned to current logged in officer
$assignList = [];
if (isset($conn) && $conn instanceof mysqli) {
    $usrFullEsc = $conn->real_escape_string($currUserFull);
    $usrUidEsc = $conn->real_escape_string($currUserUid);

    $userConditions = [];
    if (!empty($usrFullEsc)) {
        $userConditions[] = "a.assign_to = '$usrFullEsc'";
    }
    if (!empty($usrUidEsc)) {
        $userConditions[] = "a.assign_to = '$usrUidEsc'";
    }

    if (!empty($userConditions)) {
        $sqlAssign = "SELECT a.*, f.frcate, f.request_by, f.Oridiv, f.Section, f.date_add, f.Description
                      FROM `assign` a
                      JOIN `fr` f ON a.Assfrno = f.Frn 
                      WHERE (" . implode(" OR ", $userConditions) . ")
                      ORDER BY a.AssignId DESC LIMIT 200";
        $resAssign = $conn->query($sqlAssign);
        if ($resAssign) {
            $readArray = $_SESSION['read_frs'] ?? [];
            while ($rowAss = $resAssign->fetch_assoc()) {
                $rowAss['is_unread'] = !in_array($rowAss['Assfrno'], $readArray);
                $assignList[] = $rowAss;
            }
        }
    }
}

// Fetch Unassigned FRs for Modal Select
$unassignedFRs = [];
if (isset($conn) && $conn instanceof mysqli) {
    $sqlUnassigned = "SELECT Frn, frcate, request_by, Oridiv, Section, date_add, Description 
                      FROM `fr` 
                      WHERE Frn NOT IN (SELECT Assfrno FROM `assign`)
                      ORDER BY date_add DESC LIMIT 100";
    $resUnassigned = $conn->query($sqlUnassigned);
    if ($resUnassigned) {
        while ($rowU = $resUnassigned->fetch_assoc()) {
            $unassignedFRs[] = $rowU;
        }
    }
}

// Fetch Officers List from user table
$officersList = [];
if (isset($conn) && $conn instanceof mysqli) {
    $sqlUsers = "SELECT name, username, Role, Division FROM `user` WHERE active = 'Y' ORDER BY name ASC";
    $resUsers = $conn->query($sqlUsers);
    if ($resUsers) {
        while ($rowUsr = $resUsers->fetch_assoc()) {
            $officersList[] = $rowUsr;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="./assets/img/favicon.svg">
  <link rel="icon" type="image/png" href="./assets/img/favicon.svg">
  <title>NEW ASSIGN MANAGEMENT - FRSYSTEM</title>

  <!-- Fonts and icons -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" />
  <link href="./assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="./assets/css/nucleo-svg.css" rel="stylesheet" />
  <link rel="stylesheet" href="./assets/fontawesome/css/all.css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
  <link id="pagestyle" href="./assets/css/material-dashboard.css?v=3.0.0" rel="stylesheet" />
  <link rel="stylesheet" href="./assets/datatables/dataTables.dataTables.css">
</head>

<body class="g-sidenav-show bg-gray-200">
  <?php include("sidebar.php"); ?>

  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <?php include("navbar.php"); ?>

    <div class="container-fluid py-4">
      <!-- Header Banner -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card bg-gradient-dark border-0 shadow-lg position-relative overflow-hidden p-3" style="border-radius: 1rem; background: linear-gradient(135deg, #4e54c8 0%, #8f94fb 100%);">
            <div class="row align-items-center">
              <div class="col-lg-8 col-md-7">
                <span class="badge bg-white text-dark mb-2 text-uppercase tracking-wider px-3 py-2 font-weight-bolder"><i class="fas fa-user-plus me-1"></i> New Assignment Module</span>
                <h2 class="text-white font-weight-bolder mb-1">FAULT REPORT NEW ASSIGN</h2>
                <p class="text-white opacity-9 text-sm mb-0 text-uppercase">ASSIGN UNASSIGNED FAULT REPORTS TO ACTION OFFICERS AND SYSTEM EXECUTIVES.</p>
              </div>
              <div class="col-lg-4 col-md-5 text-end">
                <button type="button" class="btn btn-light text-dark font-weight-bold text-uppercase mb-0 shadow-sm" data-bs-toggle="modal" data-bs-target="#addAssignModal">
                  <i class="fas fa-plus me-1"></i> NEW ASSIGNMENT
                </button>
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

      <!-- Assign Records Data Table -->
      <div class="row">
        <div class="col-12">
          <div class="card border-0 shadow-sm" style="border-radius: 1rem; background: #ffffff;">
            <div class="card-header bg-transparent pb-0 p-3">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="mb-0 font-weight-bolder text-dark text-uppercase"><i class="fas fa-user-check me-2 text-primary"></i> ASSIGNED FAULT REPORTS LIST</h6>
                  <p class="text-xs text-muted mb-0 text-uppercase">RECORDS OF FAULT REPORTS ASSIGNED TO OFFICERS</p>
                </div>
                <span class="badge bg-light text-dark border font-weight-bold px-3 py-2 text-uppercase">TOTAL ASSIGNED: <?php echo count($assignList); ?></span>
              </div>
            </div>
            <div class="card-body p-3">
              <div class="table-responsive">
                <table class="table table-hover align-items-center mb-0 text-uppercase" id="assignTable">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">FR NO.</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ASSIGNED TO</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">REPORTER & DIVISION</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">CATEGORY</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">ASSIGNED DATE</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">ACTION</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($assignList as $ass) { 
                      $frno = htmlspecialchars((string)($ass['Assfrno'] ?? ''));
                      $toName = htmlspecialchars((string)($ass['assign_to'] ?? '-'));
                      $reporter = htmlspecialchars((string)($ass['request_by'] ?? 'N/A'));
                      $div = htmlspecialchars((string)($ass['Oridiv'] ?? 'N/A'));
                      $cate = htmlspecialchars((string)($ass['frcate'] ?? 'General'));
                      $dateAss = htmlspecialchars((string)($ass['assign_date'] ?? '-'));
                    ?>
                      <tr class="<?php echo !empty($ass['is_unread']) ? 'table-warning' : ''; ?>">
                        <td>
                          <a href="frDetail.php?frn=<?php echo $frno; ?>" class="badge bg-gradient-dark font-weight-bold text-xs">
                            <?php echo $frno; ?>
                          </a>
                          <?php if (!empty($ass['is_unread'])) { ?>
                            <span class="badge bg-gradient-danger text-xxs ms-1 animate__pulse">NEW</span>
                          <?php } ?>
                        </td>
                        <td>
                          <span class="text-xs font-weight-bold text-dark me-1"><i class="fas fa-user-circle me-1 text-primary"></i><?php echo $toName; ?></span>
                        </td>
                        <td>
                          <span class="text-xs font-weight-bold text-dark d-block"><?php echo $reporter; ?></span>
                          <span class="text-xxs text-muted font-weight-bold"><?php echo $div; ?></span>
                        </td>
                        <td>
                          <span class="badge bg-light text-primary border text-xxs font-weight-bold"><?php echo $cate; ?></span>
                        </td>
                        <td class="text-center">
                          <span class="text-xxs text-muted font-weight-bold"><?php echo $dateAss; ?></span>
                        </td>
                        <td class="text-center">
                          <a href="frDetail.php?frn=<?php echo $frno; ?>" class="btn btn-xs btn-outline-info mb-0 text-uppercase">
                            <i class="fas fa-eye"></i> VIEW DETAILS
                          </a>
                        </td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <?php if (file_exists("footer.php")) { include("footer.php"); } ?>
    </div>
  </main>

  <!-- Add New Assign Modal -->
  <div class="modal fade" id="addAssignModal" tabindex="-1" aria-labelledby="addAssignModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content border-0 shadow-lg" style="border-radius: 1rem;">
        <div class="modal-header bg-gradient-dark text-white border-0" style="border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
          <h5 class="modal-title text-white font-weight-bolder text-uppercase" id="addAssignModalLabel"><i class="fas fa-user-plus me-2"></i> ASSIGN FAULT REPORT TO OFFICER</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="assignManagement.php">
          <div class="modal-body p-4 text-uppercase">
            <div class="mb-3">
              <label class="form-label font-weight-bold text-xs text-muted">SELECT UNASSIGNED FAULT REPORT (FR NO.):</label>
              <select name="assfrno" class="form-select border px-3 py-2 font-weight-bold" style="border-radius: 0.5rem;" required>
                <option value="">-- CHOOSE UNASSIGNED FR --</option>
                <?php foreach ($unassignedFRs as $uFr) { ?>
                  <option value="<?php echo htmlspecialchars($uFr['Frn']); ?>">
                    <?php echo htmlspecialchars($uFr['Frn']); ?> - <?php echo htmlspecialchars($uFr['request_by']); ?> (<?php echo htmlspecialchars($uFr['Oridiv']); ?> &bull; <?php echo htmlspecialchars($uFr['frcate']); ?>)
                  </option>
                <?php } ?>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label font-weight-bold text-xs text-muted">ASSIGN TO OFFICER / SYSTEM EXECUTIVE:</label>
              <select name="assign_to" class="form-select border px-3 py-2 font-weight-bold" style="border-radius: 0.5rem;" required>
                <option value="">-- SELECT OFFICER --</option>
                <?php foreach ($officersList as $off) { 
                  $displayName = !empty($off['name']) ? $off['name'] : $off['username'];
                ?>
                  <option value="<?php echo htmlspecialchars($displayName); ?>">
                    <?php echo htmlspecialchars($displayName); ?> (<?php echo htmlspecialchars($off['Role']); ?> &bull; <?php echo htmlspecialchars($off['Division']); ?>)
                  </option>
                <?php } ?>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label font-weight-bold text-xs text-muted">ASSIGNMENT REMARKS / INSTRUCTIONS:</label>
              <textarea name="remarks" class="form-control border px-3 py-2" rows="3" placeholder="ENTER INSTRUCTIONS FOR ASSIGNED OFFICER..." style="border-radius: 0.5rem;">Assigned for action</textarea>
            </div>
          </div>
          <div class="modal-footer border-0 pt-0">
            <button type="button" class="btn btn-outline-secondary font-weight-bold text-uppercase" data-bs-dismiss="modal">CANCEL</button>
            <button type="submit" name="submit_assign" class="btn bg-gradient-primary font-weight-bold text-uppercase px-4">SUBMIT ASSIGNMENT</button>
          </div>
        </form>
      </div>
    </div>
  </div>

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
      $('#assignTable').DataTable({
        "pageLength": 10,
        "language": {
          "search": "SEARCH ASSIGNMENT:",
          "lengthMenu": "SHOW _MENU_ RECORDS",
          "info": "SHOWING _START_ TO _END_ OF _TOTAL_ ASSIGNMENTS"
        }
      });
    });
  </script>
</body>

</html>
