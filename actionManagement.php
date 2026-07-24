<?php
session_start();
$page = 'action_mgmt';

require 'includes/connect.php';
require 'includes/line.php';
require 'includes/login_function.php';
require 'includes/controller/role_controller.php';

// Form Submit Handler for Action Updates
$alertMsg = '';
$alertType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_action'])) {
    $frno = trim((string)($_POST['frno'] ?? ''));
    $action_taken = trim((string)($_POST['action_taken'] ?? ''));
    $causeprob = trim((string)($_POST['causeprob'] ?? 'User Error'));
    $action_status = trim((string)($_POST['action_status'] ?? 'Done'));
    $FR_status = trim((string)($_POST['FR_status'] ?? 'Close'));
    $Note2User = trim((string)($_POST['Note2User'] ?? ''));
    $actionTakenBy = $fullname ?? $_SESSION['name'] ?? $_SESSION['username'] ?? 'Officer';

    if (!empty($frno) && !empty($action_taken)) {
        if (isset($conn) && $conn instanceof mysqli) {
            $frnoEsc = $conn->real_escape_string($frno);
            $actionEsc = $conn->real_escape_string($action_taken);
            $causeEsc = $conn->real_escape_string($causeprob);
            $actStatEsc = $conn->real_escape_string($action_status);
            $frStatEsc = $conn->real_escape_string($FR_status);
            $noteEsc = $conn->real_escape_string($Note2User);
            $byEsc = $conn->real_escape_string($actionTakenBy);
            $today = date("Y-m-d");
            $nowTime = date("H:i:s");

            // Check if action record already exists for this FR
            $checkSql = "SELECT ActionId FROM `action` WHERE frno = '$frnoEsc' LIMIT 1";
            $resCheck = $conn->query($checkSql);

            if ($resCheck && $resCheck->num_rows > 0) {
                $updateSql = "UPDATE `action` SET 
                                action_taken = '$actionEsc',
                                causeprob = '$causeEsc',
                                action_status = '$actStatEsc',
                                FR_status = '$frStatEsc',
                                Note2User = '$noteEsc',
                                ActionTakenBy = '$byEsc',
                                ActionEnd = '$today',
                                DateSendToUser = '$today',
                                TimeSendToUser = '$nowTime'
                              WHERE frno = '$frnoEsc'";
                if ($conn->query($updateSql)) {
                    $alertMsg = "ACTION RECORD FOR FR NO. $frno HAS BEEN UPDATED SUCCESSFULLY!";
                    $alertType = "success";
                } else {
                    $alertMsg = "FAILED TO UPDATE ACTION RECORD: " . $conn->error;
                    $alertType = "danger";
                }
            } else {
                $insertSql = "INSERT INTO `action` (frno, DateReceived, TimeReceived, ActionStart, ActionEnd, action_taken, ActionTakenBy, causeprob, action_status, FR_status, Note2User, DateSendToUser, TimeSendToUser)
                              VALUES ('$frnoEsc', '$today', '$nowTime', '$today', '$today', '$actionEsc', '$byEsc', '$causeEsc', '$actStatEsc', '$frStatEsc', '$noteEsc', '$today', '$nowTime')";
                if ($conn->query($insertSql)) {
                    $alertMsg = "NEW ACTION RECORD FOR FR NO. $frno HAS BEEN CREATED SUCCESSFULLY!";
                    $alertType = "success";
                } else {
                    $alertMsg = "FAILED TO CREATE ACTION RECORD: " . $conn->error;
                    $alertType = "danger";
                }
            }
        }
    } else {
        $alertMsg = "PLEASE FILL IN ALL REQUIRED FIELDS (FR NUMBER & ACTION TAKEN).";
        $alertType = "warning";
    }
}

// Fetch Action Records for DataTable
$actionList = [];
if (isset($conn) && $conn instanceof mysqli) {
    $sqlActions = "SELECT a.*, f.frcate, f.request_by, f.Oridiv, f.Section, f.date_add
                   FROM `action` a
                   JOIN `fr` f ON a.frno = f.Frn
                   ORDER BY a.ActionId DESC LIMIT 200";
    $resActions = $conn->query($sqlActions);
    if ($resActions) {
        while ($rowAct = $resActions->fetch_assoc()) {
            $actionList[] = $rowAct;
        }
    }
}

// Fetch Unacted Assigned FRs for Modal Quick Select
$unactedFRs = [];
if (isset($conn) && $conn instanceof mysqli) {
    $sqlUnacted = "SELECT f.Frn, f.frcate, f.request_by, f.Oridiv, f.Description 
                   FROM `fr` f 
                   JOIN `assign` ass ON ass.Assfrno = f.Frn 
                   WHERE f.Frn NOT IN (SELECT frno FROM `action` WHERE action_status = 'Done' OR FR_status = 'Close')
                   ORDER BY f.date_add DESC LIMIT 100";
    $resUnacted = $conn->query($sqlUnacted);
    if ($resUnacted) {
        while ($rowU = $resUnacted->fetch_assoc()) {
            $unactedFRs[] = $rowU;
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
  <title>ACTION MANAGEMENT - FRSYSTEM</title>

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
          <div class="card bg-gradient-dark border-0 shadow-lg position-relative overflow-hidden p-3" style="border-radius: 1rem; background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
            <div class="row align-items-center">
              <div class="col-lg-8 col-md-7">
                <span class="badge bg-white text-dark mb-2 text-uppercase tracking-wider px-3 py-2 font-weight-bolder"><i class="fas fa-tasks me-1"></i> Action Taken Module</span>
                <h2 class="text-white font-weight-bolder mb-1">FAULT REPORT ACTION MANAGEMENT</h2>
                <p class="text-white opacity-9 text-sm mb-0 text-uppercase">RECORD WORK DONE, UPDATE CAUSE OF PROBLEM, AND CLOSE FAULT REPORTS.</p>
              </div>
              <div class="col-lg-4 col-md-5 text-end">
                <button type="button" class="btn btn-light text-dark font-weight-bold text-uppercase mb-0 shadow-sm" data-bs-toggle="modal" data-bs-target="#addActionModal">
                  <i class="fas fa-plus me-1"></i> NEW ACTION RECORD
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

      <!-- Action Records Data Table -->
      <div class="row">
        <div class="col-12">
          <div class="card border-0 shadow-sm" style="border-radius: 1rem; background: #ffffff;">
            <div class="card-header bg-transparent pb-0 p-3">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="mb-0 font-weight-bolder text-dark text-uppercase"><i class="fas fa-list-check me-2 text-success"></i> FAULT REPORT ACTION RECORDS</h6>
                  <p class="text-xs text-muted mb-0 text-uppercase">TOTAL RECENT ACTION ENTRIES RECORDED IN DATABASE</p>
                </div>
                <span class="badge bg-light text-dark border font-weight-bold px-3 py-2 text-uppercase">RECORDS: <?php echo count($actionList); ?></span>
              </div>
            </div>
            <div class="card-body p-3">
              <div class="table-responsive">
                <table class="table table-hover align-items-center mb-0 text-uppercase" id="actionTable">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">FR NO.</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ACTION TAKEN BY</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">CAUSE OF PROBLEM</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ACTION DETAILS</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">ACTION STATUS</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">FR STATUS</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">ACTION</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($actionList as $act) { 
                      $frno = htmlspecialchars((string)($act['frno'] ?? ''));
                      $by = htmlspecialchars((string)($act['ActionTakenBy'] ?? 'Officer'));
                      $cause = htmlspecialchars((string)($act['causeprob'] ?? 'Unspecified'));
                      $desc = htmlspecialchars((string)($act['action_taken'] ?? '-'));
                      $actStat = htmlspecialchars((string)($act['action_status'] ?? 'Pending'));
                      $frStat = htmlspecialchars((string)($act['FR_status'] ?? 'Open'));
                    ?>
                      <tr>
                        <td>
                          <a href="frDetail.php?frn=<?php echo $frno; ?>" class="badge bg-gradient-dark font-weight-bold text-xs">
                            <?php echo $frno; ?>
                          </a>
                        </td>
                        <td>
                          <span class="text-xs font-weight-bold text-dark me-1"><i class="fas fa-user-gear me-1 text-info"></i><?php echo $by; ?></span>
                        </td>
                        <td>
                          <span class="badge bg-light text-dark border text-xxs font-weight-bold"><?php echo $cause; ?></span>
                        </td>
                        <td>
                          <span class="text-xs font-weight-normal text-wrap" style="max-width: 250px; display: inline-block;">
                            <?php echo (strlen($desc) > 80) ? substr($desc, 0, 80) . '...' : $desc; ?>
                          </span>
                        </td>
                        <td class="text-center">
                          <span class="badge bg-gradient-<?php echo ($actStat === 'Done') ? 'success' : 'warning'; ?> text-xxs font-weight-bold">
                            <?php echo $actStat; ?>
                          </span>
                        </td>
                        <td class="text-center">
                          <span class="badge bg-gradient-<?php echo ($frStat === 'Close') ? 'dark' : 'info'; ?> text-xxs font-weight-bold">
                            <?php echo $frStat; ?>
                          </span>
                        </td>
                        <td class="text-center">
                          <a href="frDetail.php?frn=<?php echo $frno; ?>" class="btn btn-xs btn-outline-info mb-0 text-uppercase me-1">
                            <i class="fas fa-eye"></i> VIEW
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

  <!-- Add Action Record Modal -->
  <div class="modal fade" id="addActionModal" tabindex="-1" aria-labelledby="addActionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content border-0 shadow-lg" style="border-radius: 1rem;">
        <div class="modal-header bg-gradient-dark text-white border-0" style="border-top-left-radius: 1rem; border-top-right-radius: 1rem;">
          <h5 class="modal-title text-white font-weight-bolder text-uppercase" id="addActionModalLabel"><i class="fas fa-pen-to-square me-2"></i> RECORD FAULT REPORT ACTION</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="POST" action="actionManagement.php">
          <div class="modal-body p-4 text-uppercase">
            <div class="mb-3">
              <label class="form-label font-weight-bold text-xs text-muted">SELECT FAULT REPORT (FR NO.):</label>
              <select name="frno" class="form-select border px-3 py-2 font-weight-bold" style="border-radius: 0.5rem;" required>
                <option value="">-- CHOOSE PENDING ASSIGNED FR --</option>
                <?php foreach ($unactedFRs as $uFr) { ?>
                  <option value="<?php echo htmlspecialchars($uFr['Frn']); ?>">
                    <?php echo htmlspecialchars($uFr['Frn']); ?> - <?php echo htmlspecialchars($uFr['request_by']); ?> (<?php echo htmlspecialchars($uFr['frcate']); ?>)
                  </option>
                <?php } ?>
              </select>
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label font-weight-bold text-xs text-muted">CAUSE OF PROBLEM:</label>
                <select name="causeprob" class="form-select border px-3 py-2 font-weight-bold" style="border-radius: 0.5rem;">
                  <option value="User Error">USER ERROR</option>
                  <option value="Data Error">DATA ERROR</option>
                  <option value="Faulty Hardware">FAULTY HARDWARE</option>
                  <option value="Program Limitation">PROGRAM LIMITATION</option>
                  <option value="Network Issue">NETWORK ISSUE</option>
                  <option value="Unknown">UNKNOWN</option>
                </select>
              </div>
              <div class="col-md-3 mb-3">
                <label class="form-label font-weight-bold text-xs text-muted">ACTION STATUS:</label>
                <select name="action_status" class="form-select border px-3 py-2 font-weight-bold" style="border-radius: 0.5rem;">
                  <option value="Done" selected>DONE</option>
                  <option value="Pending">PENDING</option>
                </select>
              </div>
              <div class="col-md-3 mb-3">
                <label class="form-label font-weight-bold text-xs text-muted">FR STATUS:</label>
                <select name="FR_status" class="form-select border px-3 py-2 font-weight-bold" style="border-radius: 0.5rem;">
                  <option value="Close" selected>CLOSE</option>
                  <option value="Open">OPEN</option>
                </select>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label font-weight-bold text-xs text-muted">ACTION TAKEN DETAILS:</label>
              <textarea name="action_taken" class="form-control border px-3 py-2" rows="3" placeholder="DESCRIBE ACTION TAKEN TO RESOLVE FAULT REPORT..." required style="border-radius: 0.5rem;"></textarea>
            </div>

            <div class="mb-3">
              <label class="form-label font-weight-bold text-xs text-muted">NOTE TO USER / REMARKS:</label>
              <textarea name="Note2User" class="form-control border px-3 py-2" rows="2" placeholder="OPTIONAL REMARKS TO BE SENT TO REPORTER..." style="border-radius: 0.5rem;"></textarea>
            </div>
          </div>
          <div class="modal-footer border-0 pt-0">
            <button type="button" class="btn btn-outline-secondary font-weight-bold text-uppercase" data-bs-dismiss="modal">CANCEL</button>
            <button type="submit" name="submit_action" class="btn bg-gradient-success font-weight-bold text-uppercase px-4">SAVE ACTION RECORD</button>
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
      $('#actionTable').DataTable({
        "pageLength": 10,
        "language": {
          "search": "SEARCH ACTION:",
          "lengthMenu": "SHOW _MENU_ RECORDS",
          "info": "SHOWING _START_ TO _END_ OF _TOTAL_ ACTIONS"
        }
      });
    });
  </script>
</body>

</html>
