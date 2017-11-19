<?php
/*
This Code dynamically generates and downloads individual PDF (Annual Totals Summary)
	for the admin page
@ Yining Huang
*/

include_once "../../constants.php";
include_once "../../session.php";
//session_start();
include_once "../../report_template/pdfTemplate_AnnualTotals.php";
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

$certid = $_SESSION['view_certNo'];

///////////////////////////////////////////////////////////////////

$pdf = new myPDF('L','mm','A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->generate($conn);

sqlsrv_close($conn);
$name = (string)$certid."_"."AnnualTotalsSummary.pdf";
$pdf->Output($name,'D');
?>
