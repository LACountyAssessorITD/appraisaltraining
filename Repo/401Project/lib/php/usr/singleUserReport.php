<?php
/*
This Code dynamically generate individual PDF
@ Yining Huang
*/

include_once "../constants.php";
include_once "../session.php";
//session_start();
include_once "myPDF.php";
///////////////////////////////////////////////////////////////////
/* Access Database here */
$serverName = SQL_SERVER_NAME;
$uid = SQL_SERVER_USERNAME;
$pwd = SQL_SERVER_PASSWORD;
$db = SQL_SERVER_BOEDATABASE;
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

$certid =  getUserID();
$yearTypeKey = $_SESSION['yearTypeKey'];
if ($yearTypeKey == 'specific') {
	$year =  $_SESSION["specific_year"];
}
else {
	$fromYearInt = $_SESSION["fromYearInt"];
	$toYearInt = $_SESSION["toYearInt"];
}


///////////////////////////////////////////////////////////////////

$pdf = new myPDF('L','mm','A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->personInfo($conn);

sqlsrv_close($conn);
$pdf->Output('I');
?>
