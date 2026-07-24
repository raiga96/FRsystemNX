<div class="container-fluid py-4">
  <?php
  // Year filter selection (Default: All Years or Current Year)
  $selectedHqYear = trim((string)($_GET['year'] ?? date("Y")));

  $yearWhereFR = "";
  $yearWhereAction = "";
  $yearWhereRefer = "";

  if ($selectedHqYear !== 'all' && !empty($selectedHqYear)) {
      $yVal = (int)$selectedHqYear;
      $yearWhereFR = " AND YEAR(date_add) = $yVal ";
      $yearWhereAction = " AND frno IN (SELECT Frn FROM `fr` WHERE YEAR(date_add) = $yVal) ";
      $yearWhereRefer = " AND FrRefId IN (SELECT Frn FROM `fr` WHERE YEAR(date_add) = $yVal) ";
  }

  // Initialize default counts safely
  $totalFR = 0;
  $newCount = 0;
  $inProgressCount = 0;
  $solvedCount = 0;
  $unassignedCount = 0;
  $softwareCount = 0;
  $hardwareCount = 0;
  $othersCount = 0;
  $activeStaff = 0;
  $inactiveStaff = 0;
  $totalStaff = 0;
  $sainsRefTotal = 0;
  $recentFRs = [];
  $divisionBreakdown = [];
  $hqYearsList = [];

  if (isset($conn) && $conn instanceof mysqli) {
      // Get list of distinct years
      $resY = $conn->query("SELECT DISTINCT YEAR(date_add) as yr FROM `fr` WHERE date_add IS NOT NULL AND YEAR(date_add) > 2000 ORDER BY yr DESC");
      if ($resY) {
          while ($yRow = $resY->fetch_assoc()) {
              $hqYearsList[] = $yRow['yr'];
          }
      }

      // 1. Total FR (Headquarters only)
      $res = $conn->query("SELECT COUNT(*) AS total FROM `fr` WHERE (Oridiv = 'Headquarters' OR Oridiv LIKE '%Headquarters%') " . $yearWhereFR);
      if ($res && $row = $res->fetch_assoc()) {
          $totalFR = (int)$row['total'];
      }

      // 2. Solved FR (Headquarters only)
      $res = $conn->query("SELECT COUNT(*) AS total FROM `action` WHERE frno IN (SELECT Frn FROM `fr` WHERE Oridiv = 'Headquarters' OR Oridiv LIKE '%Headquarters%') AND (FR_status = 'Close' OR action_status = 'Done') " . $yearWhereAction);
      if ($res && $row = $res->fetch_assoc()) {
          $solvedCount = (int)$row['total'];
      }

      // 3. In Progress FR (Headquarters only)
      $res = $conn->query("SELECT COUNT(*) AS total FROM `action` WHERE frno IN (SELECT Frn FROM `fr` WHERE Oridiv = 'Headquarters' OR Oridiv LIKE '%Headquarters%') AND FR_status = 'Open' AND action_status != 'Done' " . $yearWhereAction);
      if ($res && $row = $res->fetch_assoc()) {
          $inProgressCount = (int)$row['total'];
      }

      // 4. New / Unassigned FR (Headquarters only)
      $res = $conn->query("SELECT COUNT(*) AS total FROM `fr` WHERE (Oridiv = 'Headquarters' OR Oridiv LIKE '%Headquarters%') AND Frn NOT IN (SELECT Assfrno FROM `assign`) " . $yearWhereFR);
      if ($res && $row = $res->fetch_assoc()) {
          $newCount = (int)$row['total'];
          $unassignedCount = $newCount;
      }

      // 5. FR Categories (Headquarters only)
      $res = $conn->query("SELECT COUNT(*) AS total FROM `fr` WHERE (Oridiv = 'Headquarters' OR Oridiv LIKE '%Headquarters%') AND (frcate LIKE '%Software%' OR frcate LIKE '%Application%') " . $yearWhereFR);
      if ($res && $row = $res->fetch_assoc()) {
          $softwareCount = (int)$row['total'];
      }

      $res = $conn->query("SELECT COUNT(*) AS total FROM `fr` WHERE (Oridiv = 'Headquarters' OR Oridiv LIKE '%Headquarters%') AND frcate LIKE '%Hardware%' " . $yearWhereFR);
      if ($res && $row = $res->fetch_assoc()) {
          $hardwareCount = (int)$row['total'];
      }

      $othersCount = max(0, $totalFR - ($softwareCount + $hardwareCount));

      // 6. Referral to External (SAINS/ISB) (Headquarters only)
      $res = $conn->query("SELECT COUNT(*) AS total FROM `refer_to` WHERE FrRefId IN (SELECT Frn FROM `fr` WHERE Oridiv = 'Headquarters' OR Oridiv LIKE '%Headquarters%') AND (Refcate LIKE '%SAINS%' OR Refcate LIKE '%ISB%') " . $yearWhereRefer);
      if ($res && $row = $res->fetch_assoc()) {
          $sainsRefTotal = (int)$row['total'];
      }

      // 7. Headquarters Users count
      $res = $conn->query("SELECT COUNT(*) AS total FROM `user` WHERE Division = 'Headquarters' OR Division LIKE '%Headquarters%'");
      if ($res && $row = $res->fetch_assoc()) {
          $totalStaff = (int)$row['total'];
      }

      $res = $conn->query("SELECT COUNT(*) AS total FROM `user` WHERE (Division = 'Headquarters' OR Division LIKE '%Headquarters%') AND active = 'Y'");
      if ($res && $row = $res->fetch_assoc()) {
          $activeStaff = (int)$row['total'];
      }
      $inactiveStaff = max(0, $totalStaff - $activeStaff);

      // 8. Recent 5 Fault Reports (Headquarters only)
      $resRecent = $conn->query("SELECT Frn, frcate, request_by, Oridiv, date_add FROM `fr` WHERE (Oridiv = 'Headquarters' OR Oridiv LIKE '%Headquarters%') " . $yearWhereFR . " ORDER BY date_add DESC LIMIT 5");
      if ($resRecent) {
          while ($rRow = $resRecent->fetch_assoc()) {
              $recentFRs[] = $rRow;
          }
      }

      // 9. Division Breakdown (Top Divisions by FR count)
      $resDiv = $conn->query("SELECT Oridiv, COUNT(*) as total FROM `fr` WHERE Oridiv IS NOT NULL AND Oridiv != '' " . $yearWhereFR . " GROUP BY Oridiv ORDER BY total DESC LIMIT 5");
      if ($resDiv) {
          while ($dRow = $resDiv->fetch_assoc()) {
              $divisionBreakdown[] = $dRow;
          }
      }
  }

  if (empty($hqYearsList)) {
      $hqYearsList = [date("Y"), date("Y") - 1, date("Y") - 2];
  }

  $solvedRate = $totalFR > 0 ? round(($solvedCount / $totalFR) * 100, 1) : 0;
  $inProgressRate = $totalFR > 0 ? round(($inProgressCount / $totalFR) * 100, 1) : 0;
  $unassignedRate = $totalFR > 0 ? round(($unassignedCount / $totalFR) * 100, 1) : 0;
  ?>

  <!-- Header Banner -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="card bg-gradient-dark border-0 shadow-lg position-relative overflow-hidden p-3" style="border-radius: 1rem; background: linear-gradient(135deg, #1e1e2f 0%, #0f1016 100%);">
        <div class="row align-items-center">
          <div class="col-lg-7 col-md-6">
            <span class="badge bg-gradient-info mb-2 text-uppercase tracking-wider px-3 py-2">HQ Fault Report Command Center</span>
            <h2 class="text-white font-weight-bolder mb-1">Fault Report System Overview</h2>
            <p class="text-white opacity-8 text-sm mb-0 text-uppercase">COMPREHENSIVE MONITORING OF DEVICE & APPLICATION FAULT REPORTS, EXTERNAL REFERRALS (SAINS), AND DIVISIONAL STATISTICS.</p>
          </div>
          <div class="col-lg-5 col-md-6 text-end">
            <!-- Year Filter Form -->
            <form method="GET" action="index.php" class="d-inline-flex align-items-center justify-content-end gap-2 mb-2">
              <label class="text-white text-xs font-weight-bold mb-0 text-uppercase"><i class="fas fa-calendar-alt me-1"></i> YEAR:</label>
              <select name="year" class="form-select form-select-sm border px-2 py-1 text-uppercase font-weight-bold" style="border-radius: 0.5rem; background:#ffffff; width: auto;" onchange="this.form.submit()">
                <option value="all" <?php echo ($selectedHqYear === 'all') ? 'selected' : ''; ?>>ALL YEARS</option>
                <?php foreach ($hqYearsList as $yOpt) { ?>
                  <option value="<?php echo $yOpt; ?>" <?php echo ((string)$yOpt === (string)$selectedHqYear) ? 'selected' : ''; ?>>
                    <?php echo $yOpt; ?>
                  </option>
                <?php } ?>
              </select>
            </form>
            <div>
              <a href="frList.php" class="btn btn-sm btn-outline-white mb-0 me-2 text-uppercase"><i class="fas fa-list me-1"></i> FR LIST</a>
              <a href="index.php" class="btn btn-sm btn-primary mb-0 text-uppercase"><i class="fas fa-sync-alt me-1"></i> REFRESH</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Metric KPI Cards -->
  <div class="row g-3 mb-4">
    <!-- Total Fault Reports Card -->
    <div class="col-xl-3 col-md-6">
      <div class="card border-0 shadow-sm h-100" style="border-radius: 1rem; background: #ffffff;">
        <div class="card-body p-3">
          <div class="d-flex align-items-center mb-3">
            <div class="icon icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl p-3 me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
              <i class="fas fa-exclamation-triangle text-white fa-lg"></i>
            </div>
            <div>
              <p class="text-xs text-uppercase font-weight-bold text-muted mb-0">TOTAL FAULT REPORTS (FR)</p>
              <h3 class="font-weight-bolder mb-0 text-dark"><?php echo number_format($totalFR); ?></h3>
            </div>
          </div>
          <div class="border-top pt-2">
            <div class="d-flex justify-content-between text-xxs text-muted font-weight-bold text-uppercase">
              <span><i class="fas fa-plus-circle text-warning me-1"></i> NEW: <?php echo $newCount; ?></span>
              <span><i class="fas fa-check-circle text-success me-1"></i> SOLVED: <?php echo $solvedCount; ?></span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Resolution Progress Card -->
    <div class="col-xl-3 col-md-6">
      <div class="card border-0 shadow-sm h-100" style="border-radius: 1rem; background: #ffffff;">
        <div class="card-body p-3">
          <div class="d-flex align-items-center mb-3">
            <div class="icon icon-shape bg-gradient-info shadow-info text-center border-radius-xl p-3 me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
              <i class="fas fa-chart-line text-white fa-lg"></i>
            </div>
            <div>
              <p class="text-xs text-uppercase font-weight-bold text-muted mb-0">RESOLUTION RATE</p>
              <h3 class="font-weight-bolder mb-0 text-dark"><?php echo $solvedRate; ?><span class="text-sm font-weight-bold">%</span></h3>
            </div>
          </div>
          <div class="border-top pt-2">
            <div class="progress mb-1" style="height: 6px; border-radius: 3px;">
              <div class="progress-bar bg-gradient-success" role="progressbar" style="width: <?php echo $solvedRate; ?>%;"></div>
              <div class="progress-bar bg-gradient-info" role="progressbar" style="width: <?php echo $inProgressRate; ?>%;"></div>
            </div>
            <span class="text-xxs text-muted font-weight-bold text-uppercase"><i class="fas fa-spinner text-info me-1"></i> <?php echo $inProgressCount; ?> ACTIVE IN-PROGRESS FRs</span>
          </div>
        </div>
      </div>
    </div>

    <!-- External Referral Card (SAINS/ISB) -->
    <div class="col-xl-3 col-md-6">
      <div class="card border-0 shadow-sm h-100" style="border-radius: 1rem; background: #ffffff;">
        <div class="card-body p-3">
          <div class="d-flex align-items-center mb-3">
            <div class="icon icon-shape bg-gradient-warning shadow-warning text-center border-radius-xl p-3 me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
              <i class="fas fa-share-square text-white fa-lg"></i>
            </div>
            <div>
              <p class="text-xs text-uppercase font-weight-bold text-muted mb-0">SAINS / ISB REFERRALS</p>
              <h3 class="font-weight-bolder mb-0 text-dark"><?php echo number_format($sainsRefTotal); ?></h3>
            </div>
          </div>
          <div class="border-top pt-2">
            <span class="text-xxs text-muted font-weight-bold text-uppercase"><i class="fas fa-external-link-alt text-warning me-1"></i> TICKETS REFERRED EXTERNALLY</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Active Users Card -->
    <div class="col-xl-3 col-md-6">
      <div class="card border-0 shadow-sm h-100" style="border-radius: 1rem; background: #ffffff;">
        <div class="card-body p-3">
          <div class="d-flex align-items-center mb-3">
            <div class="icon icon-shape bg-gradient-success shadow-success text-center border-radius-xl p-3 me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
              <i class="fas fa-users text-white fa-lg"></i>
            </div>
            <div>
              <p class="text-xs text-uppercase font-weight-bold text-muted mb-0">SYSTEM USERS</p>
              <h3 class="font-weight-bolder mb-0 text-dark"><?php echo number_format($totalStaff); ?></h3>
            </div>
          </div>
          <div class="border-top pt-2">
            <div class="d-flex justify-content-between text-xxs text-muted font-weight-bold text-uppercase">
              <span><i class="fas fa-check-circle text-success me-1"></i> ACTIVE: <?php echo number_format($activeStaff); ?></span>
              <span><i class="fas fa-minus-circle text-secondary me-1"></i> INACTIVE: <?php echo number_format($inactiveStaff); ?></span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Detailed Analytics Row -->
  <div class="row g-3 mb-4">
    <!-- Ticket Status Breakdown Table -->
    <div class="col-lg-7">
      <div class="card border-0 shadow-sm h-100" style="border-radius: 1rem; background: #ffffff;">
        <div class="card-header bg-transparent pb-0 p-3">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h6 class="mb-0 font-weight-bolder text-dark text-uppercase">TICKET STATUS & ASSIGNMENT SUMMARY</h6>
              <p class="text-xs text-muted mb-0 text-uppercase">ACTION STATUS BREAKDOWN FROM FRS DATABASE</p>
            </div>
            <span class="badge bg-light text-dark font-weight-bold px-3 py-2 border text-uppercase">LIVE STATUS</span>
          </div>
        </div>
        <div class="card-body p-3">
          <div class="table-responsive">
            <table class="table align-items-center mb-0">
              <thead>
                <tr>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">TICKET CATEGORY</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">TOTAL RECORDS</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">RATIO (%)</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">STATUS</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>
                    <div class="d-flex px-2 py-1 align-items-center">
                      <div class="icon icon-shape icon-xs me-3 bg-gradient-warning text-white border-radius-md d-flex align-items-center justify-content-center">
                        <i class="fas fa-clock"></i>
                      </div>
                      <div class="d-flex flex-column justify-content-center">
                        <h6 class="mb-0 text-sm font-weight-bold text-uppercase">UNASSIGNED</h6>
                        <span class="text-xxs text-muted text-uppercase">NEW REPORTS READY FOR ALLOCATION</span>
                      </div>
                    </div>
                  </td>
                  <td class="align-middle text-center text-sm">
                    <span class="badge bg-light text-dark font-weight-bolder px-3 py-2"><?php echo number_format($unassignedCount); ?></span>
                  </td>
                  <td class="align-middle text-center text-sm">
                    <span class="text-xs font-weight-bold text-muted"><?php echo $unassignedRate; ?>%</span>
                  </td>
                  <td class="align-middle">
                    <div class="progress-wrapper w-75 mx-auto">
                      <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-gradient-warning" role="progressbar" style="width: <?php echo $unassignedRate; ?>%;"></div>
                      </div>
                    </div>
                  </td>
                </tr>

                <tr>
                  <td>
                    <div class="d-flex px-2 py-1 align-items-center">
                      <div class="icon icon-shape icon-xs me-3 bg-gradient-info text-white border-radius-md d-flex align-items-center justify-content-center">
                        <i class="fas fa-spinner fa-spin"></i>
                      </div>
                      <div class="d-flex flex-column justify-content-center">
                        <h6 class="mb-0 text-sm font-weight-bold text-uppercase">IN PROGRESS</h6>
                        <span class="text-xxs text-muted text-uppercase">CURRENTLY BEING HANDLED BY ASSIGNED OFFICER</span>
                      </div>
                    </div>
                  </td>
                  <td class="align-middle text-center text-sm">
                    <span class="badge bg-light text-dark font-weight-bolder px-3 py-2"><?php echo number_format($inProgressCount); ?></span>
                  </td>
                  <td class="align-middle text-center text-sm">
                    <span class="text-xs font-weight-bold text-muted"><?php echo $inProgressRate; ?>%</span>
                  </td>
                  <td class="align-middle">
                    <div class="progress-wrapper w-75 mx-auto">
                      <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-gradient-info" role="progressbar" style="width: <?php echo $inProgressRate; ?>%;"></div>
                      </div>
                    </div>
                  </td>
                </tr>

                <tr>
                  <td>
                    <div class="d-flex px-2 py-1 align-items-center">
                      <div class="icon icon-shape icon-xs me-3 bg-gradient-success text-white border-radius-md d-flex align-items-center justify-content-center">
                        <i class="fas fa-check-double"></i>
                      </div>
                      <div class="d-flex flex-column justify-content-center">
                        <h6 class="mb-0 text-sm font-weight-bold text-uppercase">SOLVED / CLOSED</h6>
                        <span class="text-xxs text-muted text-uppercase">ACTION FULLY VERIFIED AND CLOSED</span>
                      </div>
                    </div>
                  </td>
                  <td class="align-middle text-center text-sm">
                    <span class="badge bg-light text-dark font-weight-bolder px-3 py-2"><?php echo number_format($solvedCount); ?></span>
                  </td>
                  <td class="align-middle text-center text-sm">
                    <span class="text-xs font-weight-bold text-muted"><?php echo $solvedRate; ?>%</span>
                  </td>
                  <td class="align-middle">
                    <div class="progress-wrapper w-75 mx-auto">
                      <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-gradient-success" role="progressbar" style="width: <?php echo $solvedRate; ?>%;"></div>
                      </div>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Category Analytics Card -->
    <div class="col-lg-5">
      <div class="card border-0 shadow-sm h-100" style="border-radius: 1rem; background: #ffffff;">
        <div class="card-header bg-transparent pb-0 p-3">
          <h6 class="mb-0 font-weight-bolder text-dark text-uppercase">FR CATEGORY ANALYTICS</h6>
          <p class="text-xs text-muted mb-0 text-uppercase">BREAKDOWN BY HARDWARE / SOFTWARE CATEGORY</p>
        </div>
        <div class="card-body p-3">
          <!-- Software Category Item -->
          <div class="card card-body border border-radius-lg p-3 mb-3 bg-gradient-light shadow-none">
            <div class="d-flex align-items-center justify-content-between">
              <div class="d-flex align-items-center">
                <div class="icon icon-shape icon-sm me-3 bg-gradient-primary text-white border-radius-md d-flex align-items-center justify-content-center">
                  <i class="fas fa-laptop-code"></i>
                </div>
                <div>
                  <h6 class="mb-0 text-sm font-weight-bolder text-uppercase">Software / Application</h6>
                  <span class="text-xxs text-muted text-uppercase">SOFTWARE & APPLICATION ISSUES</span>
                </div>
              </div>
              <h5 class="mb-0 font-weight-bolder text-primary"><?php echo number_format($softwareCount); ?></h5>
            </div>
          </div>

          <!-- Hardware Category Item -->
          <div class="card card-body border border-radius-lg p-3 mb-3 bg-gradient-light shadow-none">
            <div class="d-flex align-items-center justify-content-between">
              <div class="d-flex align-items-center">
                <div class="icon icon-shape icon-sm me-3 bg-gradient-warning text-white border-radius-md d-flex align-items-center justify-content-center">
                  <i class="fas fa-desktop"></i>
                </div>
                <div>
                  <h6 class="mb-0 text-sm font-weight-bolder text-uppercase">Hardware / Equipment</h6>
                  <span class="text-xxs text-muted text-uppercase">COMPUTER & HARDWARE FAULTS</span>
                </div>
              </div>
              <h5 class="mb-0 font-weight-bolder text-warning"><?php echo number_format($hardwareCount); ?></h5>
            </div>
          </div>

          <!-- Others Category Item -->
          <div class="card card-body border border-radius-lg p-3 bg-gradient-light shadow-none">
            <div class="d-flex align-items-center justify-content-between">
              <div class="d-flex align-items-center">
                <div class="icon icon-shape icon-sm me-3 bg-gradient-secondary text-white border-radius-md d-flex align-items-center justify-content-center">
                  <i class="fas fa-folder-open"></i>
                </div>
                <div>
                  <h6 class="mb-0 text-sm font-weight-bolder text-uppercase">Other Categories</h6>
                  <span class="text-xxs text-muted text-uppercase">NETWORK & GENERAL ISSUES</span>
                </div>
              </div>
              <h5 class="mb-0 font-weight-bolder text-secondary"><?php echo number_format($othersCount); ?></h5>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Additional Analytics Row: Recent FRs & Division Breakdown -->
  <div class="row g-3">
    <!-- Recent Fault Reports List -->
    <div class="col-lg-7">
      <div class="card border-0 shadow-sm h-100" style="border-radius: 1rem; background: #ffffff;">
        <div class="card-header bg-transparent pb-0 p-3">
          <h6 class="mb-0 font-weight-bolder text-dark text-uppercase"><i class="fas fa-history me-1 text-primary"></i> RECENT FAULT REPORTS</h6>
          <p class="text-xs text-muted mb-0 text-uppercase">LAST 5 FAULT REPORT RECORDS SUBMITTED TO SYSTEM</p>
        </div>
        <div class="card-body p-3">
          <?php if (!empty($recentFRs)) { ?>
            <div class="table-responsive">
              <table class="table align-items-center mb-0">
                <thead>
                  <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">FR NO.</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">REPORTER</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">DIVISION</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">CATEGORY</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">DATE</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($recentFRs as $fr) { ?>
                    <tr>
                      <td>
                        <span class="badge bg-gradient-dark font-weight-bold text-xs"><?php echo htmlspecialchars($fr['Frn']); ?></span>
                      </td>
                      <td>
                        <span class="text-xs font-weight-bold text-dark text-uppercase"><?php echo htmlspecialchars($fr['request_by'] ?? 'N/A'); ?></span>
                      </td>
                      <td>
                        <span class="text-xs text-muted font-weight-bold text-uppercase"><?php echo htmlspecialchars($fr['Oridiv'] ?? 'N/A'); ?></span>
                      </td>
                      <td>
                        <span class="badge bg-light text-primary border text-xxs text-uppercase"><?php echo htmlspecialchars($fr['frcate'] ?? 'General'); ?></span>
                      </td>
                      <td class="text-center">
                        <span class="text-xxs text-muted"><?php echo htmlspecialchars(substr($fr['date_add'] ?? '', 0, 10)); ?></span>
                      </td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          <?php } else { ?>
            <p class="text-xs text-muted text-center py-4 mb-0 text-uppercase">NO RECENT FAULT REPORTS FOUND.</p>
          <?php } ?>
        </div>
      </div>
    </div>

    <!-- Top Divisions by FR Count -->
    <div class="col-lg-5">
      <div class="card border-0 shadow-sm h-100" style="border-radius: 1rem; background: #ffffff;">
        <div class="card-header bg-transparent pb-0 p-3">
          <h6 class="mb-0 font-weight-bolder text-dark text-uppercase"><i class="fas fa-building me-1 text-info"></i> FAULT REPORTS BY DIVISION (TOP DIVISIONS)</h6>
          <p class="text-xs text-muted mb-0 text-uppercase">DIVISIONS WITH HIGHEST FAULT REPORT RECORDINGS</p>
        </div>
        <div class="card-body p-3">
          <?php if (!empty($divisionBreakdown)) { ?>
            <?php foreach ($divisionBreakdown as $div) { 
              $perc = $totalFR > 0 ? round(($div['total'] / $totalFR) * 100, 1) : 0;
            ?>
              <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <span class="text-xs font-weight-bold text-dark text-uppercase"><?php echo htmlspecialchars($div['Oridiv']); ?></span>
                  <span class="text-xs font-weight-bolder text-primary text-uppercase"><?php echo number_format($div['total']); ?> REPORTS (<?php echo $perc; ?>%)</span>
                </div>
                <div class="progress" style="height: 6px; border-radius: 3px;">
                  <div class="progress-bar bg-gradient-info" role="progressbar" style="width: <?php echo $perc; ?>%;"></div>
                </div>
              </div>
            <?php } ?>
          <?php } else { ?>
            <p class="text-xs text-muted text-center py-4 mb-0 text-uppercase">NO DIVISION RECORDS FOUND.</p>
          <?php } ?>
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