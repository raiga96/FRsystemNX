<div class="container py-4">
  <?php

  //Count Total Lapi
  $sqlTot = "SELECT COUNT(*) AS count FROM project_name WHERE project_type = 'LAPI'";
  $resTot = $conn->query($sqlTot);
  $rowTot = $resTot->fetch_assoc();
  $countTot = $rowTot['count'];

  //Count for total
  $sqlTot = "SELECT COUNT(*) AS count FROM surveyjob AS a JOIN division AS b ON b.DIV_ID = a.sj_div";
  $resTot = $conn->query($sqlTot);
  $rowTot = $resTot->fetch_assoc();
  $totalSJ = $rowTot['count'];

  //total fund
  $sqlFund = "SELECT SUM(total_fund) AS total_fund FROM fund WHERE fund_type = 'LAPI'";
  $resFund = $conn->query($sqlFund);
  $rowFund = $resFund->fetch_assoc();
  $total_fund = $rowFund['total_fund'];

  //total expenditure
  $sqlExp = "SELECT SUM(expenditure) AS total_exp FROM expenditure AS a JOIN fund AS b ON b.fund_id = a.fund_id WHERE a.exp_type = 'EXP'";
  $resExp = $conn->query($sqlExp);
  $rowExp = $resExp->fetch_assoc();
  $total_exp = $rowExp['total_exp'];

  $fund_ach = ($total_exp / $total_fund) * 100;

  //Total semua project
  $sqlT = "SELECT COUNT(*) AS count FROM surveyjob";
  $resT = $conn->query($sqlT);
  $rowT = $resT->fetch_assoc();
  $total_semua = $rowT['count'];

  //Total Progress
  $sqlTProg = "SELECT COUNT(*) AS count FROM connector WHERE main_status != '10'";
  $resTProg = $conn->query($sqlTProg);
  $rowTProg = $resTProg->fetch_assoc();
  $total_progress = $rowTProg['count'];

  $sqlLP = "SELECT count(*) AS project FROM surveyjob AS a 
            JOIN connector AS b ON a.sj_id = b.sj_id 
            JOIN project_name AS c ON c.project_id = b.project_id
            WHERE c.project_type = 'LAPI'";
  $resLP = $conn->query($sqlLP);
  $rowLP = $resLP->fetch_assoc();
  $lpcount = $rowLP['project'];

  $sqlNCR = "SELECT count(*) AS project FROM surveyjob AS a 
            JOIN connector AS b ON a.sj_id = b.sj_id 
            JOIN project_name AS c ON c.project_id = b.project_id
            WHERE c.project_type = 'NCR'";
  $resNCR = $conn->query($sqlNCR);
  $rowNCR = $resNCR->fetch_assoc();
  $ncrCount = $rowNCR['project'];

  $sqlLPa = "SELECT count(*) AS project FROM surveyjob AS a 
            JOIN connector AS b ON a.sj_id = b.sj_id 
            JOIN project_name AS c ON c.project_id = b.project_id
            WHERE c.project_type = 'LAPI' AND b.main_status = '11'";
  $resLPa = $conn->query($sqlLPa);
  $rowLPa = $resLPa->fetch_assoc();
  $lp_comp = $rowLPa['project'];

  $sqlNCRa = "SELECT count(*) AS project FROM surveyjob AS a 
            JOIN connector AS b ON a.sj_id = b.sj_id 
            JOIN project_name AS c ON c.project_id = b.project_id
            WHERE c.project_type = 'NCR' AND b.main_status = '11'";
  $resNCRa = $conn->query($sqlNCRa);
  $rowNCRa = $resNCRa->fetch_assoc();
  $ncr_comp = $rowNCRa['project'];

  $lp_ach = $lp_comp / $lpcount * 100;
  $ncr_ach = $ncr_comp / $ncrCount * 100;

  $sj_ach = ($lp_ach + $ncr_ach) / 2;

  ?>
  <div class="row mt-4">
    <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4 ">
      <div class="card bg-gradient-dark">
        <div class="card-header p-3 pt-2 bg-gradient-dark">
          <div class="text-start pt-1">
            <p class="text-lg mb-3 text-capitalize text-white font-weight-bold">Fault Reports</p>
            <p class="text-sm mb-0 text-white">total as (<?php echo date("Y") ?>)</p>
            <h1 class="mb-0 text-white"><span class="count p-3"><?php echo $lpcount + $ncrCount ?></span><span class="text-lg ms-n1"></span></h1>
          </div>
        </div>
        <div class="card-footer pt-2 pb-2 m-2" align="center">
          <div class="row align-items-center">
            <div class="col-sm-4 col-xl-4">
              <a href="ncr">
                <h6 class="mb-0 text-white">New</h6>
                <p class="mb-0"><span class="text-white text-sm font-weight-bolder"><?php echo $ncrCount ?></span></p>
              </a>
            </div>
            <div class="col-sm-4 col-xl-4">
              <h6 class="mb-0 text-white">In Progress</h6>
              <p class="mb-0"><span class="text-white text-sm font-weight-bolder"><?php echo $lpcount ?></span></p>
            </div>
            <div class="col-sm-4 col-xl-4">
              <h6 class="mb-0 text-success">Solved</h6>
              <p class="mb-0"><span class="text-white text-sm font-weight-bolder">0</span></p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4 "> <a href="financial.php">
        <div class="card bg-gradient-dark">
          <div class="card-header p-3 pt-2 bg-gradient-dark">
            <div class="text-start pt-1">
              <p class="text-lg mb-3 text-capitalize text-white font-weight-bold">In Progress</p>
              <div class="row">
                <div class="col-8">
                  <p class="text-sm mb-0 text-white">percentage</p>
                  <h1 class="mb-0 text-white"><span class="p-3"><?php echo round($sj_ach, 2) ?></span><span class="text-lg ms-n1">%</span></h1>
                </div>
                <div class="col-4 mt-4 " align="right">
                  <p class="text-sm mb-0 text-white opacity-7">Total SJ</p>
                  <h5 class="mb-0 text-white opacity-5">
                    <span class="p-0"><?php echo $lpcount + $ncrCount ?></span>
                    </h6>
                </div>
              </div>
            </div>
          </div>
          <div class="card-footer pt-2 pb-2 m-2" align="center">
            <div class="row align-items-center">
              <div class="col-sm-4 col-xl-4">
                <h6 class="mb-0 text-white">Pending</h6>
                <p class="mb-0"><span class="text-white text-sm font-weight-bolder"><?php echo round($ncr_ach, 2) ?>% </span></p>
              </div>
              <div class="col-sm-4 col-xl-4">
                <h6 class="mb-0 text-white">Unassigned</h6>
                <p class="mb-0"><span class="text-white text-sm font-weight-bolder"><span class="count"><?php echo round($lp_ach, 2) ?></span>%</span></p>
              </div>
              <div class="col-sm-4 col-xl-4">
                <h6 class="mb-0 text-white">Others</h6>
                <p class="mb-0"><span class="text-white text-sm font-weight-bolder">0% </span></p>
              </div>
            </div>
          </div>
        </div>
      </a> </div>
    <div class="col-xl-4 col-sm-12 mb-xl-0 mb-4 ">
      <div class="card bg-gradient-dark"> <a href="hr_overview.php">
          <div class="card-header p-3 pt-2 bg-gradient-dark">
            <div class="text-start pt-1">
              <p class="text-lg mb-3 text-capitalize text-white font-weight-bold">Users</p>
              <p class="text-sm mb-0 text-white">total staff</p>
              <h1 class="mb-0 text-white"><span class="count">1,390</span></h1>
            </div>
          </div>
        </a>
        <div class="card-footer pt-2 pb-2 m-2" align="center">
          <div class="row align-items-center">
            <div class="col-sm-6 col-xl-6">
              <h6 class="mb-0 text-white">Active</h6>
              <p class="mb-0"><span class="text-white text-sm font-weight-bolder">0 </span></p>
            </div>
            <div class="col-sm-6 col-xl-6">
              <h6 class="mb-0 text-white">Inactive</h6>
              <p class="mb-0"><span class="text-white text-sm font-weight-bolder">0 </span></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row mt-4">
    <div class="col-lg-8">
      <div class="col-xl-12 col-md-12 col-sm-12 mt-4">
        <div class="card z-index-2">
          <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
            <div class="bg-gradient-dark shadow-dark border-radius-lg py-3 pe-1">
              <div>
                <h6 class="text-white text-capitalize ps-3">Ticket In Progress</h6>
                <p class="text-white text-sm ps-3 mb-0 "></p>
              </div>
            </div>
          </div>
          <?php
          $sql = "SELECT 
                                a.main_status,
                                COUNT(*) AS count
                            FROM 
                                connector AS a
                            JOIN 
                                project_name AS b ON b.project_id = a.project_id
                            JOIN 
                                division AS c ON c.DIV_ID = b.project_div
                            WHERE 
                                a.main_status BETWEEN 0 AND 19
                            GROUP BY 
                                a.main_status
                            ORDER BY 
                                a.main_status";
          $res = $conn->query($sql);

          $status_counts = array_fill(0, 19, [1 => 0]); // Initialize with 0 count from 0 to 10

          while ($rowasd = mysqli_fetch_assoc($res)) {
            $status = $rowasd['main_status'];
            $count = $rowasd['count'];
            $status_counts[$status][1] = $count;
          }
          ?>
          <div class="card-body row align-items-center">
            <div class="col-6" id="lapi-fund-ach" style="height:400px"> </div>
            <div class="col-6 ">
              <div class="table-responsive">
                <table class="table align-items-center mb-0">
                  <tbody>
                    <tr>
                      <td>
                        <div class="d-flex px-2 py-0"> <span class="badge bg-gradient-primary me-3"> </span>
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm">Unassigned</h6>
                          </div>
                        </div>
                      </td>
                      <td class="align-middle text-center text-sm">
                        <h6 class="text-lg opacity-10 text-weight-bold m-0"><span class="count"><?php echo $status_counts[0][1] ?></span></h6>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="d-flex px-2 py-0"> <span class="badge bg-gradient-secondary me-3"> </span>
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm">Lodge to SAINS</h6>
                          </div>
                        </div>
                      </td>
                      <td class="align-middle text-center text-sm">
                        <h6 class="text-lg opacity-10 text-weight-bold m-0"><span class="count"><?php echo $status_counts[1][1] ?></span></h6>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="d-flex px-2 py-0"> <span class="badge bg-gradient-warning me-3"> </span>
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm">Technical Review</h6>
                          </div>
                        </div>
                      </td>
                      <td class="align-middle text-center text-sm">
                        <h6 class="text-lg opacity-10 text-weight-bold m-0"><span class="count"><?php echo $status_counts[1][1] ?></span></h6>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="d-flex px-2 py-0"> <span class="badge bg-gradient-info me-3"> </span>
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm">Pending Closed</h6>
                          </div>
                        </div>
                      </td>
                      <td class="align-middle text-center text-sm">
                        <h6 class="text-lg opacity-10 text-weight-bold m-0"><span class="count"><?php echo $status_counts[2][1] ?></span></h6>
                      </td>
                    </tr>
                    <tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="row mt-4">
              <?php
              $sql3 = "SELECT COUNT(*) as count FROM project_name AS a JOIN connector AS b ON b.project_id = a.project_id JOIN surveyjob AS c ON c.sj_id = b.sj_id WHERE b.main_status = '11' ";
              $res3 = $conn->query($sql3);
              $row3 = $res3->fetch_assoc();
              $complete = $row3['count'];

              $sql3 = "SELECT COUNT(*) as count FROM project_name AS a JOIN connector AS b ON b.project_id = a.project_id JOIN surveyjob AS c ON c.sj_id = b.sj_id WHERE b.main_status = '14' ";
              $res3 = $conn->query($sql3);
              $row3 = $res3->fetch_assoc();
              $cancel = $row3['count'];

              $sql3 = "SELECT COUNT(*) as count FROM project_name AS a JOIN connector AS b ON b.project_id = a.project_id JOIN surveyjob AS c ON c.sj_id = b.sj_id WHERE b.main_status = '13' ";
              $res3 = $conn->query($sql3);
              $row3 = $res3->fetch_assoc();
              $kiv = $row3['count'];
              ?>
              <div class="col-xl-4 col-sm-12 mb-xl-0 mb-4 ">
                <div class="card bg-gradient-success">
                  <div class="card-header p-3 pt-2 bg-gradient-success">
                    <div class="text-start pt-1">
                      <p class="text-lg mb-0 text-capitalize text-white font-weight-bold">Solved</p>
                      <h1 class="mb-0 text-white"><span class="count"><?php echo $complete ?></span></h1>
                    </div>
                  </div>
                  <div class="card-footer pt-0 pb-1 m-1" align="center">
                  </div>
                </div>
              </div>
              <!-- KIV -->
              <div class="col-xl-4 col-sm-12 mb-xl-0 mb-4 ">
                <div class="card bg-gradient-info">
                  <div class="card-header p-3 pt-2 bg-gradient-info">
                    <div class="text-start pt-1">
                      <p class="text-lg mb-0 text-capitalize text-white font-weight-bold">KIV</p>
                      <h1 class="mb-0 text-white"><span class="count"><?php echo $kiv ?></span></h1>
                    </div>
                  </div>
                  <div class="card-footer pt-0 pb-1 m-1" align="center">
                  </div>
                </div>
              </div>
              <!-- Canceled -->
              <div class="col-xl-4 col-sm-12 mb-xl-0 mb-4 ">
                <div class="card bg-gradient-danger">
                  <div class="card-header p-3 pt-2 bg-gradient-danger">
                    <div class="text-start pt-1">
                      <p class="text-lg mb-0 text-capitalize text-white font-weight-bold">Rejected</p>
                      <h1 class="mb-0 text-white"><span class="count"><?php echo $cancel ?></span></h1>
                    </div>
                  </div>
                  <div class="card-footer pt-0 pb-1 m-1" align="center">
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">

      <!-- Panel -->
       <div class="row">
        <div class="col-xl-6 col-sm-6 mb-xl-0 mt-0 mb-4">
            <div class="card bg-gradient-danger"> <a href="#">
                <div class="card-header bg-gradient-danger p-3 pt-2 ">
                <div class="text-start pt-1">
                    <p class="text-lg mb-3 text-capitalize text-white font-weight-bold">Critical</p>
                    <h1 class="mb-0 text-white"><span class="count"><?php echo 0 ?></span></h1>
                </div>
                </div>
            </a>
            <div class="card-footer pt-1 pb-1 m-1" align="center">
                
            </div>
            </div>
        </div>
        <div class="col-xl-6 col-sm-6 mb-xl-0 mt-0 mb-4 ">
        <div class="card bg-gradient-info"> <a href="#">
            <div class="card-header bg-gradient-info p-3 pt-2 ">
              <div class="text-start pt-1">
                <p class="text-lg mb-3 text-capitalize text-white font-weight-bold">Urgent</p>
                <h1 class="mb-0 text-white"><span class="count"><?php echo 0 ?></span></h1>
              </div>
            </div>
          </a>
          <div class="card-footer pt-1 pb-1 m-1" align="center">
            
          </div>
        </div>
      </div>
       </div>
      <!-- End Panel -->
      
      <!-- KPI -->
      <?php
      $sqlLP = "SELECT count(*) AS project FROM surveyjob AS a 
                  JOIN connector AS b ON a.sj_id = b.sj_id 
                  JOIN project_name AS c ON c.project_id = b.project_id
                  WHERE c.project_type = 'LAPI' AND b.main_status = '10'";
      $resLP = $conn->query($sqlLP);
      $rowLP = $resLP->fetch_assoc();
      $clp = $rowLP['project'];

      $sqlNCR = "SELECT count(*) AS project FROM surveyjob AS a 
                  JOIN connector AS b ON a.sj_id = b.sj_id 
                  JOIN project_name AS c ON c.project_id = b.project_id
                  WHERE c.project_type = 'NCR' AND b.main_status = '10'";
      $resNCR = $conn->query($sqlNCR);
      $rowNCR = $resNCR->fetch_assoc();
      $cncr = $rowNCR['project'];
      ?>
      <div class="col-xl-12 col-sm-6 mb-xl-0 mt-4 mb-4 ">
        <div class="card bg-gradient-dark"> <a href="#">
            <div class="card-header bg-gradient-dark p-3 pt-2 ">
              <div class="text-start pt-1">
                <p class="text-lg mb-3 text-capitalize text-white font-weight-bold">KPI</p>
              </div>
            </div>
          </a>
          <?php
          $sqlKPI = "SELECT 
              kpi_status,
              COUNT(*) AS total_jobs
            FROM (
              SELECT 
                a.sj_id,
                c.dr_actual_complete,
                c.dr_target_complete,
                CASE 
                  WHEN c.dr_actual_complete < c.dr_target_complete THEN 'Ahead of Schedule'
                  WHEN c.dr_actual_complete = c.dr_target_complete THEN 'On Time'
                  ELSE 'Late'
                END AS kpi_status
              FROM field_survey AS a 
              JOIN computation AS b ON b.sj_id = a.sj_id 
              JOIN drawing AS c ON c.sj_id = a.sj_id 
              JOIN surveyjob AS d ON d.sj_id = a.sj_id
              WHERE c.dr_actual_complete IS NOT NULL AND c.dr_target_complete IS NOT NULL
            ) AS sub
            GROUP BY kpi_status";

          $resKpi = $conn->query($sqlKPI);

          // Initialize counts with 0 for each status
          $kpi_counts = [
            'Ahead of Schedule' => 0,
            'On Time' => 0,
            'Late' => 0
          ];

          while ($rowKpi = mysqli_fetch_assoc($resKpi)) {
            $status = $rowKpi['kpi_status'];
            $count = (int)$rowKpi['total_jobs'];
            $kpi_counts[$status] = $count;
          }
          ?>
          <div class="card-footer pt-2 pb-2 m-2" align="center">
            <div class="row align-items-center">
              <div class="col-sm-6 col-xl-6">
                <h6 class="mb-0 text-white">Ontime</h6>
                <p class="mb-0"><span class="text-white text-sm font-weight-bolder"><?php echo $kpi_counts['On Time'] ?></span></p>
              </div>
              <div class="col-sm-6 col-xl-6">
                <h6 class="mb-0 text-danger">Delayed</h6>
                <p class="mb-0"><span class="text-danger text-sm font-weight-bolder"><?php echo $kpi_counts['Late'] ?></span></p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- FR Category -->
      <?php
      $sqlLP = "SELECT count(*) AS project FROM connector AS b 
                  JOIN project_name AS c ON c.project_id = b.project_id
                  WHERE c.project_type = 'LAPI' AND b.main_status != '11'";
      $resLP = $conn->query($sqlLP);
      $rowLP = $resLP->fetch_assoc();
      $plp = $rowLP['project'];

      $sqlNCR = "SELECT count(*) AS project FROM connector AS b 
                  JOIN project_name AS c ON c.project_id = b.project_id
                  WHERE c.project_type = 'NCR' AND b.main_status != '11'";
      $resNCR = $conn->query($sqlNCR);
      $rowNCR = $resNCR->fetch_assoc();
      $pncr = $rowNCR['project'];
      ?>
      <div class="col-xl-12 col-sm-6 mb-xl-0 mt-4 mb-4 ">
        <div class="card bg-gradient-info"> <a href="#">
            <div class="card-header bg-gradient-dark p-3 pt-2 ">
              <div class="text-start pt-1">
                <p class="text-lg mb-3 text-capitalize text-white font-weight-bold">FR Category</p>
                <p class="text-sm mb-0 text-white">overall total (<?php echo date("Y") ?>)</p>
                <h1 class="mb-0 text-white"><span class="count"><?php echo $plp + $pncr ?></span></h1>
              </div>
            </div>
          </a>
          <div class="card-footer pt-2 pb-2 m-2" align="center">
            <div class="row align-items-center">
              <div class="col-sm-4 col-xl-4">
                <h6 class="mb-0 text-white">Software</h6>
                <p class="mb-0"><span class="text-white text-sm font-weight-bolder"><?php echo $plp ?></span></p>
              </div>
              <div class="col-sm-4 col-xl-4">
                <h6 class="mb-0 text-white">Hardware</h6>
                <p class="mb-0"><span class="text-white text-sm font-weight-bolder"><?php echo $pncr ?></span></p>
              </div>
              <div class="col-sm-4 col-xl-4">
                <h6 class="mb-0 text-white">Others</h6>
                <p class="mb-0"><span class="text-white text-sm font-weight-bolder">0 </span></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  
  <?php
  include("footer.php");
  ?>
</div>
</main>