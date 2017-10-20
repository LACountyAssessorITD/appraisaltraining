<?php
include_once "../constants.php";
include_once "../session.php";
//session_start();

$filter_name = $_POST['filter_name'];
if ($filter_name == "CertNo" or "FirstName" or "LastName" or "CurrentStatus" or "Auditor"){
    $filter_table = "[New_Employee]";
} else if ($filter_name == "FiscalYear") {
    $filter_table = "[New_CourseDetail]";
} else if ($filter_name == "CurrentYearBalance" or "Status") {
    $filter_table = "[New_CertHistory]";
}

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

$tsql = "SELECT DISTINCT ".$filter_name." FROM ".$filter_table;
$stmt = sqlsrv_query( $conn, $tsql);
if( $stmt === false )
{
     echo "Error in executing query.</br>";
     die( print_r( sqlsrv_errors(), true));
}
else {
	$filter = array();
    while($row = sqlsrv_fetch_array($stmt)){
    	$filter[] = $row['CertNo'];
    }
    echo json_encode($filter);
}
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);

?>