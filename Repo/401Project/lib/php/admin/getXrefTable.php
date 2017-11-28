<?php
/*
This Code retrieves Employee_Xref table in the table to get EmpNo. CertNo and Names
@ Yining Huang
*/
include_once "../constants.php";
include_once "../session.php";
//session_start();

/* Access Database here */
$serverName = SQL_SERVER_NAME;
$uid = SQL_SERVER_USERNAME;
$pwd = SQL_SERVER_PASSWORD;
$db = SQL_SERVER_LACDATABASE;
$master_db = SQL_SERVER_MASTERDATABASE;
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

$tsql = "SELECT [Employee].FirstName, [Employee].LastName, [".$master_db."].[dbo].[EmployeeID_Xref].EmployeeID,[".$master_db."].[dbo].[EmployeeID_Xref].CertNo
        FROM [".$master_db."].[dbo].[EmployeeID_Xref]
        INNER JOIN [Employee]
            ON [Employee].CertNo = [".$master_db."].[dbo].[EmployeeID_Xref].CertNo";
$stmt = sqlsrv_query( $conn, $tsql);
if( $stmt === false )
{
     echo "Error in executing query.</br>";
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
