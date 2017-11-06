<?php
/*
This Code dynamically generate individual PDF (Specific Year Report)
@ Yining Huang
*/

require_once "../../constants.php";
require_once "../../session.php";
//session_start();
require_once "../../report_template/pdfTemplate_specificYear.php";
///////////////////////////////////////////////////////////////////
/* Access Database here */
$serverName = SQL_SERVER_NAME;
$uid = SQL_SERVER_USERNAME;
$pwd = SQL_SERVER_PASSWORD;
$db = SQL_SERVER_LACDATABASE;
$connectionInfo = array( "UID"=>$uid,
                         "PWD"=>$pwd,
                         "Database"=>$db,
             "ReturnDatesAsStrings"=>true);  // convert datetime to string

/* Connect using SQL Server Authentication. */
$conn = sqlsrv_connect( $serverName, $connectionInfo);
if( $conn === false )
{
     die( print_r( sqlsrv_errors(), true));
}
$totalcarryover = 0;

$certid =  getCertNo();
$year =  $_SESSION["specific_year"];

///////////////////////////////////////////////////////////////////

$pdf = new myPDF('L','mm','A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->generate($conn);

sqlsrv_close($conn);
$name = (string)$certid."_".$year."_AnnualTraining.pdf";
$pdf->Output($name,'D');
?>
