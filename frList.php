<!--
=========================================================
* Material Dashboard 2 - Fast Fault Report List (FR List)
=========================================================
-->
<?php
session_start();
$page = 'fr_list';

require 'includes/connect.php';
require 'includes/line.php';
require 'includes/login_function.php';
require 'includes/controller/role_controller.php';

// Year filter selection (Default: All Years or Current Year)
$selectedYear = trim((string)($_GET['year'] ?? date("Y")));
$currentDiv = $divName ?? '';

// Build WHERE conditions for maximum query speed (single table query on `fr`)
$whereClauses = [];

// Filter strictly by the user's division (Headquarters users view Headquarters records; Kuching views Kuching, etc.)
if (!empty($currentDiv)) {
    $divSafe = $conn->real_escape_string($currentDiv);
    $whereClauses[] = "(Oridiv = '$divSafe' OR Oridiv LIKE '%$divSafe%')";
}

if ($selectedYear !== 'all' && !empty($selectedYear)) {
    $yearSafe = (int)$selectedYear;
    $whereClauses[] = "YEAR(date_add) = $yearSafe";
}

$whereSQL = !empty($whereClauses) ? " WHERE " . implode(" AND ", $whereClauses) : "";
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="./assets/img/favicon.svg">
  <link rel="icon" type="image/png" href="./assets/img/favicon.svg">
  <title>FAULT REPORT LIST - FRSYSTEM</title>
  
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
      <?php
      // Fast single-table statistics
      $totalFRCount = 0;
      $approvedCount = 0;
      $pendingAppCount = 0;

      // Available years for dropdown filter
      $yearsList = [];

      if (isset($conn) && $conn instanceof mysqli) {
          // Total FR count
          $resT = $conn->query("SELECT COUNT(*) as t FROM `fr` " . $whereSQL);
          if ($resT && $rT = $resT->fetch_assoc()) $totalFRCount = (int)$rT['t'];

          // Approved FR count
          $resApp = $conn->query("SELECT COUNT(*) as t FROM `fr` " . ($whereSQL ? $whereSQL . " AND " : " WHERE ") . "approval_status = 'Yes'");
          if ($resApp && $rApp = $resApp->fetch_assoc()) $approvedCount = (int)$rApp['t'];

          $pendingAppCount = max(0, $totalFRCount - $approvedCount);

          // Get list of distinct years from database
          $resY = $conn->query("SELECT DISTINCT YEAR(date_add) as yr FROM `fr` WHERE date_add IS NOT NULL AND YEAR(date_add) > 2000 ORDER BY yr DESC");
          if ($resY) {
              while ($yRow = $resY->fetch_assoc()) {
                  $yearsList[] = $yRow['yr'];
              }
          }
      }

      if (empty($yearsList)) {
          $yearsList = [date("Y"), date("Y") - 1, date("Y") - 2];
      }
      ?>

      <!-- Header KPI Cards -->
      <div class="row g-3 mb-4">
        <div class="col-xl-4 col-md-6">
          <div class="card border-0 shadow-sm" style="border-radius: 1rem;">
            <div class="card-body p-3">
              <div class="d-flex align-items-center">
                <div class="icon icon-shape bg-gradient-dark shadow-dark border-radius-xl p-3 me-3 text-center d-flex align-items-center justify-content-center" style="width:48px; height:48px;">
                  <i class="fas fa-list-alt text-white"></i>
                </div>
                <div>
                  <p class="text-xs text-uppercase font-weight-bold text-muted mb-0">TOTAL FAULT REPORTS (<?php echo htmlspecialchars($selectedYear); ?>)</p>
                  <h4 class="font-weight-bolder mb-0"><?php echo number_format($totalFRCount); ?></h4>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xl-4 col-md-6">
          <div class="card border-0 shadow-sm" style="border-radius: 1rem;">
            <div class="card-body p-3">
              <div class="d-flex align-items-center">
                <div class="icon icon-shape bg-gradient-success shadow-success border-radius-xl p-3 me-3 text-center d-flex align-items-center justify-content-center" style="width:48px; height:48px;">
                  <i class="fas fa-check-circle text-white"></i>
                </div>
                <div>
                  <p class="text-xs text-uppercase font-weight-bold text-muted mb-0">APPROVED REPORTS</p>
                  <h4 class="font-weight-bolder mb-0 text-success"><?php echo number_format($approvedCount); ?></h4>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xl-4 col-md-12">
          <div class="card border-0 shadow-sm" style="border-radius: 1rem;">
            <div class="card-body p-3">
              <div class="d-flex align-items-center">
                <div class="icon icon-shape bg-gradient-warning shadow-warning border-radius-xl p-3 me-3 text-center d-flex align-items-center justify-content-center" style="width:48px; height:48px;">
                  <i class="fas fa-clock text-white"></i>
                </div>
                <div>
                  <p class="text-xs text-uppercase font-weight-bold text-muted mb-0">PENDING / OTHER STATUS</p>
                  <h4 class="font-weight-bolder mb-0 text-warning"><?php echo number_format($pendingAppCount); ?></h4>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Main FR List Table with Year Filter -->
      <div class="row">
        <div class="col-12">
          <div class="card border-0 shadow-sm" style="border-radius: 1rem;">
            <div class="card-header bg-gradient-dark p-3" style="border-radius: 1rem 1rem 0 0;">
              <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                <div>
                  <h5 class="text-white mb-0 font-weight-bolder text-uppercase"><i class="fas fa-clipboard-list me-2"></i> FAULT REPORT LIST</h5>
                  <p class="text-white text-xs opacity-8 mb-0 text-uppercase">DIRECT HIGH-SPEED SINGLE TABLE LIST & YEAR FILTER</p>
                </div>
                
                <div class="d-flex align-items-center gap-2">
                  <!-- Year Filter Dropdown -->
                  <form method="GET" action="frList.php" class="d-flex align-items-center mb-0">
                    <label class="text-white text-xs font-weight-bold me-2 mb-0 text-uppercase">YEAR:</label>
                    <select name="year" class="form-select form-select-sm border px-2 py-1 text-uppercase font-weight-bold" style="border-radius: 0.5rem; background:#ffffff;" onchange="this.form.submit()">
                      <option value="all" <?php echo ($selectedYear === 'all') ? 'selected' : ''; ?>>ALL YEARS</option>
                      <?php foreach ($yearsList as $yOpt) { ?>
                        <option value="<?php echo $yOpt; ?>" <?php echo ((string)$yOpt === (string)$selectedYear) ? 'selected' : ''; ?>>
                          <?php echo $yOpt; ?>
                        </option>
                      <?php } ?>
                    </select>
                  </form>
                  <a href="dataEntry.php" class="btn btn-sm btn-info mb-0 text-uppercase"><i class="fas fa-plus me-1"></i> LODGE NEW FR</a>
                </div>
              </div>
            </div>

            <div class="card-body p-4">
              <div class="table-responsive">
                <table class="table table-hover align-items-center mb-0 text-uppercase" id="frListTable">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">FR NUMBER / CATEGORY</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">REQUESTED BY</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">DIVISION</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">SECTION</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">EQUIPMENT / BRAND</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">APPROVAL STATUS</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">DATE LODGED</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    if (isset($conn) && $conn instanceof mysqli) {
                        // High-speed direct query on table `fr` without expensive JOINs
                        $sqlFR = "SELECT 
                                    Frn, 
                                    frcate, 
                                    frntype, 
                                    request_by, 
                                    Oridiv, 
                                    Section, 
                                    equip,
                                    brand,
                                    approval_status,
                                    date_add
                                  FROM `fr`
                                  " . $whereSQL . "
                                  ORDER BY date_add DESC 
                                  LIMIT 500";

                        $resFR = $conn->query($sqlFR);
                        if ($resFR && $resFR->num_rows > 0) {
                            while ($row = $resFR->fetch_assoc()) {
                                $frn = htmlspecialchars($row['Frn']);
                                $cate = htmlspecialchars($row['frcate'] ?? 'GENERAL');
                                $reqBy = htmlspecialchars($row['request_by'] ?? 'N/A');
                                $div = htmlspecialchars($row['Oridiv'] ?? '-');
                                $sect = htmlspecialchars($row['Section'] ?? '-');
                                $equip = htmlspecialchars($row['equip'] ?? '-');
                                $brand = htmlspecialchars($row['brand'] ?? '');
                                $equipDisplay = trim($equip . ' ' . $brand);
                                $appStatus = strtoupper(htmlspecialchars($row['approval_status'] ?? 'NO'));
                                $dateAdd = htmlspecialchars(substr($row['date_add'] ?? '', 0, 10));

                                $appBadgeClass = ($appStatus === 'YES') ? 'bg-gradient-success' : 'bg-gradient-secondary';
                    ?>
                                <tr>
                                  <td>
                                    <div class="d-flex px-2 py-1 align-items-center">
                                      <div class="icon icon-shape icon-xs me-3 bg-gradient-dark text-white border-radius-md d-flex align-items-center justify-content-center">
                                        <i class="fas fa-file-alt"></i>
                                      </div>
                                      <div class="d-flex flex-column justify-content-center">
                                        <a href="frDetail.php?frn=<?php echo urlencode($frn); ?>" class="text-dark font-weight-bolder text-decoration-underline text-sm text-uppercase">
                                          <?php echo $frn; ?> <i class="fas fa-external-link-alt ms-1 text-xxs text-primary"></i>
                                        </a>
                                        <span class="text-xxs text-primary font-weight-bold text-uppercase"><?php echo $cate; ?></span>
                                      </div>
                                    </div>
                                  </td>
                                  <td>
                                    <span class="text-xs font-weight-bold text-dark text-uppercase"><?php echo $reqBy; ?></span>
                                  </td>
                                  <td class="align-middle text-center">
                                    <span class="badge bg-gradient-info text-xxs font-weight-bold text-uppercase"><?php echo $div; ?></span>
                                  </td>
                                  <td class="align-middle text-center">
                                    <span class="text-xs font-weight-bold text-muted text-uppercase"><?php echo $sect; ?></span>
                                  </td>
                                  <td class="align-middle text-center">
                                    <span class="text-xs font-weight-bold text-dark text-uppercase"><?php echo !empty($equipDisplay) ? $equipDisplay : '-'; ?></span>
                                  </td>
                                  <td class="align-middle text-center">
                                    <span class="badge <?php echo $appBadgeClass; ?> text-xxs font-weight-bold text-uppercase"><?php echo $appStatus; ?></span>
                                  </td>
                                  <td class="align-middle text-center">
                                    <span class="text-xxs font-weight-bold text-muted"><?php echo $dateAdd; ?></span>
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
      $('#frListTable').DataTable({
        "pageLength": 10,
        "language": {
          "search": "SEARCH FR:",
          "lengthMenu": "SHOW _MENU_ RECORDS",
          "info": "SHOWING _START_ TO _END_ OF _TOTAL_ REPORTS"
        }
      });
    });
  </script>
</body>

</html>
