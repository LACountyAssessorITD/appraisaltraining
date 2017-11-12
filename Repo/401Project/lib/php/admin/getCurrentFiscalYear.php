<?php
/*
This Code dynamically generate individual PDF (Specific Year Report)
@ Yining Huang
*/

include_once "../constants.php";
include_once "../session.php";
session_start();
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

$year = -1;

$tsql = "SELECT MAX(CertYear) FROM [New_CertHistory]";
$stmt = sqlsrv_query( $conn, $tsql);
if( $stmt === false )
{
     echo "Error in executing query68.</br>";
     die( print_r( sqlsrv_errors(), true));
}
else {
    $row= sqlsrv_fetch_array($stmt);
    $year = $row[0];
    $year = substr($year,0,4);
    $year = (int)$year;
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
    $_SESSION['current_fiscal_year'] = $year;
    echo $year;
}

?>
