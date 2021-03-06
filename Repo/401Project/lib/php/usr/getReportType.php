<?php
/*
This Code retrieves available reports related information in the report table of the database
@ Yining Huang
*/
include_once "../constants.php";
include_once "../session.php";
// session_start();

/* Access Database here */
$serverName = SQL_SERVER_NAME;
$uid = SQL_SERVER_USERNAME;
$pwd = SQL_SERVER_PASSWORD;
$db = SQL_SERVER_MASTERDATABASE;
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


$tsql = "SELECT * FROM [ReportType]";
$stmt = sqlsrv_query( $conn, $tsql);
if( $stmt === false )
{
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
