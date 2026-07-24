<!--
=========================================================
* Material Dashboard 2 - My Personal Dashboard (myDashboard.php)
=========================================================
-->
<?php
session_start();
$page = 'my_dashboard';

require 'includes/connect.php';
require 'includes/line.php';
require 'includes/login_function.php';
require 'includes/controller/role_controller.php';

// Get current logged in user details & role
$currentUserFull = 'Pengguna';
$currentUsername = $_SESSION['uid'] ?? $_SESSION['user'] ?? $_SESSION['username'] ?? '';
$currentUserRole = $userRole ?? 'NU';
$currentUserDiv = $divName ?? 'Headquarters';

if (isset($conn) && $conn instanceof mysqli && !empty($currentUsername)) {
    $uSafe = $conn->real_escape_string($currentUsername);
    $resU = $conn->query("SELECT * FROM `user` WHERE username = '$uSafe' OR uid = '$uSafe' OR email = '$uSafe' LIMIT 1");
    if ($resU && $rowU = $resU->fetch_assoc()) {
        $currentUserFull = $rowU['name'] ?? $rowU['username'] ?? $currentUsername;
        $currentUserRole = strtoupper(trim((string)($rowU['Role'] ?? $currentUserRole)));
        $currentUserDiv = $rowU['Division'] ?? $currentUserDiv;
    }
}

if ($currentUserFull === 'Pengguna' && !empty($fullname)) {
    $currentUserFull = $fullname;
}

$isFocalPerson = ($currentUserRole === 'FP');

// Year filter selection
$selectedMyYear = trim((string)($_GET['year'] ?? date("Y")));
$yearWhereAssigned = "";
$yearWhereLodged = "";
$yearWhereDivision = "";

if ($selectedMyYear !== 'all' && !empty($selectedMyYear)) {
    $yVal = (int)$selectedMyYear;
    $yearWhereAssigned = " AND YEAR(f.date_add) = $yVal ";
    $yearWhereLodged = " AND YEAR(date_add) = $yVal ";
    $yearWhereDivision = " AND YEAR(date_add) = $yVal ";
}

// Personal Statistics Initialization
$assignedTotalCount = 0;
$assignedSolvedCount = 0;
$assignedPendingCount = 0;
$myLodgedTotalCount = 0;

// FP specific statistics
$fpNewFRCount = 0;
$fpPendingAssignCount = 0;
$fpNewFRList = [];

$myAssignedFRs = [];
$myLodgedFRs = [];
$myYearsList = [];

if (isset($conn) && $conn instanceof mysqli) {
    $userFullSafe = $conn->real_escape_string($currentUserFull);
    $userUidSafe = $conn->real_escape_string($currentUsername);
    $userDivSafe = $conn->real_escape_string($currentUserDiv);

    // Get list of distinct years
    $resY = $conn->query("SELECT DISTINCT YEAR(date_add) as yr FROM `fr` WHERE date_add IS NOT NULL AND YEAR(date_add) > 2000 ORDER BY yr DESC");
    if ($resY) {
        while ($yRow = $resY->fetch_assoc()) {
            $myYearsList[] = $yRow['yr'];
        }
    }

    if ($isFocalPerson) {
        // === FOCAL PERSON (FP) SPECIALIZED LOGIC ===
        // 1. Fetch New Unassigned FRs received for FP's Division
        $sqlFpUnassigned = "SELECT Frn, frcate, request_by, Oridiv, Section, Description, date_add 
                            FROM `fr` 
                            WHERE (Oridiv = '$userDivSafe' OR Oridiv LIKE '%$userDivSafe%')
                            AND Frn NOT IN (SELECT Assfrno FROM `assign`) " . $yearWhereDivision . "
                            ORDER BY date_add DESC";
        $resFpUnassigned = $conn->query($sqlFpUnassigned);
        if ($resFpUnassigned) {
            $fpNewFRCount = $resFpUnassigned->num_rows;
            $fpPendingAssignCount = $fpNewFRCount;
            while ($rowFp = $resFpUnassigned->fetch_assoc()) {
                $fpNewFRList[] = $rowFp;
            }
        }

        // 2. Fetch Officers in FP Division for quick assign dropdown
        $fpOfficersList = [];
        $resOff = $conn->query("SELECT name, username, Role, brasec, Division FROM `user` WHERE (Division = '$userDivSafe' OR Division LIKE '%$userDivSafe%') AND active = 'Y' ORDER BY name ASC");
        if ($resOff) {
            while ($rowOff = $resOff->fetch_assoc()) {
                $fpOfficersList[] = $rowOff;
            }
        }
    } else {
        // === NON-FP USER LOGIC ===
        $assignedFrnArray = [];
        $sqlGetAssigned = "SELECT DISTINCT Assfrno, assign_date FROM `assign` 
                           WHERE assign_to = '$userFullSafe' OR assign_to = '$userUidSafe'";
        $resGetA = $conn->query($sqlGetAssigned);
        if ($resGetA) {
            while ($rA = $resGetA->fetch_assoc()) {
                if (!empty($rA['Assfrno'])) {
                    $assignedFrnArray[$rA['Assfrno']] = $rA['assign_date'];
                }
            }
        }

        $sqlGetAction = "SELECT DISTINCT frno FROM `action` 
                         WHERE ActionTakenBy = '$userFullSafe' OR ActionTakenBy = '$userUidSafe'";
        $resGetAct = $conn->query($sqlGetAction);
        if ($resGetAct) {
            while ($rAct = $resGetAct->fetch_assoc()) {
                if (!empty($rAct['frno']) && !isset($assignedFrnArray[$rAct['frno']])) {
                    $assignedFrnArray[$rAct['frno']] = null;
                }
            }
        }

        if (!empty($assignedFrnArray)) {
            $frnListEscaped = "'" . implode("','", array_map([$conn, 'real_escape_string'], array_keys($assignedFrnArray))) . "'";

            $sqlAssigned = "SELECT f.Frn, f.frcate, f.request_by, f.Oridiv, f.Section, f.date_add, f.Description
                            FROM `fr` f
                            WHERE f.Frn IN ($frnListEscaped) " . $yearWhereAssigned . "
                            ORDER BY f.date_add DESC";

            $resAssigned = $conn->query($sqlAssigned);
            if ($resAssigned) {
                $assignedTotalCount = $resAssigned->num_rows;
                while ($rowAssg = $resAssigned->fetch_assoc()) {
                    $rowAssg['assign_date'] = $assignedFrnArray[$rowAssg['Frn']] ?? $rowAssg['date_add'];
                    $myAssignedFRs[] = $rowAssg;
                }
            }

            $sqlSolved = "SELECT COUNT(DISTINCT frno) as total FROM `action` 
                          WHERE frno IN ($frnListEscaped) AND (FR_status = 'Close' OR action_status = 'Done')";
            $resSolved = $conn->query($sqlSolved);
            if ($resSolved && $rSol = $resSolved->fetch_assoc()) {
                $assignedSolvedCount = (int)$rSol['total'];
            }
        }

        $assignedPendingCount = max(0, $assignedTotalCount - $assignedSolvedCount);

        $sqlMyLodged = "SELECT Frn, frcate, Oridiv, Section, approval_status, date_add 
                        FROM `fr` 
                        WHERE (request_by = '$userFullSafe' OR request_by = '$userUidSafe') " . $yearWhereLodged . "
                        ORDER BY date_add DESC LIMIT 20";
        $resMyLodged = $conn->query($sqlMyLodged);
        if ($resMyLodged) {
            $myLodgedTotalCount = $resMyLodged->num_rows;
            while ($rowLodge = $resMyLodged->fetch_assoc()) {
                $myLodgedFRs[] = $rowLodge;
            }
        }
    }
}

if (empty($myYearsList)) {
    $myYearsList = [date("Y"), date("Y") - 1, date("Y") - 2];
}

$mySolvedRate = $assignedTotalCount > 0 ? round(($assignedSolvedCount / $assignedTotalCount) * 100, 1) : 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="./assets/img/favicon.svg">
  <link rel="icon" type="image/png" href="./assets/img/favicon.svg">
  <title>MY DASHBOARD - FRSYSTEM</title>
  
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
      <!-- Header Personal Banner -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card bg-gradient-dark border-0 shadow-lg position-relative overflow-hidden p-3" style="border-radius: 1rem; background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);">
            <div class="row align-items-center">
              <div class="col-lg-7 col-md-6">
                <span class="badge bg-white text-dark mb-2 text-uppercase tracking-wider px-3 py-2 font-weight-bolder"><i class="fas fa-user-circle me-1"></i> Personal Officer Dashboard</span>
                <h2 class="text-white font-weight-bolder mb-1">WELCOME, <?php echo htmlspecialchars($currentUserFull); ?></h2>
                <p class="text-white opacity-9 text-sm mb-0 text-uppercase">PERSONAL OVERVIEW OF FAULT REPORTS ASSIGNED TO YOU AND REPORTS SUBMITTED BY YOUR ACCOUNT.</p>
              </div>
              <div class="col-lg-5 col-md-6 text-end">
                <!-- Year Filter Form -->
                <form method="GET" action="myDashboard.php" class="d-inline-flex align-items-center justify-content-end gap-2 mb-2">
                  <label class="text-white text-xs font-weight-bold mb-0 text-uppercase"><i class="fas fa-calendar-alt me-1"></i> YEAR:</label>
                  <select name="year" class="form-select form-select-sm border px-2 py-1 text-uppercase font-weight-bold" style="border-radius: 0.5rem; background:#ffffff; width: auto;" onchange="this.form.submit()">
                    <option value="all" <?php echo ($selectedMyYear === 'all') ? 'selected' : ''; ?>>ALL YEARS</option>
                    <?php foreach ($myYearsList as $yOpt) { ?>
                      <option value="<?php echo $yOpt; ?>" <?php echo ((string)$yOpt === (string)$selectedMyYear) ? 'selected' : ''; ?>>
                        <?php echo $yOpt; ?>
                      </option>
                    <?php } ?>
                  </select>
                </form>
                <div>
                  <a href="dataEntry.php" class="btn btn-sm btn-light text-dark mb-0 me-2 text-uppercase"><i class="fas fa-plus me-1"></i> LODGE NEW FR</a>
                  <a href="myDashboard.php" class="btn btn-sm btn-outline-white mb-0 text-uppercase"><i class="fas fa-sync-alt me-1"></i> REFRESH</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Metric KPI Cards -->
      <div class="row g-3 mb-4">
        <?php if ($isFocalPerson) { ?>
          <!-- Focal Person (FP) Specialized KPI Cards -->
          <div class="col-xl-6 col-md-6">
            <div class="card border-0 shadow-sm" style="border-radius: 1rem;">
              <div class="card-body p-3">
                <div class="d-flex align-items-center">
                  <div class="icon icon-shape bg-gradient-danger shadow-danger border-radius-xl p-3 me-3 text-center d-flex align-items-center justify-content-center" style="width:48px; height:48px;">
                    <i class="fas fa-bell text-white"></i>
                  </div>
                  <div>
                    <p class="text-xs text-uppercase font-weight-bold text-muted mb-0">NEW FR RECEIVED (<?php echo htmlspecialchars(strtoupper($currentUserDiv)); ?>)</p>
                    <h4 class="font-weight-bolder mb-0 text-danger"><?php echo number_format($fpNewFRCount); ?></h4>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-xl-6 col-md-6">
            <div class="card border-0 shadow-sm" style="border-radius: 1rem;">
              <div class="card-body p-3">
                <div class="d-flex align-items-center">
                  <div class="icon icon-shape bg-gradient-warning shadow-warning border-radius-xl p-3 me-3 text-center d-flex align-items-center justify-content-center" style="width:48px; height:48px;">
                    <i class="fas fa-user-clock text-white"></i>
                  </div>
                  <div>
                    <p class="text-xs text-uppercase font-weight-bold text-muted mb-0">PENDING ASSIGN</p>
                    <h4 class="font-weight-bolder mb-0 text-warning"><?php echo number_format($fpPendingAssignCount); ?></h4>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php } else { ?>
          <!-- Standard User KPI Cards -->
          <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm" style="border-radius: 1rem;">
              <div class="card-body p-3">
                <div class="d-flex align-items-center">
                  <div class="icon icon-shape bg-gradient-primary shadow-primary border-radius-xl p-3 me-3 text-center d-flex align-items-center justify-content-center" style="width:48px; height:48px;">
                    <i class="fas fa-tasks text-white"></i>
                  </div>
                  <div>
                    <p class="text-xs text-uppercase font-weight-bold text-muted mb-0">ASSIGNED TO ME</p>
                    <h4 class="font-weight-bolder mb-0 text-primary"><?php echo number_format($assignedTotalCount); ?></h4>
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
                    <i class="fas fa-check-circle text-white"></i>
                  </div>
                  <div>
                    <p class="text-xs text-uppercase font-weight-bold text-muted mb-0">MY SOLVED REPORTS</p>
                    <h4 class="font-weight-bolder mb-0 text-success"><?php echo number_format($assignedSolvedCount); ?></h4>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm" style="border-radius: 1rem;">
              <div class="card-body p-3">
                <div class="d-flex align-items-center">
                  <div class="icon icon-shape bg-gradient-warning shadow-warning border-radius-xl p-3 me-3 text-center d-flex align-items-center justify-content-center" style="width:48px; height:48px;">
                    <i class="fas fa-clock text-white"></i>
                  </div>
                  <div>
                    <p class="text-xs text-uppercase font-weight-bold text-muted mb-0">MY PENDING REPORTS</p>
                    <h4 class="font-weight-bolder mb-0 text-warning"><?php echo number_format($assignedPendingCount); ?></h4>
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
                    <i class="fas fa-paper-plane text-white"></i>
                  </div>
                  <div>
                    <p class="text-xs text-uppercase font-weight-bold text-muted mb-0">REPORTS LODGED BY ME</p>
                    <h4 class="font-weight-bolder mb-0 text-info"><?php echo number_format($myLodgedTotalCount); ?></h4>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php } ?>
      </div>

      <!-- Main Table Section -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card border-0 shadow-sm" style="border-radius: 1rem;">
            <?php if ($isFocalPerson) { ?>
              <!-- Focal Person Table: New FR Received to Assign -->
              <div class="card-header bg-gradient-dark p-3" style="border-radius: 1rem 1rem 0 0;">
                <h5 class="text-white mb-0 font-weight-bolder text-uppercase"><i class="fas fa-inbox me-2"></i> NEW FAULT REPORTS RECEIVED (TO BE ASSIGNED)</h5>
                <p class="text-white text-xs opacity-8 mb-0 text-uppercase">UNASSIGNED FAULT REPORTS SUBMITTED IN <?php echo htmlspecialchars(strtoupper($currentUserDiv)); ?> DIVISION</p>
              </div>
              <div class="card-body p-4">
                <div class="table-responsive">
                  <table class="table table-hover align-items-center mb-0 text-uppercase" id="fpNewFRTable">
                    <thead>
                      <tr>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">FR NUMBER / CATEGORY</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">REPORTER</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">SECTION</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">DATE RECEIVED</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">ACTION</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if (!empty($fpNewFRList)) {
                        foreach ($fpNewFRList as $fpFr) {
                          $fpFrn = htmlspecialchars($fpFr['Frn']);
                          $fpCate = htmlspecialchars($fpFr['frcate'] ?? 'GENERAL');
                          $fpReq = htmlspecialchars($fpFr['request_by'] ?? 'N/A');
                          $fpSect = htmlspecialchars($fpFr['Section'] ?? '-');
                          $fpDate = htmlspecialchars(substr($fpFr['date_add'] ?? '', 0, 10));
                      ?>
                          <tr>
                            <td>
                              <div class="d-flex px-2 py-1 align-items-center">
                                <div class="icon icon-shape icon-xs me-3 bg-gradient-danger text-white border-radius-md d-flex align-items-center justify-content-center">
                                  <i class="fas fa-exclamation-circle"></i>
                                </div>
                                <div class="d-flex flex-column justify-content-center">
                                  <a href="frDetail.php?frn=<?php echo urlencode($fpFrn); ?>" class="text-dark font-weight-bolder text-decoration-underline text-sm text-uppercase">
                                    <?php echo $fpFrn; ?> <i class="fas fa-external-link-alt ms-1 text-xxs text-primary"></i>
                                  </a>
                                  <span class="text-xxs text-primary font-weight-bold text-uppercase"><?php echo $fpCate; ?></span>
                                </div>
                              </div>
                            </td>
                            <td>
                              <span class="text-xs font-weight-bold text-dark text-uppercase"><?php echo $fpReq; ?></span>
                            </td>
                            <td class="align-middle text-center">
                              <span class="badge bg-light text-dark border text-xxs font-weight-bold text-uppercase"><?php echo $fpSect; ?></span>
                            </td>
                            <td class="align-middle text-center">
                              <span class="text-xxs font-weight-bold text-muted"><?php echo $fpDate; ?></span>
                            </td>
                            <td class="align-middle text-center">
                              <a href="assignManagement.php" class="btn btn-sm bg-gradient-primary text-white mb-0 text-uppercase me-1">
                                <i class="fas fa-user-plus me-1"></i> ASSIGN OFFICER
                              </a>
                              <a href="frDetail.php?frn=<?php echo urlencode($fpFrn); ?>" class="btn btn-sm btn-outline-info mb-0 text-uppercase">
                                <i class="fas fa-eye"></i> VIEW
                              </a>
                            </td>
                          </tr>
                      <?php } } ?>
                    </tbody>
                  </table>
                </div>
              </div>
            <?php } else { ?>
              <!-- Standard User Table: Assigned Tasks -->
              <div class="card-header bg-gradient-dark p-3" style="border-radius: 1rem 1rem 0 0;">
                <h5 class="text-white mb-0 font-weight-bolder text-uppercase"><i class="fas fa-clipboard-check me-2"></i> FAULT REPORTS ASSIGNED TO ME</h5>
                <p class="text-white text-xs opacity-8 mb-0 text-uppercase">LIST OF FAULT REPORTS CURRENTLY ASSIGNED TO YOUR ACCOUNT</p>
              </div>
              <div class="card-body p-4">
                <div class="table-responsive">
                  <table class="table table-hover align-items-center mb-0 text-uppercase" id="myAssignedTable">
                    <thead>
                      <tr>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">FR NUMBER / CATEGORY</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">REQUESTED BY</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">DIVISION / SECTION</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">ASSIGNED DATE</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">ACTION</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if (!empty($myAssignedFRs)) {
                        foreach ($myAssignedFRs as $mFr) {
                          $mFrn = htmlspecialchars($mFr['Frn']);
                          $mCate = htmlspecialchars($mFr['frcate'] ?? 'GENERAL');
                          $mReq = htmlspecialchars($mFr['request_by'] ?? 'N/A');
                          $mDiv = htmlspecialchars($mFr['Oridiv'] ?? '-');
                          $mSect = htmlspecialchars($mFr['Section'] ?? '-');
                          $mDate = htmlspecialchars(substr($mFr['assign_date'] ?? $mFr['date_add'] ?? '', 0, 10));
                      ?>
                          <tr>
                            <td>
                              <div class="d-flex px-2 py-1 align-items-center">
                                <div class="icon icon-shape icon-xs me-3 bg-gradient-dark text-white border-radius-md d-flex align-items-center justify-content-center">
                                  <i class="fas fa-file-alt"></i>
                                </div>
                                <div class="d-flex flex-column justify-content-center">
                                  <a href="frDetail.php?frn=<?php echo urlencode($mFrn); ?>" class="text-dark font-weight-bolder text-decoration-underline text-sm text-uppercase">
                                    <?php echo $mFrn; ?> <i class="fas fa-external-link-alt ms-1 text-xxs text-primary"></i>
                                  </a>
                                  <span class="text-xxs text-primary font-weight-bold text-uppercase"><?php echo $mCate; ?></span>
                                </div>
                              </div>
                            </td>
                            <td>
                              <span class="text-xs font-weight-bold text-dark text-uppercase"><?php echo $mReq; ?></span>
                            </td>
                            <td class="align-middle text-center">
                              <span class="badge bg-gradient-info text-xxs font-weight-bold text-uppercase"><?php echo $mDiv; ?></span>
                              <span class="text-xxs text-muted d-block text-uppercase"><?php echo $mSect; ?></span>
                            </td>
                            <td class="align-middle text-center">
                              <span class="text-xxs font-weight-bold text-muted"><?php echo $mDate; ?></span>
                            </td>
                            <td class="align-middle text-center">
                              <a href="frDetail.php?frn=<?php echo urlencode($mFrn); ?>" class="btn btn-sm btn-outline-primary mb-0 text-uppercase"><i class="fas fa-eye me-1"></i> VIEW DETAILS</a>
                            </td>
                          </tr>
                      <?php } } ?>
                    </tbody>
                  </table>
                </div>
              </div>
            <?php } ?>
          </div>
        </div>
      </div>

      <?php if (!$isFocalPerson) { ?>
        <!-- Secondary Section: Reports Lodged by Me (Hidden for FP Role) -->
        <div class="row">
          <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 1rem;">
              <div class="card-header bg-gradient-dark p-3" style="border-radius: 1rem 1rem 0 0;">
                <h5 class="text-white mb-0 font-weight-bolder text-uppercase"><i class="fas fa-paper-plane me-2"></i> REPORTS LODGED BY ME</h5>
                <p class="text-white text-xs opacity-8 mb-0 text-uppercase">RECENT FAULT REPORTS SUBMITTED UNDER YOUR ACCOUNT</p>
              </div>
              <div class="card-body p-4">
                <div class="table-responsive">
                  <table class="table table-hover align-items-center mb-0 text-uppercase" id="myLodgedTable">
                    <thead>
                      <tr>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">FR NUMBER / CATEGORY</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">DIVISION / SECTION</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">APPROVAL STATUS</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">DATE LODGED</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">ACTION</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if (!empty($myLodgedFRs)) {
                        foreach ($myLodgedFRs as $lFr) {
                          $lFrn = htmlspecialchars($lFr['Frn']);
                          $lCate = htmlspecialchars($lFr['frcate'] ?? 'GENERAL');
                          $lDiv = htmlspecialchars($lFr['Oridiv'] ?? '-');
                          $lSect = htmlspecialchars($lFr['Section'] ?? '-');
                          $lApp = strtoupper(htmlspecialchars($lFr['approval_status'] ?? 'NO'));
                          $lDate = htmlspecialchars(substr($lFr['date_add'] ?? '', 0, 10));

                          $appBadge = ($lApp === 'YES') ? 'bg-gradient-success' : 'bg-gradient-secondary';
                      ?>
                          <tr>
                            <td>
                              <div class="d-flex px-2 py-1 align-items-center">
                                <div class="icon icon-shape icon-xs me-3 bg-gradient-info text-white border-radius-md d-flex align-items-center justify-content-center">
                                  <i class="fas fa-file-invoice"></i>
                                </div>
                                <div class="d-flex flex-column justify-content-center">
                                  <a href="frDetail.php?frn=<?php echo urlencode($lFrn); ?>" class="text-dark font-weight-bolder text-decoration-underline text-sm text-uppercase">
                                    <?php echo $lFrn; ?> <i class="fas fa-external-link-alt ms-1 text-xxs text-primary"></i>
                                  </a>
                                  <span class="text-xxs text-primary font-weight-bold text-uppercase"><?php echo $lCate; ?></span>
                                </div>
                              </div>
                            </td>
                            <td class="align-middle text-center">
                              <span class="badge bg-light text-dark border text-xxs font-weight-bold text-uppercase"><?php echo $lDiv; ?></span>
                              <span class="text-xxs text-muted d-block text-uppercase"><?php echo $lSect; ?></span>
                            </td>
                            <td class="align-middle text-center">
                              <span class="badge <?php echo $appBadge; ?> text-xxs font-weight-bold text-uppercase"><?php echo $lApp; ?></span>
                            </td>
                            <td class="align-middle text-center">
                              <span class="text-xxs font-weight-bold text-muted"><?php echo $lDate; ?></span>
                            </td>
                            <td class="align-middle text-center">
                              <a href="frDetail.php?frn=<?php echo urlencode($lFrn); ?>" class="btn btn-sm btn-outline-info mb-0 text-uppercase"><i class="fas fa-eye me-1"></i> VIEW DETAILS</a>
                            </td>
                          </tr>
                      <?php } } ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php } ?>

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
  <script src="./assets/js/material-dashboard.min.js?v=3.0.0"></script>
  <script src="./assets/datatables/dataTables.js"></script>

  <script>
    $(document).ready(function() {
      if ($('#fpNewFRTable').length) {
        $('#fpNewFRTable').DataTable({
          "pageLength": 10,
          "language": {
            "search": "SEARCH RECEIVED FR:",
            "lengthMenu": "SHOW _MENU_ RECORDS",
            "info": "SHOWING _START_ TO _END_ OF _TOTAL_ NEW REPORTS"
          }
        });
      }

      if ($('#myAssignedTable').length) {
        $('#myAssignedTable').DataTable({
          "pageLength": 5,
          "language": {
            "search": "SEARCH ASSIGNED FR:",
            "lengthMenu": "SHOW _MENU_ RECORDS",
            "info": "SHOWING _START_ TO _END_ OF _TOTAL_ ASSIGNED REPORTS"
          }
        });
      }

      if ($('#myLodgedTable').length) {
        $('#myLodgedTable').DataTable({
          "pageLength": 5,
          "language": {
            "search": "SEARCH MY LODGED FR:",
            "lengthMenu": "SHOW _MENU_ RECORDS",
            "info": "SHOWING _START_ TO _END_ OF _TOTAL_ LODGED REPORTS"
          }
        });
      }
    });
  </script>
</body>

</html>
