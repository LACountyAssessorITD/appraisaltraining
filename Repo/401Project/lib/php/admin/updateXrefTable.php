<?php
/*
This Code update Employee_Xref table for admin (Incomplete)
@ Yining Huang
*/
include_once "../constants.php";
include_once "../session.php";

$employeeIDStr = $_POST['employeeIDStr'];
$certNoStr = $_POST['certNoStr'];

/* Access Database here */
$serverName = SQL_SERVER_NAME;
$uid = SQL_SERVER_USERNAME;
$pwd = SQL_SERVER_PASSWORD;
$master_db = SQL_SERVER_MASTERDATABASE;
$connectionInfo = array( "UID"=>$uid,
                         "PWD"=>$pwd,
                         "Database"=>$master_db,
             "ReturnDatesAsStrings"=>true);  // convert datetime to string

/* Connect using SQL Server Authentication. */
$conn = sqlsrv_connect( $serverName, $connectionInfo);
if( $conn === false )
{
     echo "Unable to connect.</br>";
     die( print_r( sqlsrv_errors(), true));
}

$tsql = "TRUNCATE TABLE ".$master_db.".dbo.EmployeeID_Xref";
$stmt = sqlsrv_query( $conn, $tsql);
if( $stmt === false )
{
     echo "Error in dropping database.</br>";
     die( print_r( sqlsrv_errors(), true));
}
else {
	$result = array();
    while($row = sqlsrv_fetch_array($stmt)){
        $result[] = $row;
    }
    echo json_encode($result);
}
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);

?>
