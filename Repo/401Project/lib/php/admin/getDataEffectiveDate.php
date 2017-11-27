<?php
/*
This Code retrieves the newest fiscal year from the database ->  as current fiscal year
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
$tsql = "SELECT [EffectiveDate] FROM [UploadedDatabaseFiles]        
        WHERE [ifCurrentDatabase]=TRUE";
$stmt = sqlsrv_query( $conn, $tsql);
if( $stmt === false )
{
     echo "Error in executing query68.</br>";
     die( print_r( sqlsrv_errors(), true));
}
else {
    $row= sqlsrv_fetch_array($stmt);
    if ($row[0] =="") $row[0]="NA";
    echo $row[0];
}

?>