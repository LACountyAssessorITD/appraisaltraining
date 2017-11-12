<?php
include_once "../constants.php";
include_once "../session.php";

$query = $_POST['query'];

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
if( $conn === false ) {
	echo "Unable to connect.</br>";
	die( print_r( sqlsrv_errors(), true));
}
// $tsql = "SELECT * FROM [New_Employee]"." WHERE ".(string)$query; // This is where hardcoded!!!!!!

if ($query != "") {
	$query = " AND (".$query.")";
}

$tsql = "
SELECT [New_Employee].CertNo, [New_Employee].FirstName, [New_Employee].LastName, [New_EmployeeID_Xref].EmployeeID, [New_CertHistory].CurrentYearBalance
  FROM [New_Employee]
  INNER JOIN [New_EmployeeID_Xref] 
	ON [New_Employee].CertNo = [New_EmployeeID_Xref].CertNo
  INNER JOIN [New_CertHistory] 
	ON [New_Employee].CertNo = [New_CertHistory].CertNo
  WHERE ([New_CertHistory].CertYear = '".2017-2018."')".
  	" AND ".$query;

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