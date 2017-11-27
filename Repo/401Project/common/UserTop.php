<?php
include_once "../lib/php/constants.php";

$date="NA";
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
if( $conn === false ){
     die( print_r( sqlsrv_errors(), true));
}
$tsql = "SELECT [EffectiveDate] FROM [UploadedDatabaseFiles]
        WHERE [ifCurrentDatabase]='TRUE'";
$stmt = sqlsrv_query( $conn, $tsql);
if( $stmt === false ){
     die( print_r( sqlsrv_errors(), true));
}
else {
    $row= sqlsrv_fetch_array($stmt);
    if ($row[0] =="") $row[0]="NA";
    $date = $row[0];
}
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);

echo "<div class='top'>
		<div class='header'>
			<div class='Welcome'>
				<label>Welcome, </label>
				<label>Name</label>
			</div>
			<div id='pageTitle'>
				<h1><strong>Training Record</strong></h1>
				<hr>
				<h3><strong>Los Angeles County Office of the Assessor</strong></h3>
				<br>
				<h4 id='effectiveLabel'>Data effective as of: <span>".$date."</span></h4>
			</div>
			<img src='../BGimg/Logo.png' alt='Logo' width='130px' height='130px'>
		</div>

		<nav class='navigationBar'>
			<a id='homeTab' href='UserHome.php'><i class='fa fa-home' aria-hidden='true'></i> Home</a>
			<a id='faqTab' href='UserFAQ.php'><i class='fa fa-question-circle-o' aria-hidden='true'></i> FAQs</a>
			<a id='issueTab' href='#'><i class='fa fa-life-ring' aria-hidden='true'></i> <strong>Support</strong></a>
		</nav>
	</div>";
?>
