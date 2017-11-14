<?php
/*
	To handle upload requests by admin
	@ Yining Huang
*/
	if(isset($_POST["submit"])) {

		$files = $_FILES["fileToUpload"];
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
		$uploadOk = 1;
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
		    	$uploadOk = 0;
		    }
		    // Allow certain file formats
		    if($FileType != "mdb" && $FileType != "xlsx" && $FileType != "csv") {
		    	echo "Sorry, only mdb, xlsx and csv files are allowed.";
		    	$uploadOk = 0;
		    }

		}

		// Check if $uploadOk is set to 0 by an error
	    if ($uploadOk == 0) {
	    	echo "\nSorry, your file was not uploaded.";
	    // if everything is ok, try to upload file
	    } else {
	    	$moved_progress = 0;
	    	mkdir($target_dir,0700,true);
	    	for ($i = 0; $i< count($files["name"]); $i++) {
	    		if (move_uploaded_file($files["tmp_name"][$i], $target_file[$i])){
	    			$moved_progress ++;
	    		}
	    	}

	    	if ($moved_progress != count($files["name"])) {
	    		echo "\nSorry, there was an error uploading your file.";
	    	} else {
	    		$message = "file uploaddded";
	    		echo '<script>alert("File Uploaded!!!!!");</script>';
	    	}

	    	/*
	    	if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
	        	// reloadPage();
	        	// header("Location:AdminUpload.php");
	        	// header("Location:AdminHome.html");
	    		$message = "file uploaddded";
	    		echo '<script>alert("File Uploaded!!!!!");</script>';
	    		echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
	    		echo " Renamed as ".$File_name;
	            //echo '<script>update_confirm("'.$target_file.'"");</script>';
	    		echo '<script type="text/javascript">',
	    		'update_confirm('.$target_file.');',
	    		'</script>'
	    		;
	    		echo '<script>
	    		if(confirm("Do you want to update the db file now?")){
	    			alert("Start updating!");
	    			$.ajax({
	    				url:"../lib/php/admin/updateDB.php",
	    				type: "POST",
	    				data: {
	    					db:db
	    				},
	    				dataType: "json",
	    				success:function(results){
	    					alert("Finish Updating!");
	    				},
	    				error: function(xhr, status, error){
	    					alert("Fail to connect to the server when trying to submit update request");
	    				}
	    			});
	    		}
	    		</script>';
	        	// echo "<script>setTimeout('header('Location:AdminUpload.php')');</script>";
	            //echo '<script>alert("File Uploaded!")</script>';
	    	} else {
	    		echo "Sorry, there was an error uploading your file.". $_FILES["fileToUpload"]['error'];
	    	}
	    	*/
	    }


}
?>
