<?php
/*
This Code dynamically generate individual PDF (Specific Year Report)
@ Yining Huang
*/

include_once "../../constants.php";
include_once "../../session.php";
//session_start();
include_once "../../report_template/pdfTemplate_specificYear.php";
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
     echo "Unable to connect.</br>";
     die( print_r( sqlsrv_errors(), true));
}
$totalcarryover = 0;

$all_certids = $_SESSION['view_certNo']; // maybe an array-
$certid = -1;
$year = $_SESSION['view_specific_year']; // get the current year

///////////////////////////////////////////////////////////////////

$pdf = new myPDF('L','mm','A4');
for ($i = 0; $i < count($all_certids); $i++) {
	$certid = $all_certids[$i];
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->generate($conn);
}

sqlsrv_close($conn);
$name = "LACounty".$year."_AnnualTraining.pdf";
$pdf->Output($name,'D');
?>