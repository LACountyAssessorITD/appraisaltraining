<?php
/*
    Dynamically query the database, fill in the filter dropdown on AdminHome.php
     and gets distinct values for each requested columns in table
    @ Yining Huang
*/
include_once "../constants.php";
include_once "../session.php";
session_start();

$filter_name = $_POST['filter_name'];
$filter_table = $_POST['filter_type'];

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

if ($filter_name == "Name") {
    $tsql = "SELECT FirstName,LastName FROM ".$filter_table;
}
else {
    $tsql = "SELECT DISTINCT ".$filter_name." FROM ".$filter_table;
}
$stmt = sqlsrv_query( $conn, $tsql);
if( $stmt === false )
{
     echo "Error in executing query.</br>";
     die( print_r( sqlsrv_errors(), true));
}
else {
	$filter = array();
    while($row = sqlsrv_fetch_array($stmt)){
        if ($filter_name == "Name") {
            $filter[] = $row["LastName"]." ,  ".$row["FirstName"];
        }
        else {
            $filter[] = $row[$filter_name];
        }

    }
    echo json_encode($filter);
}
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);

?>