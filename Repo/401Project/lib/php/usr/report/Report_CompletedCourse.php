<?php
/*
This Code dynamically generate individual PDF (Completed COurse SummaryReport)
@ Yining Huang
*/

require_once "../../constants.php";
require_once "../../session.php";
session_start();
require_once "../../report_template/pdfTemplate_CompletedCourse.php";
include_once "../../LDAP/getLdapInfoInReport.php";
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

$certid =  getCertNo();
$fromYearInt = $_SESSION["fromYearInt"];
$toYearInt = $_SESSION["toYearInt"];
$empid = getEmployeeID();
$ldap_info = getInfo($empid);

///////////////////////////////////////////////////////////////////

$pdf = new myPDF('L','mm','A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->generate($conn);

sqlsrv_close($conn);
$pdf->Output('I');
?>
