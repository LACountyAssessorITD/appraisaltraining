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

$tsql = "CREATE TABLE ReportType (ReportType varchar(50),YearInputType int,PhpFileName varchar(50),
        Description varchar(255));";
$tsql += "INSERT INTO ReportType VALUES ('Specific Year',1, 'Report_userSpecificYear.php', 
            'Generate report for a specific fiscal year');";
$tsql += "INSERT INTO ReportType VALUES ('Completed Courses',2,'Report_userCompletedCourse.php', 
            'Generate report for all completed courses within a year range');";
$tsql += "INSERT INTO ReportType VALUES ('Annual Total Summary',2,'Report_userAnnualTotals.php', 
            'Generate report for annual total summary based on BOE record');";

$stmt = sqlsrv_query( $conn, $tsql);

if( $stmt === false )
{
     echo "Error in executing query.</br>";
     die( print_r( sqlsrv_errors(), true));
}
else {
    echo "done";
}

sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);


?>
