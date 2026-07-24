<!--
=========================================================
* Material Dashboard 2 - Fault Report Details (frDetail.php)
=========================================================
-->
<?php
session_start();
$page = 'fr_list';

require 'includes/connect.php';
require 'includes/line.php';
require 'includes/login_function.php';
require 'includes/controller/role_controller.php';

$frn = trim((string)($_GET['frn'] ?? ''));

if (empty($frn)) {
    header("Location: frList.php");
    exit();
}

$frData = null;
$assignData = [];
$actionData = null;
$referData = [];
$attachmentData = [];

if (isset($conn) && $conn instanceof mysqli) {
    $frnSafe = $conn->real_escape_string($frn);

    // 1. Fetch main FR info
    $resFR = $conn->query("SELECT * FROM `fr` WHERE Frn = '$frnSafe' LIMIT 1");
    if ($resFR && $resFR->num_rows > 0) {
        $frData = $resFR->fetch_assoc();

        // Mark FR as read for current user
        if (!isset($_SESSION['read_frs'])) {
            $_SESSION['read_frs'] = [];
        }
        if (!in_array($frnSafe, $_SESSION['read_frs'])) {
            $_SESSION['read_frs'][] = $frnSafe;
        }
    } else {
        // Access Denied / Record Not Found
        header("Location: frList.php?error=notfound");
        exit();
    }

    // Access Check: Ensure user division matches FR division unless HQ
    $currentDiv = $divName ?? '';
    $isHQ = (strtoupper(trim((string)$currentDiv)) === 'HEADQUARTERS');
    if (!$isHQ && !empty($currentDiv)) {
        if (stripos(($frData['Oridiv'] ?? ''), $currentDiv) === false) {
            header("Location: frList.php?error=unauthorized");
            exit();
        }
    }

    // 2. Fetch Assign History
    $resAssign = $conn->query("SELECT * FROM `assign` WHERE Assfrno = '$frnSafe' ORDER BY AssignId DESC");
    if ($resAssign) {
        while ($rowA = $resAssign->fetch_assoc()) {
            $assignData[] = $rowA;
        }
    }

    // 3. Fetch Action Record
    $resAction = $conn->query("SELECT * FROM `action` WHERE frno = '$frnSafe' LIMIT 1");
    if ($resAction && $resAction->num_rows > 0) {
        $actionData = $resAction->fetch_assoc();
    }

    // 4. Fetch Referral Records (SAINS / ISB)
    $resRefer = $conn->query("SELECT * FROM `refer_to` WHERE FrRefId = '$frnSafe' ORDER BY ReferId DESC");
    if ($resRefer) {
        while ($rowR = $resRefer->fetch_assoc()) {
            $referData[] = $rowR;
        }
    }

    // 5. Fetch Attachments (from recatt and upload)
    $resAtt1 = $conn->query("SELECT attname as filename, atttype as mime, attsize as filesize, fileatt_by as uploader FROM `recatt` WHERE attFrid = '$frnSafe'");
    if ($resAtt1) {
        while ($rowAtt1 = $resAtt1->fetch_assoc()) {
            $attachmentData[] = $rowAtt1;
        }
    }
    $resAtt2 = $conn->query("SELECT name as filename, type as mime, size as filesize, att_by as uploader FROM `upload` WHERE Frid = '$frnSafe'");
    if ($resAtt2) {
        while ($rowAtt2 = $resAtt2->fetch_assoc()) {
            $attachmentData[] = $rowAtt2;
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
  <title>FR DETAILS - <?php echo htmlspecialchars($frData['Frn']); ?></title>
  
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
      <!-- Top Action Bar -->
      <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="frList.php" class="btn btn-outline-dark btn-sm mb-0 text-uppercase"><i class="fas fa-arrow-left me-1"></i> BACK TO FR LIST</a>
        <div>
          <span class="badge bg-gradient-dark me-2 text-uppercase font-weight-bold px-3 py-2">FR NO: <?php echo htmlspecialchars($frData['Frn']); ?></span>
          <span class="badge <?php echo (strtoupper($frData['approval_status'] ?? '') === 'YES') ? 'bg-gradient-success' : 'bg-gradient-warning'; ?> text-uppercase font-weight-bold px-3 py-2">
            APPROVAL: <?php echo htmlspecialchars(strtoupper($frData['approval_status'] ?? 'NO')); ?>
          </span>
        </div>
      </div>

      <!-- Main Detail Header Banner -->
      <div class="card bg-gradient-dark border-0 shadow-lg mb-4" style="border-radius: 1rem; background: linear-gradient(135deg, #1e1e2f 0%, #0f1016 100%);">
        <div class="card-body p-4">
          <div class="row align-items-center">
            <div class="col-lg-8">
              <span class="badge bg-gradient-info text-uppercase px-3 py-1 mb-2 font-weight-bold"><?php echo htmlspecialchars($frData['frcate'] ?? 'GENERAL'); ?></span>
              <h3 class="text-white font-weight-bolder mb-1">FAULT REPORT <?php echo htmlspecialchars($frData['Frn']); ?></h3>
              <p class="text-white opacity-8 text-sm mb-0"><i class="fas fa-user me-1"></i> REQUESTED BY: <strong><?php echo htmlspecialchars($frData['request_by'] ?? 'N/A'); ?></strong> &bull; <i class="fas fa-building me-1"></i> <?php echo htmlspecialchars($frData['Oridiv'] ?? '-'); ?> (<?php echo htmlspecialchars($frData['Section'] ?? '-'); ?>)</p>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
              <span class="text-white text-xs opacity-6 d-block">DATE LODGED:</span>
              <h5 class="text-white font-weight-bold mb-0"><?php echo htmlspecialchars($frData['date_add'] ?? '-'); ?></h5>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-3 mb-4">
        <!-- Main Fault Report Specification Card -->
        <div class="col-lg-7">
          <div class="card border-0 shadow-sm h-100" style="border-radius: 1rem; background: #ffffff;">
            <div class="card-header bg-transparent pb-0 p-3">
              <h6 class="mb-0 font-weight-bolder text-dark text-uppercase"><i class="fas fa-info-circle me-2 text-primary"></i> REPORT INFORMATION & DESCRIPTION</h6>
            </div>
            <div class="card-body p-3">
              <div class="table-responsive">
                <table class="table table-sm align-items-center mb-3">
                  <tbody>
                    <tr>
                      <th class="text-xxs text-uppercase text-secondary font-weight-bolder w-30">SYSTEM / FR TYPE</th>
                      <td class="text-xs font-weight-bold text-dark text-uppercase"><?php echo htmlspecialchars($frData['frntype'] ?? '-'); ?></td>
                    </tr>
                    <tr>
                      <th class="text-xxs text-uppercase text-secondary font-weight-bolder">EQUIPMENT NAME</th>
                      <td class="text-xs font-weight-bold text-dark text-uppercase"><?php echo htmlspecialchars($frData['equip'] ?? '-'); ?></td>
                    </tr>
                    <tr>
                      <th class="text-xxs text-uppercase text-secondary font-weight-bolder">BRAND / SERIAL NO</th>
                      <td class="text-xs font-weight-bold text-dark text-uppercase"><?php echo htmlspecialchars($frData['brand'] ?? '-'); ?> / <?php echo htmlspecialchars($frData['srn'] ?? '-'); ?></td>
                    </tr>
                    <tr>
                      <th class="text-xxs text-uppercase text-secondary font-weight-bolder">HARDWARE SLA</th>
                      <td class="text-xs font-weight-bold text-dark text-uppercase"><?php echo htmlspecialchars($frData['HardSLA'] ?? '-'); ?></td>
                    </tr>
                    <tr>
                      <th class="text-xxs text-uppercase text-secondary font-weight-bolder">INCIDENT DATE & TIME</th>
                      <td class="text-xs font-weight-bold text-dark text-uppercase"><?php echo htmlspecialchars($frData['occurDate'] ?? '-'); ?> <?php echo htmlspecialchars($frData['timeoccur'] ?? ''); ?></td>
                    </tr>
                    <tr>
                      <th class="text-xxs text-uppercase text-secondary font-weight-bolder">APPROVER NAME</th>
                      <td class="text-xs font-weight-bold text-dark text-uppercase"><?php echo htmlspecialchars($frData['approved_by'] ?? '-'); ?> (<?php echo htmlspecialchars($frData['AppRej_date'] ?? ''); ?>)</td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <div class="border-top pt-3 mt-2">
                <h6 class="text-xs font-weight-bolder text-uppercase text-muted mb-2">FAULT DESCRIPTION:</h6>
                <div class="p-3 border-radius-lg bg-light text-dark text-sm font-weight-bold" style="line-height:1.6; white-space: pre-wrap;">
                  <?php echo htmlspecialchars($frData['Description'] ?? 'No description provided.'); ?>
                </div>
              </div>

              <?php if (!empty($frData['reject_reason'])) { ?>
                <div class="border-top pt-3 mt-3">
                  <h6 class="text-xs font-weight-bolder text-uppercase text-danger mb-2">REJECTION REASON:</h6>
                  <div class="p-3 border-radius-lg bg-gradient-light border border-danger text-danger text-sm font-weight-bold">
                    <?php echo htmlspecialchars($frData['reject_reason']); ?>
                  </div>
                </div>
              <?php } ?>

              <!-- Attachments UI Section -->
              <div class="border-top pt-3 mt-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <h6 class="text-xs font-weight-bolder text-uppercase text-dark mb-0"><i class="fas fa-paperclip me-2 text-primary"></i> ATTACHMENTS (<?php echo count($attachmentData); ?>)</h6>
                </div>

                <?php if (!empty($attachmentData)) { ?>
                  <div class="row g-2">
                    <?php foreach ($attachmentData as $att) { 
                      $fileName = htmlspecialchars($att['filename'] ?? 'file');
                      $fileMime = htmlspecialchars($att['mime'] ?? '');
                      $uploader = htmlspecialchars($att['uploader'] ?? 'User');
                      $fileSize = (int)($att['filesize'] ?? 0);
                      $formattedSize = $fileSize > 0 ? round($fileSize / 1024, 1) . ' KB' : 'N/A';

                      // Determine icon based on mime/extension
                      $fileIcon = 'fa-file-alt';
                      $iconColor = 'text-primary';
                      if (preg_match('/pdf/i', $fileMime) || preg_match('/\.pdf$/i', $fileName)) {
                          $fileIcon = 'fa-file-pdf';
                          $iconColor = 'text-danger';
                      } elseif (preg_match('/image/i', $fileMime) || preg_match('/\.(jpg|jpeg|png|gif)$/i', $fileName)) {
                          $fileIcon = 'fa-file-image';
                          $iconColor = 'text-info';
                      } elseif (preg_match('/word|doc/i', $fileMime) || preg_match('/\.(doc|docx)$/i', $fileName)) {
                          $fileIcon = 'fa-file-word';
                          $iconColor = 'text-primary';
                      }
                    ?>
                      <div class="col-md-6">
                        <div class="card card-body border border-radius-lg p-2 bg-gradient-light shadow-none">
                          <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                              <i class="fas <?php echo $fileIcon; ?> <?php echo $iconColor; ?> fa-2x me-3"></i>
                              <div class="text-truncate" style="max-width: 170px;">
                                <h6 class="mb-0 text-xs font-weight-bold text-dark text-truncate" title="<?php echo $fileName; ?>"><?php echo $fileName; ?></h6>
                                <span class="text-xxs text-muted d-block"><?php echo $formattedSize; ?> &bull; <?php echo $uploader; ?></span>
                              </div>
                            </div>
                            <div class="d-flex gap-1">
                              <button type="button" class="btn btn-sm btn-info mb-0 p-2 view-attachment-btn" data-url="uploads/<?php echo urlencode($fileName); ?>" data-filename="<?php echo $fileName; ?>" data-mime="<?php echo $fileMime; ?>" title="View Attachment">
                                <i class="fas fa-eye"></i> View
                              </button>
                              <a href="uploads/<?php echo urlencode($fileName); ?>" download class="btn btn-sm btn-outline-primary mb-0 p-2" title="Download Attachment">
                                <i class="fas fa-download"></i>
                              </a>
                            </div>
                          </div>
                        </div>
                      </div>
                    <?php } ?>
                  </div>
                <?php } else { ?>
                  <div class="p-3 border-radius-lg bg-light text-center">
                    <p class="text-xs text-muted mb-0"><i class="fas fa-info-circle me-1"></i> No file attachments associated with this Fault Report.</p>
                  </div>
                <?php } ?>
              </div>
            </div>
          </div>
        </div>

        <!-- Right Column: Action Status & Assignment -->
        <div class="col-lg-5">
          <!-- Action Record Card -->
          <div class="card border-0 shadow-sm mb-3" style="border-radius: 1rem; background: #ffffff;">
            <div class="card-header bg-transparent pb-0 p-3">
              <h6 class="mb-0 font-weight-bolder text-dark text-uppercase"><i class="fas fa-tools me-2 text-success"></i> ACTION & RESOLUTION STATUS</h6>
            </div>
            <div class="card-body p-3">
              <?php if ($actionData) { ?>
                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                  <span class="text-xs font-weight-bold text-uppercase text-muted">FR RESOLUTION STATUS:</span>
                  <span class="badge <?php echo (strtoupper($actionData['FR_status'] ?? '') === 'CLOSE') ? 'bg-gradient-success' : 'bg-gradient-warning'; ?> text-uppercase font-weight-bold">
                    <?php echo htmlspecialchars(strtoupper($actionData['FR_status'] ?? 'OPEN')); ?>
                  </span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                  <span class="text-xs font-weight-bold text-uppercase text-muted">ACTION OFFICER:</span>
                  <span class="text-xs font-weight-bolder text-dark text-uppercase"><?php echo htmlspecialchars($actionData['ActionTakenBy'] ?? '-'); ?></span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                  <span class="text-xs font-weight-bold text-uppercase text-muted">CAUSE OF PROBLEM:</span>
                  <span class="badge bg-light text-dark border text-xxs font-weight-bolder text-uppercase"><?php echo htmlspecialchars($actionData['causeprob'] ?? 'UNSPECIFIED'); ?></span>
                </div>
                <div class="mb-2">
                  <span class="text-xs font-weight-bold text-uppercase text-muted d-block mb-1">ACTION TAKEN DETAILS:</span>
                  <div class="p-2 border-radius-md bg-light text-xs font-weight-bold text-dark">
                    <?php echo htmlspecialchars($actionData['action_taken'] ?? 'No action details recorded yet.'); ?>
                  </div>
                </div>
              <?php } else { ?>
                <p class="text-xs text-muted text-center py-3 mb-0">No action record initiated for this Fault Report yet.</p>
              <?php } ?>
            </div>
          </div>

          <!-- Assignment Card -->
          <div class="card border-0 shadow-sm mb-3" style="border-radius: 1rem; background: #ffffff;">
            <div class="card-header bg-transparent pb-0 p-3">
              <h6 class="mb-0 font-weight-bolder text-dark text-uppercase"><i class="fas fa-user-check me-2 text-info"></i> ASSIGNMENT HISTORY</h6>
            </div>
            <div class="card-body p-3">
              <?php if (!empty($assignData)) { ?>
                <ul class="list-group list-group-flush">
                  <?php foreach ($assignData as $asg) { ?>
                    <li class="list-group-item px-0 py-2 border-0 d-flex justify-content-between align-items-center">
                      <div class="d-flex align-items-center">
                        <i class="fas fa-user-circle text-info me-2 fa-lg"></i>
                        <div>
                          <h6 class="mb-0 text-xs font-weight-bolder text-uppercase"><?php echo htmlspecialchars($asg['assign_to']); ?></h6>
                          <span class="text-xxs text-muted"><?php echo htmlspecialchars($asg['assign_date'] ?? ''); ?></span>
                        </div>
                      </div>
                      <span class="badge bg-light text-dark border text-xxs"><?php echo htmlspecialchars(strtoupper($asg['act_status'] ?? 'ASSIGNED')); ?></span>
                    </li>
                  <?php } ?>
                </ul>
              <?php } else { ?>
                <p class="text-xs text-muted text-center py-3 mb-0">This Fault Report has not been assigned to any officer yet.</p>
              <?php } ?>
            </div>
          </div>

          <!-- External Referral Card (SAINS / ISB) -->
          <?php if (!empty($referData)) { ?>
            <div class="card border-0 shadow-sm" style="border-radius: 1rem; background: #ffffff;">
              <div class="card-header bg-transparent pb-0 p-3">
                <h6 class="mb-0 font-weight-bolder text-dark text-uppercase"><i class="fas fa-external-link-alt me-2 text-warning"></i> EXTERNAL REFERRAL (SAINS / ISB)</h6>
              </div>
              <div class="card-body p-3">
                <ul class="list-group list-group-flush">
                  <?php foreach ($referData as $ref) { ?>
                    <li class="list-group-item px-0 py-2 border-0">
                      <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-gradient-warning text-xxs font-weight-bold text-uppercase"><?php echo htmlspecialchars($ref['Refcate']); ?></span>
                        <span class="text-xxs text-muted"><?php echo htmlspecialchars($ref['DateRef']); ?></span>
                      </div>
                      <p class="text-xs font-weight-bold text-dark mt-1 mb-0">REFERRED TO: <?php echo htmlspecialchars($ref['RefWho']); ?> (DOCKET: <?php echo htmlspecialchars($ref['SainsDocNo'] ?? '-'); ?>)</p>
                    </li>
                  <?php } ?>
                </ul>
              </div>
            </div>
          <?php } ?>
        </div>
      </div>

      <?php
      if (file_exists("footer.php")) {
          include("footer.php");
      }
      ?>
    </div>
  </main>

  <!-- Attachment Preview Modal -->
  <div class="modal fade" id="attachmentModal" tabindex="-1" aria-labelledby="attachmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
      <div class="modal-content" style="border-radius: 1rem;">
        <div class="modal-header bg-gradient-dark text-white p-3" style="border-radius: 1rem 1rem 0 0;">
          <h6 class="modal-title text-white font-weight-bolder text-uppercase mb-0" id="attachmentModalLabel"><i class="fas fa-file-alt me-2"></i> PREVIEW ATTACHMENT</h6>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-0 text-center bg-dark" style="min-height: 500px; display: flex; align-items: center; justify-content: center;" id="attachmentModalBody">
          <!-- Dynamic Content Loaded Here -->
        </div>
        <div class="modal-footer bg-light p-2" style="border-radius: 0 0 1rem 1rem;">
          <a href="#" id="attachmentModalDownload" download class="btn btn-sm btn-primary mb-0"><i class="fas fa-download me-1"></i> DOWNLOAD FILE</a>
          <button type="button" class="btn btn-sm btn-secondary mb-0" data-bs-dismiss="modal">CLOSE</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Core JS -->
  <script src="./assets/js/core/popper.min.js"></script>
  <script src="./assets/js/core/bootstrap.min.js"></script>
  <script src="./assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="./assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script src="./assets/js/jquery.min.js"></script>
  <script src="./assets/js/material-dashboard.min.js?v=3.0.0"></script>

  <script>
    $(document).ready(function() {
      $(document).on('click', '.view-attachment-btn', function() {
        var fileUrl = $(this).data('url');
        var fileName = $(this).data('filename');
        var fileMime = $(this).data('mime') || '';
        
        $('#attachmentModalLabel').html('<i class="fas fa-eye me-2"></i> PREVIEW: ' + fileName);
        $('#attachmentModalDownload').attr('href', fileUrl);

        var bodyHtml = '';

        // PDF Preview
        if (fileMime.indexOf('pdf') !== -1 || fileName.toLowerCase().endsWith('.pdf')) {
          bodyHtml = '<iframe src="' + fileUrl + '" style="width:100%; height:600px; border:none;"></iframe>';
        }
        // Image Preview
        else if (fileMime.indexOf('image') !== -1 || fileName.match(/\.(jpg|jpeg|png|gif|webp)$/i)) {
          bodyHtml = '<img src="' + fileUrl + '" class="img-fluid p-2" style="max-height:600px; object-fit:contain;" alt="' + fileName + '">';
        }
        // Text / Code Preview
        else if (fileMime.indexOf('text') !== -1 || fileName.match(/\.(txt|log|json|xml|html)$/i)) {
          bodyHtml = '<iframe src="' + fileUrl + '" style="width:100%; height:500px; border:none; background:#ffffff;"></iframe>';
        }
        // Fallback for Word/Excel/other binary files
        else {
          bodyHtml = '<div class="text-white p-5"><i class="fas fa-file-download fa-4x mb-3 text-warning"></i><p class="mb-2 text-sm font-weight-bold">File type preview not directly supported in browser.</p><a href="' + fileUrl + '" target="_blank" class="btn btn-info btn-sm">Open File in New Tab</a></div>';
        }

        $('#attachmentModalBody').html(bodyHtml);
        var attModal = new bootstrap.Modal(document.getElementById('attachmentModal'));
        attModal.show();
      });
    });
  </script>
</body>

</html>
