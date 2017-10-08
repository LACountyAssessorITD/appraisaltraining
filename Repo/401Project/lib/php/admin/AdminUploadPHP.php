<?php
	if(isset($_POST["submit"])) {
		if ($_FILES["fileToUpload"]["name"] == "") {
			echo "Please select a file";
			//return;
		}
	    $target_dir = "D:/temp/";
	    $FileType = pathinfo($_FILES["fileToUpload"]["name"],PATHINFO_EXTENSION);
	    $target_file = $target_dir . basename("BOE".'_'.time().'.'.$FileType);
	    $uploadOk = 1;
	    // $uploadSuccess = 0;


	    // Check if file already exists
	    if (file_exists($target_file)) {
	        echo "Sorry, file already exists.";
	        $uploadOk = 0;
	    }
		// Check file size
	    if ($_FILES["fileToUpload"]["size"] > 50000000) { // if the file is larger than 50 MB
	        echo "Sorry, your file is too large.";
	        $uploadOk = 0;
	    }
	    // Allow certain file formats
	    if($FileType != "mdb" && $FileType != "xlsx" && $FileType != "csv") {
	        echo "Sorry, only mdb, xlsx and csv files are allowed.";
	        $uploadOk = 0;
	    }
	    // Check if $uploadOk is set to 0 by an error
	    if ($uploadOk == 0) {
	        echo "Sorry, your file was not uploaded.";
	    // if everything is ok, try to upload file
	    } else {
	        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
	        	// reloadPage();
	        	// header("Location:AdminUpload.php");
	        	// header("Location:AdminHome.html");
	        	$message = "file uploaddded";
	        	echo '<script>alert("File Uploaded!!!!!")</script>';
	            echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
	            echo " Renamed as ".basename("BOE".'_'.time().'.'.$FileType);
	            echo "<?php update_table();?>";
	        	// echo "<script>setTimeout('header('Location:AdminUpload.php')');</script>";
	            //echo '<script>alert("File Uploaded!")</script>';
	        } else {
	            echo "Sorry, there was an error uploading your file.". $_FILES["fileToUpload"]['error'];
	        }
	    }

	}
?>