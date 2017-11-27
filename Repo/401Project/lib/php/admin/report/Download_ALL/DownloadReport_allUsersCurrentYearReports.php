<?php
/*
This Code generates a 600+ pages pdf Annual Training report (for current fiscal year) 
and enable to download
@ Yining Huang
*/

include_once "../../../constants.php";
include_once "../../../session.php";
session_start();
include_once "pdfTemplate_allUsersCurrentYearReports.php";
include_once "../../../LDAP/getLdapInfoInReport.php";
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
$certid = -1;
$year = $_SESSION['current_fiscal_year'];

$all_certid;
$all_empid;

$tsql = "SELECT [New_Employee].[CertNo],[New_EmployeeID_Xref].[EmployeeID] FROM [New_Employee]
        INNER JOIN [New_EmployeeID_Xref]
            ON [New_Employee].CertNo = [New_EmployeeID_Xref].CertNo";
$stmt = sqlsrv_query( $conn, $tsql);
if( $stmt === false )
{
     echo "Error in executing query68.</br>";
     die( print_r( sqlsrv_errors(), true));
}
else {
    while($row = sqlsrv_fetch_array($stmt)){
    	$all_certid[] = $row['CertNo'];
        $all_empid[] = $row['EmployeeID'];
    }
}
sqlsrv_free_stmt($stmt);



///////////////////////////////////////////////////////////////////

$pdf = new myPDF('L','mm','A4');
for ($i=0; $i < count($all_certid); $i ++) {
	$certid = $all_certid[$i];
    $empid = $all_empid[$i];
    $ldap_info = getInfo($empid);
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->generate($conn);
}
sqlsrv_close($conn);
$name = "LACounty".$year."_AnnualTraining.pdf";
$pdf->Output($name,'D');
?>
