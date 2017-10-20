<?php
include_once "../constants.php";
include_once "../session.php";
//session_start();

$filter_name = $_POST['filter_name'];
$filter_type = $_POST['filter_type'];

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

$tsql = "SELECT DISTINCT FirstName FROM [New_Employee]";
$stmt = sqlsrv_query( $conn, $tsql);
if( $stmt === false )
{
     echo "Error in executing query.</br>";
     die( print_r( sqlsrv_errors(), true));
}
else {
	$filter = array();
    while($row = sqlsrv_fetch_array($stmt)){
    	$filter[] = $row['FirstName'];
    }
    echo json_encode($filter);
}
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);

?>