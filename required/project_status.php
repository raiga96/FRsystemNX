<?php 
//COUNT FOR PROJECT STATUS
$sqlDa = "SELECT COUNT(*) as COUNT FROM mossla_case WHERE CASE_STATUS IN ('1','1A','2','3','3A','3B')";
$resDa = $conn -> query($sqlDa);
$rowDa = $resDa -> fetch_assoc();
$dataentry = $rowDa['COUNT'];

$sqlSr = "SELECT COUNT(*) as COUNT FROM mossla_case WHERE CASE_STATUS = '5'";
$resSr = $conn -> query($sqlSr);
$rowSr = $resSr -> fetch_assoc();
$surveyField = $rowSr['COUNT'];

$sqlComp = "SELECT COUNT(*) as COUNT FROM mossla_case WHERE CASE_STATUS = '6'";
$resComp = $conn -> query($sqlComp);
$rowComp = $resComp -> fetch_assoc();
$comp= $rowComp['COUNT'];

$sqlChart = "SELECT COUNT(*) as COUNT FROM mossla_case WHERE CASE_STATUS = '7'";
$resChart = $conn -> query($sqlChart);
$rowChart = $resChart -> fetch_assoc();
$chart = $rowChart['COUNT'];

$sqlsbr = "SELECT COUNT(*) as COUNT FROM mossla_case WHERE CASE_STATUS = '8'";
$ressbr = $conn -> query($sqlsbr);
$rowsbr = $ressbr -> fetch_assoc();
$sbrc = $rowsbr['COUNT'];

$sqlSuC = "SELECT COUNT(*) as COUNT FROM mossla_case WHERE CASE_STATUS = '9'";
$resSuC = $conn -> query($sqlSuC);
$rowSuC = $resSuC -> fetch_assoc();
$surComp = $rowSuC['COUNT'];

$sqlpp = "SELECT COUNT(*) as COUNT FROM mossla_case WHERE CASE_STATUS = '10'";
$respp = $conn -> query($sqlpp);
$rowpp = $respp -> fetch_assoc();
$ppc = $rowpp['COUNT'];

$sqlCompl = "SELECT COUNT(*) as COUNT FROM mossla_case WHERE CASE_STATUS = '11'";
$resCompl = $conn -> query($sqlCompl);
$rowCompl = $resCompl -> fetch_assoc();
$completed = $rowCompl['COUNT'];

$sqlKIV = "SELECT COUNT(*) as COUNT FROM mossla_case WHERE CASE_STATUS = '22'";
$resKIV = $conn -> query($sqlKIV);
$rowKIV = $resKIV -> fetch_assoc();
$kiv = $rowKIV['COUNT'];




//COUNT FOR DATA ENTRY STATUS
$sqlNDE = "SELECT COUNT(*) as COUNT FROM mossla_case WHERE CASE_STATUS = '1'";
$resNDE = $conn -> query($sqlNDE);
$rowNDE = $resNDE -> fetch_assoc();
$not_entry = $rowNDE['COUNT'];

$sqlPNS = "SELECT COUNT(*) as COUNT FROM mossla_case WHERE CASE_STATUS = '1A'";
$resPNS = $conn -> query($sqlPNS);
$rowPNS = $resPNS -> fetch_assoc();
$not_sub = $rowPNS['COUNT'];

$sqlSJI = "SELECT COUNT(*) as COUNT FROM mossla_case WHERE CASE_STATUS = '2'";
$resSJI = $conn -> query($sqlSJI);
$rowSJI = $resSJI -> fetch_assoc();
$sji = $rowSJI['COUNT'];

$sqlSJG = "SELECT COUNT(*) as COUNT FROM mossla_case WHERE CASE_STATUS = '3'";
$resSJG = $conn -> query($sqlNDE);
$rowSJG = $resSJG -> fetch_assoc();
$sjg = $rowSJG['COUNT'];

$sqlPSSS = "SELECT COUNT(*) as COUNT FROM mossla_case WHERE CASE_STATUS = '3A'";
$resPSSS = $conn -> query($sqlPSSS);
$rowPSSS = $resPSSS -> fetch_assoc();
$psss = $rowPSSS['COUNT'];

$sqlSJSS = "SELECT COUNT(*) as COUNT FROM mossla_case WHERE CASE_STATUS = '3B'";
$resSJSS = $conn -> query($sqlSJSS);
$rowSJSS = $resSJSS -> fetch_assoc();
$sjss = $rowSJSS['COUNT'];


//COUNT FOR LAPI
$sql02 = "SELECT COUNT(*) as COUNT FROM mossla_project WHERE PJ_LAPI = '02'";
$res02 = $conn -> query($sql02);
$row02 = $res02 -> fetch_assoc();
$lapi02 = $row02['COUNT'];

$sql06 = "SELECT COUNT(*) as COUNT FROM mossla_project WHERE PJ_LAPI = '06'";
$res06 = $conn -> query($sql06);
$row06 = $res06 -> fetch_assoc();
$lapi06 = $row06['COUNT'];

$sql08 = "SELECT COUNT(*) as COUNT FROM mossla_project WHERE PJ_LAPI = '08'";
$res08 = $conn -> query($sql08);
$row08 = $res08 -> fetch_assoc();
$lapi08 = $row08['COUNT'];

$sql15 = "SELECT COUNT(*) as COUNT FROM mossla_project WHERE PJ_LAPI = '15'";
$res15 = $conn -> query($sql15);
$row15 = $res15 -> fetch_assoc();
$lapi15 = $row15['COUNT'];

$sql10 = "SELECT COUNT(*) as COUNT FROM mossla_project WHERE PJ_LAPI = '10'";
$res10 = $conn -> query($sql10);
$row10 = $res10 -> fetch_assoc();
$lapi10 = $row10['COUNT'];

$sql11 = "SELECT COUNT(*) as COUNT FROM mossla_project WHERE PJ_LAPI = '11'";
$res11 = $conn -> query($sql11);
$row11 = $res11 -> fetch_assoc();
$lapi11 = $row11['COUNT'];

$sql14 = "SELECT COUNT(*) as COUNT FROM mossla_project WHERE PJ_LAPI = '14'";
$res14 = $conn -> query($sql14);
$row14 = $res14 -> fetch_assoc();
$lapi14 = $row14['COUNT'];


//COUNT FOR SURVEY JOB

//COUNT FOR PROJECT TARGET

// 1/4

$sqlPT4 = "SELECT COUNT(*) as COUNT FROM mossla_project WHERE PJ_TARGET = '1/4'";
$resPT4 = $conn -> query($sqlPT4);
$rowPT4 = $resPT4 -> fetch_assoc();
$PT1 = $rowPT4['COUNT'];

// 1/7

$sqlPT7 = "SELECT COUNT(*) as COUNT FROM mossla_project WHERE PJ_TARGET = '1/7'";
$resPT7 = $conn -> query($sqlPT7);
$rowPT7 = $resPT7 -> fetch_assoc();
$PT2 = $rowPT7['COUNT'];

// 31/12

$sqlPT12 = "SELECT COUNT(*) as COUNT FROM mossla_project WHERE PJ_TARGET = '31/12'";
$resPT12 = $conn -> query($sqlPT12);
$rowPT12 = $resPT12 -> fetch_assoc();
$PT3 = $rowPT12['COUNT'];

?>