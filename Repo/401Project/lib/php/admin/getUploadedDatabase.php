<?php
/*
This Code retrieves the newest fiscal year from the database ->  as current fiscal year
@ Yining Huang
*/

include_once "../constants.php";
session_start();
///////////////////////////////////////////////////////////////////
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
     die( print_r( sqlsrv_errors(), true));
}

$year = -1;

$tsql = "SELECT * FROM [UploadedDatabaseFiles] ORDER BY [uploadedTimestamp] DESC";
$stmt = sqlsrv_query( $conn, $tsql);
if( $stmt === false )
{
     die( print_r( sqlsrv_errors(), true));
}
else {
    $result = array();
    while($row = sqlsrv_fetch_array($stmt)){
        $row['EffectiveDate'] = date("Y/m/d",strtotime($row['EffectiveDate']));
        if ($row['ifCurrentDatabase'] == 0)
            $row['ifCurrentDatabase'] = 'No';
        else $row['ifCurrentDatabase'] = 'Yes';
        $result[] = $row;
    }
    echo json_encode($result);
}

?>
