<?php
include_once "constants.php";
include_once "session.php";
//session_start();


//*******************************************************************
// For Testing purposes, Id here is hard-coded"
$certid = getUserID();

//*******************************************************************8

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

$tsql = "SELECT * FROM [AnnualReq] WHERE CertNo=".(string)$certid;
$stmt = sqlsrv_query( $conn, $tsql);
if( $stmt === false )
{
     echo "Error in executing query.</br>";
     die( print_r( sqlsrv_errors(), true));
}
else {
	$all_years = array();
    while($row = sqlsrv_fetch_array($stmt)){
    	$all_years[] = $row['FiscalYear'];
    }
    echo json_encode($all_years);
}

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);


?>
