<?php
$username = 'Pengguna';
$fullname = 'Pengguna';
$userRole = $userRole ?? 'NU';

if (isset($conn) && $conn instanceof mysqli && !empty($_SESSION['uid'])) {
    $uid_safe = $conn->real_escape_string($_SESSION['uid']);
    $sqluser = "SELECT * FROM `user` WHERE uid = '" . $uid_safe . "' OR username = '" . $uid_safe . "' LIMIT 1";
    $resuser = $conn->query($sqluser);
    if ($resuser && $rowuser = $resuser->fetch_assoc()) {
        $fullname = $rowuser["name"] ?? $rowuser["username"] ?? 'Pengguna';
        $userRole = strtoupper(trim((string)($rowuser["Role"] ?? 'NU')));
        $parts = explode(' ', $fullname);
        if (count($parts) >= 2) {
            $username = $parts[0] . ' ' . $parts[1];
        } else {
            $username = $fullname;
        }
    }
}

// Format readable role label for display
$roleLabelDisplay = 'Normal User';
if ($userRole === 'SPV') {
    $roleLabelDisplay = 'Supervisor';
} elseif ($userRole === 'SE') {
    $roleLabelDisplay = 'System Executive';
} elseif ($userRole === 'FP') {
    $roleLabelDisplay = 'Focal Person';
}
?>

<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 bg-gradient-dark bg-dark" id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer opacity-5 position-absolute end-0 top-0 d-none d-xl-none text-white" aria-hidden="true" id="iconSidenav"></i>
        <a class="m-0" href="index">
            <img src="./assets/img/logo-white.svg" class="w-100 mt-3" style="height:40px" alt="main_logo">
        </a>
    </div>
    <hr class="horizontal light mt-0 mb-2">
    
    <div class="collapse navbar-collapse w-auto h-auto" id="sidenav-collapse-main">
        <ul class="navbar-nav">
            <!-- User Profile Section -->
            <li class="nav-item mb-2 mt-0">
                <a data-bs-toggle="collapse" href="#ProfileNav" class="nav-link text-white" aria-controls="ProfileNav" role="button" aria-expanded="false">
                    <img src="./assets/img/user.png" class="avatar me-2">
                    <div class="d-flex flex-column">
                        <span class="nav-link-text font-weight-bolder text-sm mb-0"><?php echo htmlspecialchars($username); ?></span>
                        <span class="text-xxs text-info font-weight-bold text-uppercase"><?php echo htmlspecialchars($roleLabelDisplay); ?></span>
                    </div>
                </a>
                <div class="collapse" id="ProfileNav">
                    <ul class="nav ms-4 ps-2">
                        <li class="nav-item">
                            <a class="nav-link text-white" href="accProfile.php">
                                <i class="fa fa-user me-2"></i>
                                <span class="sidenav-normal text-xs"> My Profile </span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="includes/logout.php">
                                <i class="fa fa-right-from-bracket me-2"></i>
                                <span class="sidenav-normal text-xs"> Logout </span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <hr class="horizontal light mt-0 mb-2">

            <!-- General Navigation -->
            <li class="nav-item">
                <a class="nav-link <?php if (($page ?? '') == 'index') { echo 'active bg-gradient-primary'; } ?>" href="index">
                    <i class="fa fa-cube"></i>
                    <span class="nav-link-text ms-2 ps-1 font-weight-bold">Division Dashboard</span>
                </a>
            </li>

            <?php
            // Calculate Focal Person (FP) unassigned new FR badge count
            $fpUnassignedBadgeCount = 0;
            if (isset($conn) && $conn instanceof mysqli && ($userRole ?? '') === 'FP') {
                $userDivForBadge = $conn->real_escape_string($divName ?? 'Headquarters');
                $sqlFpBadge = "SELECT COUNT(Frn) as total FROM `fr` 
                               WHERE (Oridiv = '$userDivForBadge' OR Oridiv LIKE '%$userDivForBadge%') 
                               AND Frn NOT IN (SELECT Assfrno FROM `assign`)";
                $resFpBadge = $conn->query($sqlFpBadge);
                if ($resFpBadge && $rowFpBadge = $resFpBadge->fetch_assoc()) {
                    $fpUnassignedBadgeCount = (int)$rowFpBadge['total'];
                }
            }
            ?>
            <li class="nav-item">
                <a class="nav-link <?php if (($page ?? '') == 'my_dashboard') { echo 'active bg-gradient-primary'; } else { echo 'text-white'; } ?>" href="myDashboard.php">
                    <i class="fa fa-user-circle me-2"></i>
                    <span class="nav-link-text font-weight-bold">My Dashboard</span>
                    <?php if ($fpUnassignedBadgeCount > 0) { ?>
                        <span class="badge bg-gradient-danger text-xxs ms-auto"><?php echo $fpUnassignedBadgeCount; ?> NEW</span>
                    <?php } ?>
                </a>
            </li>

            <!-- Menu Based on Role: SPV (Supervisor) & SE (Support Engineer) & FP (Focal Person) & NU (Normal User) -->
            
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder text-primary">Fault Reports</h6>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php if (($page ?? '') == 'fr_list') { echo 'active bg-gradient-primary'; } else { echo 'text-white'; } ?>" href="frList.php">
                    <i class="fa fa-list me-2"></i>
                    <span class="nav-link-text font-weight-bold">FR List</span>
                </a>
            </li>

            <!-- Data Entry / Lodge Report (For NU, FP, SPV) -->
            <li class="nav-item">
                <a class="nav-link text-white" href="dataEntry.php">
                    <i class="fas fa-pen-to-square me-2"></i>
                    <span class="nav-link-text font-weight-bold">Lodge Report</span>
                </a>
            </li>

            <!-- Action Taken Module -->
            <li class="nav-item">
                <a class="nav-link <?php if (($page ?? '') == 'action_mgmt') { echo 'active bg-gradient-primary'; } else { echo 'text-white'; } ?>" href="actionManagement.php">
                    <i class="fas fa-tasks me-2"></i>
                    <span class="nav-link-text font-weight-bold">Action Taken</span>
                </a>
            </li>

            <!-- New Assign Module -->
            <?php
            $unreadAssignCount = 0;
            if (isset($conn) && $conn instanceof mysqli) {
                $myUserFull = trim((string)($fullname ?? ''));
                $myUserUid = trim((string)($_SESSION['uid'] ?? $_SESSION['user'] ?? $_SESSION['username'] ?? ''));

                // Exact match check only if valid user name exists
                if (!empty($myUserUid) || (!empty($myUserFull) && $myUserFull !== 'Pengguna' && $myUserFull !== 'User')) {
                    $myFullEsc = $conn->real_escape_string($myUserFull);
                    $myUidEsc = $conn->real_escape_string($myUserUid);

                    $userMatchConditions = [];
                    if (!empty($myFullEsc) && $myUserFull !== 'Pengguna' && $myUserFull !== 'User') {
                        $userMatchConditions[] = "assign_to = '$myFullEsc'";
                    }
                    if (!empty($myUidEsc)) {
                        $userMatchConditions[] = "assign_to = '$myUidEsc'";
                    }

                    if (!empty($userMatchConditions)) {
                        $sqlUnread = "SELECT DISTINCT Assfrno FROM `assign` WHERE (" . implode(" OR ", $userMatchConditions) . ")";
                        $resUnread = $conn->query($sqlUnread);
                        if ($resUnread) {
                            $readArray = $_SESSION['read_frs'] ?? [];
                            while ($rRow = $resUnread->fetch_assoc()) {
                                $fNum = $rRow['Assfrno'];
                                if (!empty($fNum) && !in_array($fNum, $readArray)) {
                                    $unreadAssignCount++;
                                }
                            }
                        }
                    }
                }
            }
            ?>
            <li class="nav-item">
                <a class="nav-link <?php if (($page ?? '') == 'assign_mgmt') { echo 'active bg-gradient-primary'; } else { echo 'text-white'; } ?>" href="assignManagement.php">
                    <i class="fas fa-user-plus me-2"></i>
                    <span class="nav-link-text font-weight-bold">New Assign</span>
                    <?php if (isset($unreadAssignCount) && (int)$unreadAssignCount > 0) { ?>
                        <span class="badge bg-gradient-danger text-xxs ms-auto"><?php echo (int)$unreadAssignCount; ?> NEW</span>
                    <?php } ?>
                </a>
            </li>

            <!-- System Administration Menu (HQ Division or SPV / Pentadbir only) -->
            <?php if (($divName ?? '') === 'Headquarters' || $userRole === 'SPV') { ?>
                <li class="nav-item mt-3">
                    <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder text-primary">System Administration</h6>
                </li>

                <?php if (($divName ?? '') === 'Headquarters') { ?>
                    <li class="nav-item">
                        <a class="nav-link <?php if (($page ?? '') == 'sys_acc') { echo 'active bg-gradient-primary'; } ?>" href="accManagement.php">
                            <i class="fa fa-user-cog me-2"></i>
                            <span class="nav-link-text font-weight-bold">User Management</span>
                        </a>
                    </li>
                <?php } ?>
            <?php } ?>

            <!-- Reporting Menu -->
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder text-primary">Reporting</h6>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="reports.php">
                    <i class="fa fa-file-invoice me-2"></i>
                    <span class="nav-link-text font-weight-bold">Reports</span>
                </a>
            </li>

            <li class="nav-item mt-3">
                <a class="nav-link text-white" href="includes/logout.php">
                    <i class="fa fa-right-from-bracket me-2 text-danger"></i>
                    <span class="nav-link-text font-weight-bold text-danger">Logout</span>
                </a>
            </li>
        </ul>
    </div>
</aside>