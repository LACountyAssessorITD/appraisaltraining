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
    	//echo $IDs;
		include_once "myPDF.php";
		$_SESSION["current"] = 0;
		$size = sizeof($IDs);
		$data = 0;
		foreach ($IDs as &$value) {
			$_SESSION["id"] = $value;
			$_SESSION["year"] = 2016;

			//ob_start(); // begin collecting output
			include 'generatePDFandemail.php';
			// $result = ob_get_clean();
			// if ($result == "success")
				$data ++;
			//echo '<script type="text/javascript">parent.moveProgress('.$data.','.$size.')</script>';
			//echo "<script type=\"text/javascript\">parent.document.getElementById( 'foo').innerHTML += 'Line $i<br />';</script>";
			// flush();
			// ob_flush();
			sleep(1);
		}
    }
?>
