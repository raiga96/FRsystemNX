<!--
=========================================================
* Material Dashboard 2 - v3.0.0
=========================================================

* Original Product Page: https://www.creative-tim.com/product/material-dashboard
* Copyright 2021 Creative Tim (https://www.creative-tim.com)
* Licensed under MIT (https://www.creative-tim.com/license)
* Coded by Creative Tim
* Modified by Darmizi & Hillary for Survey System

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->
<?php
session_start();
$page = 'index';

// Connection for database
require 'includes/connect.php';
// Auto Select Sarawaknet Line or Not
require 'includes/line.php';
// Login function
require 'includes/login_function.php';


//Controller for Role and permission
require 'includes/controller/role_controller.php';

if ($divName != "Headquarters") {
  header("Location: dashboard2.php?division=$divName");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="./assets/img/favicon.svg">
  <link rel="icon" type="image/png" href="./assets/img/favicon.svg">
  <title>
    FRSystem
  </title>
  <!--     Fonts and icons     -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Roboto+Slab:400,700" />
  <!-- Nucleo Icons -->
  <link href="./assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="./assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="./assets/fontawesome/css/all.css">
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <!-- Material Icons -->
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
  <!-- CSS Files -->
  <link id="pagestyle" href="./assets/css/material-dashboard.css?v=3.0.0" rel="stylesheet" />

  <!--3D CHART JS & CSS-->
  <!--<script src="https://cdn.anychart.com/releases/8.11.0/js/anychart-base.min.js"></script>-->
  <script src="./assets/js/anychart/anychart-base.min.js"></script>
  <!--<script src="https://cdn.anychart.com/releases/8.11.0/js/anychart-exports.min.js"></script>-->
  <script src="./assets/js/anychart/anychart-exports.min.js"></script>
  <!--<script src="https://cdn.anychart.com/releases/8.11.0/js/anychart-ui.min.js"></script>-->
  <script src="./assets/js/anychart/anychart-ui.min.js"></script>
  <!--<link rel="stylesheet" href="https://cdn.anychart.com/releases/8.11.0/css/anychart-ui.min.css" />-->
  <link rel="stylesheet" href="./assets/js/anychart/anychart-ui.min.css" />

  <!--<script src="https://cdn.anychart.com/releases/8.11.0/js/anychart-core.min.js"></script>-->
  <script src="./assets/js/anychart/anychart-core.min.js"></script>
  <!--<script src="https://cdn.anychart.com/releases/8.11.0/js/anychart-cartesian-3d.min.js"></script>-->
  <script src="./assets/js/anychart/anychart-cartesian-3d.min.js"></script>

  <!--<script src="https://cdn.anychart.com/releases/8.11.0/js/anychart-pie.min.js"></script>-->
  <script src="./assets/js/anychart/anychart-pie.min.js"></script>

</head>

<body class="g-sidenav-show  bg-gray-200">
  <?php
  include("sidebar.php");
  ?>
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->
    <?php
    include("navbar.php");
    ?>
    <!-- End Navbar -->
    <?php
    if ($divName == 'Headquarters') {

      include("hq_dashboard.php");
    } else {

      include("div_dashboard.php");
    }
    ?>

    <!--   Core JS Files   -->
    <script src="./assets/js/core/popper.min.js"></script>
    <script src="./assets/js/core/bootstrap.min.js"></script>
    <script src="./assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="./assets/js/plugins/smooth-scrollbar.min.js"></script>
    <script src="./assets/js/plugins/chartjs.min.js"></script>
    <script src="./assets/js/jquery.min.js"></script>
    <!-- Script for count animation -->
    <script>
      $('.count1').each(function() {
        $(this).prop('Counter', 0).animate({
          Counter: $(this).text().replace(/,/g, '')
        }, {
          duration: 0,
          easing: 'swing',
          step: function(now) {
            $(this).text(Math.abs(now).toLocaleString(undefined, {
              minimumFractionDigits: 2
            }));
          }
        });
      });
    </script>
    <script>
      $('.count').each(function() {
        $(this).prop('Counter', 0).animate({
          Counter: $(this).text().replace(/,/g, '')
        }, {
          duration: 0,
          easing: 'swing',
          step: function(now) {
            $(this).text(Math.ceil(now).toLocaleString());
          }
        });
      });
    </script>
    <!-- End Script for count animation -->

    <script>
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

      $sql3 = "SELECT COUNT(*) as count FROM project_name AS a JOIN connector AS b ON b.project_id = a.project_id JOIN surveyjob AS c ON c.sj_id = b.sj_id WHERE b.main_status BETWEEN 20 AND 49";
      $res3 = $conn->query($sql3);
      $row3 = $res3->fetch_assoc();
      $query = $row3['count'];
      ?>
      anychart.onDocumentReady(function() {

        var data = anychart.data.set([{
            x: 'Data Entry',
            value: <?php echo $status_counts[0][1] ?>,
            normal: {
              fill: '#FB2571'
            }
          },
          {
            x: 'Pending Assign',
            value: <?php echo $status_counts[1][1] ?>,
            normal: {
              fill: '#5B595A'
            }
          },
          {
            x: 'Pending TA',
            value: <?php echo $status_counts[2][1] ?>,
            normal: {
              fill: '#2684DA'
            }
          },
          {
            x: 'Field Survey',
            value: <?php echo $status_counts[3][1] + $status_counts[4][1] ?>,
            normal: {
              fill: '#F0B010'
            }
          },
          {
            x: 'Computation',
            value: <?php echo $status_counts[5][1] + $status_counts[6][1] ?>,
            normal: {
              fill: '#0D0D0D'
            }
          },
          {
            x: 'Charting',
            value: <?php echo $status_counts[7][1] + $status_counts[8][1] ?>,
            normal: {
              fill: '#1BB564'
            }
          },
          {
            x: 'Pending SBR',
            value: <?php echo $status_counts[9][1] ?>,
            normal: {
              fill: '#A80C95'
            }
          },
          {
            x: 'QUERY',
            value: <?php echo $query ?>,
            normal: {
              fill: '#D11926'
            }
          },
        ]);


        // create pie chart with passed data
        var chart = anychart.pie(data);

        // set chart radius
        chart
          .innerRadius('55%')
          // set value for the exploded slices
          .explode(25);
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
                                 a.main_status != '11'";
        $res = $conn->query($sql);
        $row = $res->fetch_assoc();
        $semua = $row['count'];
        ?>
        // create standalone label and set settings
        var label = anychart.standalones.label();
        label
          .enabled(true)
          .text('<?php echo $semua ?>\n Pending')
          .width('100%')
          .height('100%')
          .adjustFontSize(true, true)
          .minFontSize(10)
          .maxFontSize(25)
          .fontColor('#60727b')
          .position('center')
          .anchor('center')
          .hAlign('center')
          .vAlign('middle');

        // set label to center content of chart
        chart.center().content(label);



        // set hovered settings
        chart.hovered().fill('#6f3448');

        // set selected settings
        chart.selected().fill('#ff6e40');

        // Hide the legend
        chart.legend(null);

        // set hovered outline settings
        chart
          .hovered()
          .outline()
          .fill(function() {
            return anychart.color.lighten('#6f3448', 0.55);
          });

        // set selected outline settings
        chart
          .selected()
          .outline()
          .offset(5)
          .fill(function() {
            return anychart.color.lighten('#ff6e40', 0.25);
          });

        // set container id for the chart
        chart.container('lapi-fund-ach');
        // initiate chart drawing
        chart.draw();
      });
    </script>

    <script>
      anychart.onDocumentReady(function() {
        // create data set
        var data = anychart.data.set([
          ['Completed', 115200],
          ['In Progress', 35850],
          ['Delayed', 75900]
        ]);

        // create pie chart with passed data
        var chart = anychart.pie(data);

        // set chart radius
        chart
          .innerRadius('55%')
          // set value for the exploded slices
          .explode(25);

        // create standalone label and set settings
        var label = anychart.standalones.label();
        label
          .enabled(true)
          .text('75%\n Completed')
          .width('100%')
          .height('100%')
          .adjustFontSize(true, true)
          .minFontSize(10)
          .maxFontSize(25)
          .fontColor('#60727b')
          .position('center')
          .anchor('center')
          .hAlign('center')
          .vAlign('middle');

        // set label to center content of chart
        chart.center().content(label);

        // create range color palette with color ranged
        var palette = anychart.palettes.rangeColors();
        palette.items([{
          color: '#FF0064'
        }, {
          color: '#dba869'
        }]);
        // set chart palette
        chart.palette(palette);

        // set hovered settings
        chart.hovered().fill('#6f3448');

        // set selected settings
        chart.selected().fill('#ff6e40');

        // Hide the legend
        chart.legend(null);

        // set hovered outline settings
        chart
          .hovered()
          .outline()
          .fill(function() {
            return anychart.color.lighten('#6f3448', 0.55);
          });

        // set selected outline settings
        chart
          .selected()
          .outline()
          .offset(5)
          .fill(function() {
            return anychart.color.lighten('#ff6e40', 0.25);
          });

        // set container id for the chart
        chart.container('overall-performance');
        // initiate chart drawing
        chart.draw();
      });
    </script>

    <?php include("./script/div_script.js"); ?>


    <script>
      var win = navigator.platform.indexOf('Win') > -1;
      if (win && document.querySelector('#sidenav-scrollbar')) {
        var options = {
          damping: '0.5'
        }
        Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
      }
    </script>
    <!-- Github buttons -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="./assets/js/material-dashboard.min.js?v=3.0.0"></script>
</body>

</html>