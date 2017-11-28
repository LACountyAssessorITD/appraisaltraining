<?php
/*
	To handle upload filtering process according to what are selected by admin
	@ Yining Huang
*/
include_once "../constants.php";
include_once "../session.php";
session_start();

$query = $_POST['query'];
$year = $_POST['current_fiscal_year'];

/* Access Database here */
$serverName = SQL_SERVER_NAME;
$uid = SQL_SERVER_USERNAME;
$pwd = SQL_SERVER_PASSWORD;
$db = SQL_SERVER_LACDATABASE;
$master_db = SQL_SERVER_MASTERDATABASE;
$connectionInfo = array( "UID"=>$uid,
                         "PWD"=>$pwd,
                         "Database"=>$db,
             "ReturnDatesAsStrings"=>true);  // convert datetime to string

/* Connect using SQL Server Authentication. */
$conn = sqlsrv_connect( $serverName, $connectionInfo);
if( $conn === false ) {
	echo "Unable to connect.</br>";
	die( print_r( sqlsrv_errors(), true));
}

$year_string = (string)$year . "-" . (string)($year+1);
$query = str_replace("Employee", "a", $query);
$query = str_replace("EmployeeID_Xref", "b", $query);
$query = str_replace("CertHistory", "c", $query);
$tsql = "SELECT a.CertNo, a.FirstName, a.LastName, b.EmployeeID, c.CarryForwardTotal
  FROM ".$db.".dbo.Employee a
  INNER JOIN ".$master_db.".dbo.EmployeeID_Xref b
	ON a.CertNo = b.CertNo
  INNER JOIN ".$db.".dbo.CertHistory c
	ON a.CertNo = c.CertNo
  WHERE (c.CertYear = '".$year_string."')".$query;

$stmt = sqlsrv_query($conn, $tsql);
if( $stmt === false ) {
	echo "Error in executing query.</br>";
	die( print_r( sqlsrv_errors(), true));
}
else {
	$results = array();
	while($row = sqlsrv_fetch_array($stmt)){
		$results[] = $row;
    }
    echo json_encode($results);
}
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>
