<?php
	session_start();
	include_once "constants.php";
	// ---------------------------------------------
	// Access Database here
	$serverName = SQL_SERVER_NAME;
	$uid = SQL_SERVER_USERNAME;
	$pwd = SQL_SERVER_PASSWORD;
	$db = SQL_SERVER_BOEDATABASE;
	$connectionInfo = array( "UID"=>$uid,
	                         "PWD"=>$pwd,
	                         "Database"=>$db,
	             			 "ReturnDatesAsStrings"=>true);  // convert datetime to string

	/* Connect using SQL Server Authentication. */
	$conn = sqlsrv_connect($serverName, $connectionInfo);
	if( $conn === false )
	{
	     echo "Unable to connect.</br>";
	     die( print_r( sqlsrv_errors(), true));
	}

	$tsql = "SELECT * FROM [Summary]";
    $stmt = sqlsrv_query( $conn, $tsql);
    if( $stmt === false )
    {
         echo "Error in executing query.</br>";
         die( print_r( sqlsrv_errors(), true));
    }
    else {
    	$IDs = array();
        while($row = sqlsrv_fetch_array($stmt)){
        	$IDs[] = $row['CertNo'];
        }
        sqlsrv_free_stmt($stmt);
    	sqlsrv_close($conn);
    	header("Content_Type: application/json",true);
    	ob_start();
    	ob_end_clean();

    	echo json_encode($IDs);
	}

?>
