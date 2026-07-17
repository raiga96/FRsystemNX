<?php
$sqluser = "SELECT * FROM userinfo WHERE uid = '" . $_SESSION['uid'] . "' ";
$resuser = $conn1->query($sqluser);
$rowuser = $resuser->fetch_array();
$username = $rowuser["fullname"];
$fullname = $rowuser["fullname"];

$parts = explode(' ', $fullname);
if (count($parts) >= 2) {
    $username = $parts[0] . ' ' . $parts[1];
} else {
    $username = $fullname; // fallback if not enough parts
}

?>
<?php if ($section == 'ISB') { ?>
    <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 <?php if ($divName == 'Headquarters') { ?>  bg-gradient-dark <?php } else { ?> bg-gradient-dark <?php } ?> bg-dark " id="sidenav-main">
        <div class="sidenav-header">
            <i class="fas fa-times p-3 cursor-pointer opacity-5 position-absolute end-0 top-0 d-none d-xl-none text-white" aria-hidden="true" id="iconSidenav"></i>
            <a class="m-0" href="index" target="_blank">
                <img src="./assets/img/logo-white.svg" class="w-100 mt-3" style="height:40px" alt="main_logo">
            </a>
        </div>
        <hr class="horizontal light mt-0 mb-2">
        <div class="collapse navbar-collapse  w-auto h-auto" id="sidenav-collapse-main">
            <ul class="navbar-nav">
                <li class="nav-item mb-2 mt-0">
                    <a data-bs-toggle="collapse" href="#ProfileNav" class="nav-link text-white" aria-controls="ProfileNav" role="button" aria-expanded="false">
                        <img src="./assets/img/user.png" class="avatar">
                        <span class="nav-link-text ms-2 ps-1 font-weight-bolder"><?php echo $username ?></span>
                    </a>
                    <div class="collapse" id="ProfileNav">
                        <ul class="nav ">
                            <li class="nav-item">
                                <a class="nav-link text-white" href="../../pages/pages/profile/overview.html">
                                    <i class="fa fa-outdent"></i>
                                    <span class="sidenav-normal  ms-3  ps-1"><?php echo $section ?> </span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white" href="../../pages/pages/profile/overview.html">
                                    <!-- <span class="sidenav-mini-icon"> MP </span> -->
                                    <i class="fa fa-user"></i>
                                    <span class="sidenav-normal  ms-3  ps-1"> My Profile </span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white" href="../../pages/pages/account/settings.html">
                                    <i class="fa fa-gear"></i>
                                    <span class="sidenav-normal  ms-3  ps-1"> Settings </span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white" href="includes/logout.php">
                                    <i class="fa fa-right-from-bracket"></i>
                                    <span class="sidenav-normal  ms-3  ps-1"> Logout </span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <hr class="horizontal mt-0 dark">
                <li class="nav-item">
                    <a class="nav-link <?php if (($page == 'index') && ($divName == 'Headquarters')) { ?> active bg-gradient-primary <?php } elseif (($page == 'index') && ($divName != 'Headquarters')) { ?> active bg-gradient-primary <?php } ?>" href="index">
                        <i class="fa fa-cube"></i>
                        <span class="nav-link-text ms-2 ps-1 font-weight-bold">Dashboards</span>
                    </a>
                </li>
                <li class="nav-item mt-3">
                    <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder text-primary">Fault Reports</h6>
                </li>

                <li class="nav-item">
                    <a data-bs-toggle="collapse" href="#dashboardsExamples1" class="nav-link text-white" aria-controls="dashboardsExamples1" role="button" aria-expanded="false">
                        <i class="fa fa-pen-to-square"></i>
                        <span class="nav-link-text ms-2 ps-1 font-weight-bold">Data Entry</span>
                    </a>
                    <div class="collapse <?php if ($page == 'data_entry') { ?> show <?php } ?>" id="dashboardsExamples1">
                        <ul class="nav ">
                            <li class="nav-item">
                                <a class="nav-link text-white <?php if (($page == 'data_entry') && ($subpage == 'data_entry') && ($divName == 'Headquarters')) { ?> active bg-gradient-primary <?php } elseif (($page == 'data_entry') && ($subpage == 'data_entry') && ($divName != 'Headquarters')) { ?> active bg-gradient-warning <?php } ?>" href="dataEntry.php?form=none">
                                    <i class="fas fa-keyboard"></i>
                                    <span class="sidenav-normal  ms-2  ps-1 font-weight-bold"> Lodge Report</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white <?php if (($page == 'data_entry') && ($subpage == 'saved') && ($divName == 'Headquarters')) { ?> active bg-gradient-primary <?php } elseif (($page == 'data_entry') && ($subpage == 'saved') && ($divName != 'Headquarters')) { ?> active bg-gradient-warning <?php } ?>" href="savedProject.php">
                                    <i class="fas fa-paper-plane"></i>
                                    <span class="sidenav-normal  ms-2  ps-1 font-weight-bold"> Assign FR</span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>

                <li class="nav-item mt-3">
                    <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder text-primary">System Administration</h6>
                </li>
                <li class="nav-item">
                    <a class="nav-link " href="accManagement">
                        <i class="fa fa-list"></i>
                        <span class="nav-link-text ms-2 ps-1 font-weight-bold">FR List</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link <?php if (($page == 'sys_acc') && ($divName == 'Headquarters')) { ?> active bg-gradient-primary <?php } elseif (($page == 'sys_acc') && ($divName != 'Headquarters')) { ?> active bg-gradient-primary <?php } ?>" href="accManagement">
                        <i class="fa fa-users"></i>
                        <span class="nav-link-text ms-2 ps-1 font-weight-bold">Account Management</span>
                    </a>
                </li>

                <li class="nav-item mt-3">
                    <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder text-primary">Reporting</h6>
                </li>

                <li class="nav-item">
                    <a class="nav-link " href="accManagement">
                        <i class="material-icons-round {% if page.brand == 'RTL' %}ms-2{% else %} me-2{% endif %}">receipt_long</i>
                        <span class="nav-link-text ms-2 ps-1 font-weight-bold">Reports</span>
                    </a>
                </li>
               

            </ul>
        </div>
    </aside>
<?php } else if ($section == 'Survey ') { ?>
    <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 <?php if ($divName == 'Headquarters') { ?>  bg-gradient-dark <?php } else { ?> bg-gradient-dark <?php } ?> bg-dark " id="sidenav-main">
        <div class="sidenav-header">
            <i class="fas fa-times p-3 cursor-pointer opacity-5 position-absolute end-0 top-0 d-none d-xl-none text-white" aria-hidden="true" id="iconSidenav"></i>
            <a class="m-0" href="index" target="_blank">
                <img src="./assets/img/logo-white.svg" class="w-100 mt-3" style="height:50px" alt="main_logo">
            </a>
        </div>
        <hr class="horizontal light mt-0 mb-2">
        <div class="collapse navbar-collapse  w-auto h-auto" id="sidenav-collapse-main">
            <ul class="navbar-nav">
                <li class="nav-item mb-2 mt-0">
                    <a data-bs-toggle="collapse" href="#ProfileNav" class="nav-link text-white" aria-controls="ProfileNav" role="button" aria-expanded="false">
                        <img src="./assets/img/team-3.jpg" class="avatar">
                        <span class="nav-link-text ms-2 ps-1"><?php echo $username ?></span>
                    </a>
                    <div class="collapse" id="ProfileNav">
                        <ul class="nav ">
                            <li class="nav-item">
                                <a class="nav-link text-white" href="../../pages/pages/profile/overview.html">
                                    <i class="fa fa-outdent"></i>
                                    <span class="sidenav-normal  ms-3  ps-1"><?php echo $section ?> </span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white" href="../../pages/pages/profile/overview.html">
                                    <!-- <span class="sidenav-mini-icon"> MP </span> -->
                                    <i class="fa fa-user"></i>
                                    <span class="sidenav-normal  ms-3  ps-1"> My Profile </span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white" href="../../pages/pages/account/settings.html">
                                    <i class="fa fa-gear"></i>
                                    <span class="sidenav-normal  ms-3  ps-1"> Settings </span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white" href="includes/logout.php">
                                    <i class="fa fa-right-from-bracket"></i>
                                    <span class="sidenav-normal  ms-3  ps-1"> Logout </span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <hr class="horizontal mt-0 dark">
                <li class="nav-item">
                    <a class="nav-link <?php if (($page == 'index') && ($divName == 'Headquarters')) { ?> active bg-gradient-primary <?php } elseif (($page == 'index') && ($divName != 'Headquarters')) { ?> active bg-gradient-warning <?php } ?>" href="index">
                        <i class="fa fa-cube"></i>
                        <span class="nav-link-text ms-2 ps-1 font-weight-bold">Dashboards</span>
                    </a>
                </li>
                <li class="nav-item mt-3">
                    <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder text-white">Management</h6>
                </li>

                <li class="nav-item">
                    <a data-bs-toggle="collapse" href="#dashboardsExamples1" class="nav-link text-white" aria-controls="dashboardsExamples1" role="button" aria-expanded="false">
                        <i class="fa fa-pen-to-square"></i>
                        <span class="nav-link-text ms-2 ps-1 font-weight-bold">Data Entry</span>
                    </a>
                    <div class="collapse <?php if ($page == 'data_entry') { ?> show <?php } ?>" id="dashboardsExamples1">
                        <ul class="nav ">
                            <li class="nav-item">
                                <a class="nav-link text-white <?php if (($page == 'data_entry') && ($subpage == 'data_entry') && ($divName == 'Headquarters')) { ?> active bg-gradient-primary <?php } elseif (($page == 'data_entry') && ($subpage == 'data_entry') && ($divName != 'Headquarters')) { ?> active bg-gradient-warning <?php } ?>" href="dataEntry.php?form=none">
                                    <i class="fas fa-keyboard"></i>
                                    <span class="sidenav-normal  ms-2  ps-1 font-weight-bold"> New Project</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white <?php if (($page == 'data_entry') && ($subpage == 'saved') && ($divName == 'Headquarters')) { ?> active bg-gradient-primary <?php } elseif (($page == 'data_entry') && ($subpage == 'saved') && ($divName != 'Headquarters')) { ?> active bg-gradient-warning <?php } ?>" href="savedProject.php">
                                    <i class="fas fa-floppy-disk"></i>
                                    <span class="sidenav-normal  ms-2  ps-1 font-weight-bold"> Saved</span>
                                </a>
                            </li>
                            <li class="nav-item ">
                                <a class="nav-link text-white <?php if (($page == 'data_entry') && ($subpage == 'update_home') && ($divName == 'Headquarters')) { ?> active bg-gradient-primary <?php } elseif (($page == 'data_entry') && ($subpage == 'update_home') && ($divName != 'Headquarters')) { ?> active bg-gradient-warning <?php } ?>" href="updateHome.php">
                                    <i class="fas fa-paper-plane"></i>
                                    <span class="sidenav-normal  ms-2  ps-1 font-weight-bold"> Updating </span>
                                </a>
                            </li>
                            <li class="nav-item ">
                                <a class="nav-link text-white <?php if (($page == 'data_entry') && ($subpage == 'add_fund') && ($divName == 'Headquarters')) { ?> active bg-gradient-primary <?php } elseif (($page == 'data_entry') && ($subpage == 'add_fund') && ($divName != 'Headquarters')) { ?> active bg-gradient-warning <?php } ?>" href="addFundHome.php">
                                    <i class="fas fa-dollar"></i>
                                    <span class="sidenav-normal  ms-2  ps-1 font-weight-bold"> Add / Update Fund </span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a data-bs-toggle="collapse" href="#dashboardsExamples" class="nav-link text-white" aria-controls="dashboardsExamples" role="button" aria-expanded="false">
                        <i class="fa fa-cubes"></i>
                        <span class="nav-link-text ms-2 ps-1 font-weight-bold">Job Management</span>
                    </a>
                    <div class="collapse <?php if (($page == 'lapi') || ($page == 'ncr')) { ?> show <?php } ?>" id="dashboardsExamples">
                        <ul class="nav ">
                            <li class="nav-item">
                                <a class="nav-link text-white <?php if (($page == 'lapi') && ($divName == 'Headquarters')) { ?> active bg-gradient-primary <?php } elseif (($page == 'lapi') && ($divName != 'Headquarters')) { ?> active bg-gradient-warning <?php } ?>" href="lapi">
                                    <i class="fas fa-building-circle-arrow-right"></i>
                                    <span class="sidenav-normal  ms-2  ps-1 font-weight-bold"> LAPI</span>
                                </a>
                            </li>
                            <li class="nav-item ">
                                <a class="nav-link text-white <?php if (($page == 'ncr') && ($divName == 'Headquarters')) { ?> active bg-gradient-primary <?php } elseif (($page == 'ncr') && ($divName != 'Headquarters')) { ?> active bg-gradient-warning <?php } ?>" href="ncr">
                                    <i class="fas fa-building-circle-arrow-right"></i>
                                    <span class="sidenav-normal  ms-2  ps-1 font-weight-bold"> NCR </span>
                                </a>
                            </li>
                            <li class="nav-item ">
                                <a class="nav-link text-white" href="../../pages/dashboards/sales.html">
                                    <i class="fas fa-layer-group"></i>
                                    <span class="sidenav-normal  ms-2  ps-1 font-weight-bold"> TOPO </span>
                                </a>
                            </li>
                            <li class="nav-item ">
                                <a class="nav-link text-white" href="../../pages/dashboards/automotive.html">
                                    <i class="fas fa-signs-post"></i>
                                    <span class="sidenav-normal  ms-2  ps-1 font-weight-bold"> Others </span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a data-bs-toggle="collapse" href="#pagesExamples" class="nav-link text-white" aria-controls="pagesExamples" role="button" aria-expanded="false">
                        <!-- <i class="material-icons-round {% if page.brand == 'RTL' %}ms-2{% else %} me-2{% endif %}">paid</i> -->
                        <i class="fa fa-dollar"></i>
                        <span class="nav-link-text ms-2 ps-1 font-weight-bold">Financial</span>
                    </a>
                    <div class="collapse <?php if (($page == 'financial-overview') || ($page == 'financial-overview')) { ?> show <?php } ?>" id="pagesExamples">
                        <ul class="nav ">
                            <li class="nav-item ">
                                <a class="nav-link text-white <?php if (($page == 'financial-overview') && ($divName == 'Headquarters')) { ?> active bg-gradient-primary <?php } elseif (($page == 'financial-overview') && ($divName != 'Headquarters')) { ?> active bg-gradient-warning <?php } ?>" href="financial">
                                    <span class="sidenav-mini-icon"> O </span>
                                    <span class="sidenav-normal  ms-2  ps-1 font-weight-bold"> Overview </span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a data-bs-toggle="collapse" href="#applicationsExamples" class="nav-link text-white collapsed" aria-controls="applicationsExamples" role="button" aria-expanded="false">
                        <i class="fa fa-users"></i>
                        <span class="nav-link-text ms-2 ps-1 font-weight-bold">Human Resources</span>
                    </a>
                    <div class="collapse  <?php if (($page == 'hr-overview') || ($page == 'hr-overview')) { ?> show <?php } ?>" id="applicationsExamples">
                        <ul class="nav ">
                            <li class="nav-item ">
                                <a class="nav-link text-white <?php if (($page == 'hr-overview') && ($divName == 'Headquarters')) { ?> active bg-gradient-primary <?php } elseif (($page == 'hr-overview') && ($divName != 'Headquarters')) { ?> active bg-gradient-warning <?php } ?>" href="hr_overview">
                                    <span class="sidenav-mini-icon"> O </span>
                                    <span class="sidenav-normal  ms-2  ps-1 font-weight-bold"> Overview</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item mt-3">
                    <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder text-white">Masterplan</h6>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if (($page == 'plan') && $divName == 'Headquarters') {
                                            echo 'active bg-gradient-primary';
                                        } elseif (($page == 'plan') && ($divName != 'Headquarters')) {
                                            echo 'active bg-gradient-warning';
                                        } ?>" href="masterPlan.php">
                        <i class="fas fa-map-location-dot"></i>
                        <span class="nav-link-text ms-2 ps-1 font-weight-bold">Plan</span>
                    </a>
                </li>
                <li class="nav-item mt-3">
                    <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder text-white">Monitoring</h6>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if (($page == 'executive') && $divName == 'Headquarters') {
                                            echo 'active bg-gradient-primary';
                                        } elseif (($page == 'executive') && ($divName != 'Headquarters')) {
                                            echo 'active bg-gradient-warning';
                                        } ?>" href="execDashboard.php">
                        <i class="fas fa-user-tie"></i>
                        <span class="nav-link-text ms-2 ps-1 font-weight-bold">Executive</span>
                    </a>
                </li>
                <li class="nav-item mt-3">
                    <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder text-white">Others</h6>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="https://github.com/creativetimofficial/ct-material-dashboard-pro/blob/master/CHANGELOG.md">
                        <i class="material-icons-round {% if page.brand == 'RTL' %}ms-2{% else %} me-2{% endif %}">storefront</i>
                        <span class="nav-link-text ms-2 ps-1">Store</span>
                    </a>
                </li>
                <li class="nav-item mt-3">
                    <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder text-white">Reporting</h6>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if (($page == 'reports') && ($divName == 'Headquarters')) { ?> active bg-gradient-primary <?php } elseif (($page == 'reports') && ($divName != 'Headquarters')) { ?> active bg-gradient-warning <?php } ?>" href="reports">
                        <i class="material-icons-round {% if page.brand == 'RTL' %}ms-2{% else %} me-2{% endif %}">receipt_long</i>
                        <span class="nav-link-text ms-2 ps-1 font-weight-bold">Report</span>
                    </a>
                </li>
            </ul>
        </div>
    </aside>
<?php } else if ($section == 'Land') { ?>
    <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 <?php if ($divName == 'Headquarters') { ?>  bg-gradient-dark <?php } else { ?> bg-gradient-danger <?php } ?> bg-dark " id="sidenav-main">
        <div class="sidenav-header">
            <i class="fas fa-times p-3 cursor-pointer opacity-5 position-absolute end-0 top-0 d-none d-xl-none text-white" aria-hidden="true" id="iconSidenav"></i>
            <a class="m-0" href="index" target="_blank">
                <img src="./assets/img/logo-white.svg" class="w-100 mt-3" style="height:50px" alt="main_logo">
            </a>
        </div>
        <hr class="horizontal light mt-0 mb-2">
        <div class="collapse navbar-collapse  w-auto h-auto" id="sidenav-collapse-main">

            <ul class="navbar-nav">
                <li class="nav-item mb-2 mt-0">
                    <a data-bs-toggle="collapse" href="#ProfileNav" class="nav-link text-white" aria-controls="ProfileNav" role="button" aria-expanded="false">
                        <img src="./assets/img/team-3.jpg" class="avatar">
                        <span class="nav-link-text ms-2 ps-1 font-weight-bold"><?php echo $username ?></span>
                    </a>
                    <div class="collapse" id="ProfileNav">
                        <ul class="nav ">
                            <li class="nav-item">
                                <a class="nav-link text-white" href="../../pages/pages/profile/overview.html">
                                    <i class="fa fa-outdent"></i>
                                    <span class="sidenav-normal  ms-3  ps-1"><?php echo $section ?> </span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white" href="../../pages/pages/profile/overview.html">
                                    <!-- <span class="sidenav-mini-icon"> MP </span> -->
                                    <i class="fa fa-user"></i>
                                    <span class="sidenav-normal  ms-3  ps-1"> My Profile </span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white" href="../../pages/pages/account/settings.html">
                                    <i class="fa fa-gear"></i>
                                    <span class="sidenav-normal  ms-3  ps-1"> Settings </span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white" href="includes/logout.php">
                                    <i class="fa fa-right-from-bracket"></i>
                                    <span class="sidenav-normal  ms-3  ps-1"> Logout </span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <hr class="horizontal mt-0 dark">
                <li class="nav-item">
                    <a class="nav-link <?php if ($page == 'index') { ?> active bg-gradient-dark <?php } ?>" href="index">
                        <!-- <i class="material-icons-round opacity-10">dashboard</i> -->
                        <i class="fa fa-cube"></i>
                        <span class="nav-link-text ms-2 ps-1 font-weight-bold">Dashboards</span>
                    </a>
                </li>
                <li class="nav-item mt-3">
                    <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder text-white">Management</h6>
                </li>
                <li class="nav-item">
                    <a data-bs-toggle="collapse" href="#dashboardsExamples1" class="nav-link text-white" aria-controls="dashboardsExamples1" role="button" aria-expanded="false">
                        <i class="fa fa-pen-to-square"></i>
                        <span class="nav-link-text ms-2 ps-1 font-weight-bold">Data Entry</span>
                    </a>
                    <div class="collapse <?php if ($page == 'data_entry') { ?> show <?php } ?>" id="dashboardsExamples1">
                        <ul class="nav ">
                            <li class="nav-item">
                                <a class="nav-link text-white <?php if (($page == 'data_entry') && ($subpage == 'data_entry') && ($divName == 'Headquarters')) { ?> active bg-gradient-primary <?php } elseif (($page == 'data_entry') && ($subpage == 'data_entry') && ($divName != 'Headquarters')) {
                                                                                                                                                                                                if ($section == 'Land') { ?> active bg-gradient-dark <?php } else ?> active bg-gradient-warning <?php } ?>" href="dataEntry.php?form=none">
                                    <i class="fas fa-keyboard"></i>
                                    <span class="sidenav-normal  ms-2  ps-1 font-weight-bold"> New Project</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white <?php if (($page == 'data_entry') && ($subpage == 'saved') && ($divName == 'Headquarters')) { ?> active bg-gradient-primary <?php } elseif (($page == 'data_entry') && ($subpage == 'saved') && ($divName != 'Headquarters')) {
                                                                                                                                                                                            if ($section == 'Land') { ?> active bg-gradient-dark <?php } else ?> active bg-gradient-warning <?php } ?>" href="savedProject.php">
                                    <i class="fas fa-floppy-disk"></i>
                                    <span class="sidenav-normal  ms-2  ps-1 font-weight-bold"> Saved</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white <?php if (($page == 'data_entry') && ($subpage == 'submit') && ($divName == 'Headquarters')) { ?> active bg-gradient-primary <?php } elseif (($page == 'data_entry') && ($subpage == 'submit') && ($divName != 'Headquarters')) {
                                                                                                                                                                                            if ($section == 'Land') { ?> active bg-gradient-dark <?php } else ?> active bg-gradient-warning <?php } ?>" href="submit2sb.php">
                                    <i class="fas fa-paper-plane"></i>
                                    <span class="sidenav-normal  ms-2  ps-1 font-weight-bold"> Submitted</span>
                                </a>
                            </li>
                            <li class="nav-item ">
                                <a class="nav-link text-white <?php if (($page == 'data_entry') && ($subpage == 'update_home') && ($divName == 'Headquarters')) { ?> active bg-gradient-primary <?php } elseif (($page == 'data_entry') && ($subpage == 'add_fund') && ($divName != 'Headquarters')) { ?> active bg-gradient-warning <?php } ?>" href="updateHome.php">
                                    <i class="fas fa-arrows-rotate"></i>
                                    <span class="sidenav-normal  ms-2  ps-1 font-weight-bold"> Updating </span>
                                </a>
                            </li>
                            <li class="nav-item ">
                                <a class="nav-link text-white <?php if (($page == 'data_entry') && ($subpage == 'add_fund') && ($divName == 'Headquarters')) { ?> active bg-gradient-primary <?php } elseif (($page == 'data_entry') && ($subpage == 'add_fund') && ($divName != 'Headquarters')) { ?> active bg-gradient-warning <?php } ?>" href="addFundHome.php">
                                    <span class="sidenav-mini-icon"> $ </span>
                                    <span class="sidenav-normal  ms-2  ps-1 font-weight-bold"> Add / Update Fund </span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if ($page == 'sj_generate') { ?> active bg-gradient-dark <?php } ?>" href="sj_generate">
                        <i class="fa fa-cubes"></i>
                        <span class="nav-link-text ms-2 ps-1 font-weight-bold">Survey Job</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link " href="index.php">
                        <i class="fa fa-history"></i>
                        <span class="nav-link-text ms-2 ps-1 font-weight-bold">History</span>
                    </a>
                </li>
                <li class="nav-item mt-3">
                    <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder text-white">Reporting</h6>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if (($page == 'reports') && ($divName == 'Headquarters')) { ?> active bg-gradient-primary <?php } elseif (($page == 'reports') && ($divName != 'Headquarters')) { ?> active bg-gradient-warning <?php } ?>" href="reports">
                        <i class="material-icons-round {% if page.brand == 'RTL' %}ms-2{% else %} me-2{% endif %}">receipt_long</i>
                        <span class="nav-link-text ms-2 ps-1 font-weight-bold">Report</span>
                    </a>
                </li>
            </ul>
        </div>
    </aside>
<?php } else if ($section == 'Valuation') { ?>
    <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 <?php if ($divName == 'Headquarters') { ?>  bg-gradient-dark <?php } else { ?> bg-gradient-success <?php } ?> bg-dark " id="sidenav-main">
        <div class="sidenav-header">
            <i class="fas fa-times p-3 cursor-pointer opacity-5 position-absolute end-0 top-0 d-none d-xl-none text-white" aria-hidden="true" id="iconSidenav"></i>
            <a class="m-0" href="index" target="_blank">
                <img src="./assets/img/logo-white.svg" class="w-100 mt-3" style="height:50px" alt="main_logo">
            </a>
        </div>
        <hr class="horizontal light mt-0 mb-2">
        <div class="collapse navbar-collapse  w-auto h-auto" id="sidenav-collapse-main">

            <ul class="navbar-nav">
                <li class="nav-item mb-2 mt-0">
                    <a data-bs-toggle="collapse" href="#ProfileNav" class="nav-link text-white" aria-controls="ProfileNav" role="button" aria-expanded="false">
                        <img src="./assets/img/team-3.jpg" class="avatar">
                        <span class="nav-link-text ms-2 ps-1 font-weight-bold"><?php echo $username ?></span>
                    </a>
                    <div class="collapse" id="ProfileNav">
                        <ul class="nav ">
                            <li class="nav-item">
                                <a class="nav-link text-white" href="../../pages/pages/profile/overview.html">
                                    <i class="fa fa-outdent"></i>
                                    <span class="sidenav-normal  ms-3  ps-1"><?php echo $section ?> </span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white" href="../../pages/pages/profile/overview.html">
                                    <!-- <span class="sidenav-mini-icon"> MP </span> -->
                                    <i class="fa fa-user"></i>
                                    <span class="sidenav-normal  ms-3  ps-1"> My Profile </span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white" href="../../pages/pages/account/settings.html">
                                    <i class="fa fa-gear"></i>
                                    <span class="sidenav-normal  ms-3  ps-1"> Settings </span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white" href="includes/logout.php">
                                    <i class="fa fa-right-from-bracket"></i>
                                    <span class="sidenav-normal  ms-3  ps-1"> Logout </span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <hr class="horizontal mt-0 dark">
                <li class="nav-item">
                    <a class="nav-link <?php if ($page == 'index') { ?> active bg-gradient-dark <?php } ?>" href="index">
                        <!-- <i class="material-icons-round opacity-10">dashboard</i> -->
                        <i class="fa fa-cube"></i>
                        <span class="nav-link-text ms-2 ps-1 font-weight-bold">Dashboards</span>
                    </a>
                </li>
                <li class="nav-item mt-3">
                    <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder text-white">Management</h6>
                </li>
                <li class="nav-item">
                    <a data-bs-toggle="collapse" href="#dashboardsExamples1" class="nav-link text-white" aria-controls="dashboardsExamples1" role="button" aria-expanded="false">
                        <i class="fa fa-pen-to-square"></i>
                        <span class="nav-link-text ms-2 ps-1 font-weight-bold">Data Entry</span>
                    </a>
                    <div class="collapse <?php if ($page == 'data_entry') { ?> show <?php } ?>" id="dashboardsExamples1">
                        <ul class="nav ">
                            <li class="nav-item">
                                <a class="nav-link text-white <?php if (($page == 'data_entry') && ($subpage == 'data_entry') && ($divName == 'Headquarters')) { ?> active bg-gradient-primary <?php } elseif (($page == 'data_entry') && ($subpage == 'data_entry') && ($divName != 'Headquarters')) {
                                                                                                                                                                                                if ($section == 'Land') { ?> active bg-gradient-dark <?php } else ?> active bg-gradient-warning <?php } ?>" href="dataEntry.php?form=none">
                                    <i class="fas fa-keyboard"></i>
                                    <span class="sidenav-normal  ms-2  ps-1 font-weight-bold"> New Project</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white <?php if (($page == 'data_entry') && ($subpage == 'saved') && ($divName == 'Headquarters')) { ?> active bg-gradient-primary <?php } elseif (($page == 'data_entry') && ($subpage == 'saved') && ($divName != 'Headquarters')) {
                                                                                                                                                                                            if ($section == 'Land') { ?> active bg-gradient-dark <?php } else ?> active bg-gradient-warning <?php } ?>" href="savedProject.php">
                                    <i class="fas fa-floppy-disk"></i>
                                    <span class="sidenav-normal  ms-2  ps-1 font-weight-bold"> Saved</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-white <?php if (($page == 'data_entry') && ($subpage == 'submit') && ($divName == 'Headquarters')) { ?> active bg-gradient-primary <?php } elseif (($page == 'data_entry') && ($subpage == 'submit') && ($divName != 'Headquarters')) {
                                                                                                                                                                                            if ($section == 'Land') { ?> active bg-gradient-dark <?php } else ?> active bg-gradient-warning <?php } ?>" href="submit2sb.php">
                                    <i class="fas fa-paper-plane"></i>
                                    <span class="sidenav-normal  ms-2  ps-1 font-weight-bold"> Submitted</span>
                                </a>
                            </li>
                            <li class="nav-item ">
                                <a class="nav-link text-white <?php if (($page == 'data_entry') && ($subpage == 'update_home') && ($divName == 'Headquarters')) { ?> active bg-gradient-primary <?php } elseif (($page == 'data_entry') && ($subpage == 'add_fund') && ($divName != 'Headquarters')) { ?> active bg-gradient-warning <?php } ?>" href="updateHome.php">
                                    <i class="fas fa-arrows-rotate"></i>
                                    <span class="sidenav-normal  ms-2  ps-1 font-weight-bold"> Updating </span>
                                </a>
                            </li>
                            <li class="nav-item ">
                                <a class="nav-link text-white <?php if (($page == 'data_entry') && ($subpage == 'add_fund') && ($divName == 'Headquarters')) { ?> active bg-gradient-primary <?php } elseif (($page == 'data_entry') && ($subpage == 'add_fund') && ($divName != 'Headquarters')) { ?> active bg-gradient-warning <?php } ?>" href="addFundHome.php">
                                    <span class="sidenav-mini-icon"> $ </span>
                                    <span class="sidenav-normal  ms-2  ps-1 font-weight-bold"> Add / Update Fund </span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if ($page == 'sj_generate') { ?> active bg-gradient-dark <?php } ?>" href="sj_generate">
                        <i class="fa fa-cubes"></i>
                        <span class="nav-link-text ms-2 ps-1 font-weight-bold">Survey Job</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link " href="index.php">
                        <i class="fa fa-history"></i>
                        <span class="nav-link-text ms-2 ps-1 font-weight-bold">History</span>
                    </a>
                </li>
            </ul>
        </div>
    </aside>
<?php } ?>