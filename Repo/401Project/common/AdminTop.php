<?php
include_once "../lib/php/session.php";
include_once "../lib/php/constants.php";
// redirect_onAdminPage();

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
    if ($row[0] =="") 
    	$date = "NA";
    else 
    	$date = date("Y/m/d",strtotime($row[0]));
}
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);

// redirect_onAdminPage();
echo "<div class='top'>
		<div class='header'>
			<div class='Welcome'>
				<label>Welcome, </label>
				<label>".$_SESSION['FIRSTNAME']."</label>
				<br>
			</div>
			<h1><strong>Training Record</strong></h1>
			<hr>
			<h3><strong>Los Angeles County Office of the Assessor</strong></h3>
			<br>
			<h4 id='effectiveLabel'>Data effective as of: <span>".$date."</span></h4>
			<img src='../BGimg/Logo.png' alt='Logo' width='130px' height='130px'>
		</div>

		<nav class='navigationBar'>
			<a id='homeTab' href='AdminHome.php'><i class='fa fa-home' aria-hidden='true'></i> Home</a>
			<a id='updateTab' href='AdminUpload.php'><i class='fa fa-upload' aria-hidden='true'></i> Update</a>
			<a id='faqTab' href='AdminFAQ.php'><i class='fa fa-question-circle-o' aria-hidden='true'></i> FAQs</a>
			<a id='issueTab' href='#'><i class='fa fa-life-ring' aria-hidden='true'></i> <strong>Support</strong></a>
		</nav>
	</div>";
?>


