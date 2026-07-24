<div class="container-fluid py-4">
  <?php
  // Year filter selection for division dashboard
  $selectedDivYear = trim((string)($_GET['year'] ?? date("Y")));

  $divYearWhereFR = "";
  $divYearWhereAction = "";

  if ($selectedDivYear !== 'all' && !empty($selectedDivYear)) {
      $yVal = (int)$selectedDivYear;
      $divYearWhereFR = " AND YEAR(date_add) = $yVal ";
      $divYearWhereAction = " AND YEAR(f.date_add) = $yVal ";
  }

  // Initialize counts for division dashboard
  $currentDivName = !empty($divName) ? $divName : 'Bahagian';
  
  $totalDivFR = 0;
  $newDivCount = 0;
  $inProgressDivCount = 0;
  $solvedDivCount = 0;
  $softwareDivCount = 0;
  $hardwareDivCount = 0;
  $othersDivCount = 0;
  $recentDivFRs = [];
  $divYearsList = [];

  if (isset($conn) && $conn instanceof mysqli) {
      $divSafe = $conn->real_escape_string($currentDivName);

      // Get list of distinct years
      $resY = $conn->query("SELECT DISTINCT YEAR(date_add) as yr FROM `fr` WHERE date_add IS NOT NULL AND YEAR(date_add) > 2000 ORDER BY yr DESC");
      if ($resY) {
          while ($yRow = $resY->fetch_assoc()) {
              $divYearsList[] = $yRow['yr'];
          }
      }

      // 1. Total FR for this division
      $res = $conn->query("SELECT COUNT(*) AS total FROM `fr` WHERE (Oridiv = '$divSafe' OR Oridiv LIKE '%$divSafe%') " . $divYearWhereFR);
      if ($res && $row = $res->fetch_assoc()) {
          $totalDivFR = (int)$row['total'];
      }

      // 2. Solved FR for this division
      $res = $conn->query("SELECT COUNT(*) AS total FROM `fr` f JOIN `action` a ON a.frno = f.Frn WHERE (f.Oridiv = '$divSafe' OR f.Oridiv LIKE '%$divSafe%') AND (a.FR_status = 'Close' OR a.action_status = 'Done') " . $divYearWhereAction);
      if ($res && $row = $res->fetch_assoc()) {
          $solvedDivCount = (int)$row['total'];
      }

      // 3. In Progress FR for this division
      $res = $conn->query("SELECT COUNT(*) AS total FROM `fr` f JOIN `action` a ON a.frno = f.Frn WHERE (f.Oridiv = '$divSafe' OR f.Oridiv LIKE '%$divSafe%') AND a.FR_status = 'Open' AND a.action_status != 'Done' " . $divYearWhereAction);
      if ($res && $row = $res->fetch_assoc()) {
          $inProgressDivCount = (int)$row['total'];
      }

      // 4. New / Unassigned FR for this division
      $res = $conn->query("SELECT COUNT(*) AS total FROM `fr` WHERE (Oridiv = '$divSafe' OR Oridiv LIKE '%$divSafe%') AND Frn NOT IN (SELECT Assfrno FROM `assign`) " . $divYearWhereFR);
      if ($res && $row = $res->fetch_assoc()) {
          $newDivCount = (int)$row['total'];
      }

      // 5. Division Categories
      $res = $conn->query("SELECT COUNT(*) AS total FROM `fr` WHERE (Oridiv = '$divSafe' OR Oridiv LIKE '%$divSafe%') AND (frcate LIKE '%Software%' OR frcate LIKE '%Application%') " . $divYearWhereFR);
      if ($res && $row = $res->fetch_assoc()) {
          $softwareDivCount = (int)$row['total'];
      }

      $res = $conn->query("SELECT COUNT(*) AS total FROM `fr` WHERE (Oridiv = '$divSafe' OR Oridiv LIKE '%$divSafe%') AND frcate LIKE '%Hardware%' " . $divYearWhereFR);
      if ($res && $row = $res->fetch_assoc()) {
          $hardwareDivCount = (int)$row['total'];
      }

      $othersDivCount = max(0, $totalDivFR - ($softwareDivCount + $hardwareDivCount));

      // 6. Recent 5 Fault Reports for this division
      $resRecent = $conn->query("SELECT Frn, frcate, request_by, Section, date_add FROM `fr` WHERE (Oridiv = '$divSafe' OR Oridiv LIKE '%$divSafe%') " . $divYearWhereFR . " ORDER BY date_add DESC LIMIT 5");
      if ($resRecent) {
          while ($rRow = $resRecent->fetch_assoc()) {
              $recentDivFRs[] = $rRow;
          }
      }
  }

  if (empty($divYearsList)) {
      $divYearsList = [date("Y"), date("Y") - 1, date("Y") - 2];
  }

  $solvedRate = $totalDivFR > 0 ? round(($solvedDivCount / $totalDivFR) * 100, 1) : 0;
  $inProgressRate = $totalDivFR > 0 ? round(($inProgressDivCount / $totalDivFR) * 100, 1) : 0;
  $newRate = $totalDivFR > 0 ? round(($newDivCount / $totalDivFR) * 100, 1) : 0;
  ?>

  <div class="row mb-4">
    <div class="col-12">
      <div class="card bg-gradient-dark border-0 shadow-lg position-relative overflow-hidden p-3" style="border-radius: 1rem; background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
        <div class="row align-items-center">
          <div class="col-lg-7 col-md-6">
            <span class="badge bg-white text-dark mb-2 text-uppercase tracking-wider px-3 py-2 font-weight-bolder">DIVISION DASHBOARD: <?php echo htmlspecialchars($currentDivName); ?></span>
            <h2 class="text-white font-weight-bolder mb-1">Fault Report Overview</h2>
            <p class="text-white opacity-9 text-sm mb-0 text-uppercase">MONITORING FAULT REPORTS AND ACTION PROGRESS SPECIFICALLY FOR <?php echo htmlspecialchars($currentDivName); ?> DIVISION.</p>
          </div>
          <div class="col-lg-5 col-md-6 text-end">
            <!-- Year Filter Form -->
            <form method="GET" action="index.php" class="d-inline-flex align-items-center justify-content-end gap-2 mb-2">
              <label class="text-white text-xs font-weight-bold mb-0 text-uppercase"><i class="fas fa-calendar-alt me-1"></i> YEAR:</label>
              <select name="year" class="form-select form-select-sm border px-2 py-1 text-uppercase font-weight-bold" style="border-radius: 0.5rem; background:#ffffff; width: auto;" onchange="this.form.submit()">
                <option value="all" <?php echo ($selectedDivYear === 'all') ? 'selected' : ''; ?>>ALL YEARS</option>
                <?php foreach ($divYearsList as $yOpt) { ?>
                  <option value="<?php echo $yOpt; ?>" <?php echo ((string)$yOpt === (string)$selectedDivYear) ? 'selected' : ''; ?>>
                    <?php echo $yOpt; ?>
                  </option>
                <?php } ?>
              </select>
            </form>
            <div>
              <a href="frList.php" class="btn btn-sm btn-light text-dark mb-0 me-2 text-uppercase"><i class="fas fa-list me-1"></i> FR LIST</a>
              <a href="index.php" class="btn btn-sm btn-outline-white mb-0 text-uppercase"><i class="fas fa-sync-alt me-1"></i> REFRESH</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Metric KPI Cards -->
  <div class="row g-3 mb-4">
    <!-- Total Division FR -->
    <div class="col-xl-3 col-md-6">
      <div class="card border-0 shadow-sm h-100" style="border-radius: 1rem; background: #ffffff;">
        <div class="card-body p-3">
          <div class="d-flex align-items-center mb-3">
            <div class="icon icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl p-3 me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
              <i class="fas fa-exclamation-triangle text-white fa-lg"></i>
            </div>
            <div>
              <p class="text-xs text-uppercase font-weight-bold text-muted mb-0">TOTAL DIVISION FRs</p>
              <h3 class="font-weight-bolder mb-0 text-dark"><?php echo number_format($totalDivFR); ?></h3>
            </div>
          </div>
          <div class="border-top pt-2">
            <div class="d-flex justify-content-between text-xxs text-muted font-weight-bold text-uppercase">
              <span><i class="fas fa-plus-circle text-warning me-1"></i> NEW: <?php echo $newDivCount; ?></span>
              <span><i class="fas fa-check-circle text-success me-1"></i> SOLVED: <?php echo $solvedDivCount; ?></span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Resolution Rate -->
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
            <span class="text-xxs text-muted font-weight-bold text-uppercase"><i class="fas fa-spinner text-info me-1"></i> <?php echo $inProgressDivCount; ?> ACTIVE IN-PROGRESS FRs</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Software Category -->
    <div class="col-xl-3 col-md-6">
      <div class="card border-0 shadow-sm h-100" style="border-radius: 1rem; background: #ffffff;">
        <div class="card-body p-3">
          <div class="d-flex align-items-center mb-3">
            <div class="icon icon-shape bg-gradient-warning shadow-warning text-center border-radius-xl p-3 me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
              <i class="fas fa-laptop-code text-white fa-lg"></i>
            </div>
            <div>
              <p class="text-xs text-uppercase font-weight-bold text-muted mb-0">SOFTWARE / APP</p>
              <h3 class="font-weight-bolder mb-0 text-dark"><?php echo number_format($softwareDivCount); ?></h3>
            </div>
          </div>
          <div class="border-top pt-2">
            <span class="text-xxs text-muted font-weight-bold text-uppercase"><i class="fas fa-desktop text-warning me-1"></i> HARDWARE: <?php echo number_format($hardwareDivCount); ?></span>
          </div>
        </div>
      </div>
    </div>

    <!-- Others Category -->
    <div class="col-xl-3 col-md-6">
      <div class="card border-0 shadow-sm h-100" style="border-radius: 1rem; background: #ffffff;">
        <div class="card-body p-3">
          <div class="d-flex align-items-center mb-3">
            <div class="icon icon-shape bg-gradient-secondary shadow-secondary text-center border-radius-xl p-3 me-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
              <i class="fas fa-folder-open text-white fa-lg"></i>
            </div>
            <div>
              <p class="text-xs text-uppercase font-weight-bold text-muted mb-0">OTHER CATEGORIES</p>
              <h3 class="font-weight-bolder mb-0 text-dark"><?php echo number_format($othersDivCount); ?></h3>
            </div>
          </div>
          <div class="border-top pt-2">
            <span class="text-xxs text-muted font-weight-bold text-uppercase"><i class="fas fa-info-circle me-1"></i> NETWORK & GENERAL</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Table Recent FRs for Division -->
  <div class="row g-3">
    <div class="col-12">
      <div class="card border-0 shadow-sm" style="border-radius: 1rem; background: #ffffff;">
        <div class="card-header bg-transparent pb-0 p-3">
          <h6 class="mb-0 font-weight-bolder text-dark text-uppercase"><i class="fas fa-history me-1 text-primary"></i> RECENT DIVISION FAULT REPORTS</h6>
          <p class="text-xs text-muted mb-0 text-uppercase">LAST 5 FAULT REPORTS SUBMITTED BY <?php echo htmlspecialchars($currentDivName); ?> DIVISION</p>
        </div>
        <div class="card-body p-3">
          <?php if (!empty($recentDivFRs)) { ?>
            <div class="table-responsive">
              <table class="table align-items-center mb-0">
                <thead>
                  <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">FR NO.</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">REPORTER</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">SECTION</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">CATEGORY</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">DATE</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($recentDivFRs as $fr) { ?>
                    <tr>
                      <td>
                        <span class="badge bg-gradient-dark font-weight-bold text-xs"><?php echo htmlspecialchars($fr['Frn']); ?></span>
                      </td>
                      <td>
                        <span class="text-xs font-weight-bold text-dark text-uppercase"><?php echo htmlspecialchars($fr['request_by'] ?? 'N/A'); ?></span>
                      </td>
                      <td>
                        <span class="text-xs text-muted font-weight-bold text-uppercase"><?php echo htmlspecialchars($fr['Section'] ?? 'N/A'); ?></span>
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
            <p class="text-xs text-muted text-center py-4 mb-0 text-uppercase">NO RECENT FAULT REPORTS FOUND FOR THIS DIVISION.</p>
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
