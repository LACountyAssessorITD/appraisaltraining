<?php
include_once "../constants.php";
include_once "../session.php";
//session_start();

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

$certid = getCertNo();

$tsql = "SELECT * FROM [New_CertHistory] WHERE CertNo=".(string)$certid." Order By [CertYear] DESC";
$stmt = sqlsrv_query( $conn, $tsql);
if( $stmt === false )
{
     echo "Error in executing query68.</br>";
     die( print_r( sqlsrv_errors(), true));
}
else {
    $row= sqlsrv_fetch_array($stmt);
    $balance = $row['CarryForwardTotal'];
    echo (int)$balance;
}

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);


?>
