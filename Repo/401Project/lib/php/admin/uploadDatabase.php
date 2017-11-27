<?php
/*
	To handle upload requests by admin
	@ Yining Huang
*/
	// echo "Going inside Upload.php";
	// if(isset($_POST["submit"])) {
	include_once "../constants.php";

		$files = $_FILES["fileToUpload"];
		$effectiveDate = $_GET['Date'];
		$note = $_GET['Note'];
		$note = str_replace("'", "''", $note);

		if  (count($files["name"])==0) {
			echo "Please select files to upload";
			return;
		} else if (count($files["name"])!=3) {
			echo "Please select 3 files";
			return;
		}
		// } else if (count($files["name"])!=3) {
		// 	echo "Please select 3 files";
		// 	return;
		// }

		$now = new DateTime();
		// $now->format('Y-m-d H:i:s');
		$timestamp = $now->getTimestamp();
		$target_dir = "D:/temp/".$timestamp.'/';
		$target_file = Array();

		for ($i = 0; $i< count($files["name"]); $i++) {
			$FileType = pathinfo($files["name"][$i],PATHINFO_EXTENSION);
			// $File_name = basename("[".$i."]"."BOE".'_'.$timestamp.'.'.$FileType);
			$File_name = basename($files["name"][$i]);
			$target_file[] = $target_dir . $File_name;
	    	// Check if file already exists
			// if (file_exists($target_file[$i])) {
			// 	echo "Sorry, file already exists.";
			// 	$uploadOk = 0;
			// }
			// Check file size
		    if ($files["size"][$i] > 50000000) { // if the file is larger than 50 MB
		    	echo "Sorry, your file is too large.";
		    	return;
		    }
		    // Allow certain file formats
		    if($FileType != "mdb" && $FileType != "xlsx" && $FileType != "csv") {
		    	echo "Sorry, only mdb, xlsx and csv files are allowed.";
		    	return;
		    }

		}


    	$moved_progress = 0;
    	mkdir($target_dir,0700,true);
    	for ($i = 0; $i< count($files["name"]); $i++) {
    		if (move_uploaded_file($files["tmp_name"][$i], $target_file[$i])){
    			$moved_progress ++;
    		}
    	}

    	if ($moved_progress != count($files["name"])) {
    		echo "Sorry, there was an error uploading your file.";
    		return;
    	} else {
    		echo "success:".$target_dir;
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
			if( $conn === false ) {
		    	die( print_r( sqlsrv_errors(), true));
			}
			$tsql = "INSERT INTO [UploadedDatabaseFiles]
					(Timestamp, EffectiveDate, ifCurrentDatabase, Note)
					VALUES
					('".$timestamp."',	'".$effectiveDate."',	'"."0"."',	'".$note."') ";
			$stmt = sqlsrv_query( $conn, $tsql);
			if( $stmt === false ){
				die( print_r( sqlsrv_errors(), true));
			}
			sqlsrv_free_stmt($stmt);
			sqlsrv_close($conn);
    	}
    	return;

?>
