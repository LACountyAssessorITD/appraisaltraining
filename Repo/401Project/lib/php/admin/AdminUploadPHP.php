<?php
/*
	To handle upload requests by admin
	@ Yining Huang
*/
	if(isset($_POST["submit"])) {
		if  (count($_FILES["fileToUpload"]["name"])==0) {
			echo "alert('Please select files to upload')";
			return;
		} else if (count($_FILES["fileToUpload"]["name"])!=3) {
			echo "alert('Please select 3 files')";
			return;
		}
	    $target_dir = "D:/temp/";
	    $FileType = pathinfo($_FILES["fileToUpload"]["name"],PATHINFO_EXTENSION);
	    $File_name = basename("BOE".'_'.time().'.'.$FileType);
	    $target_file = $target_dir . $File_name;
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
	    }

	}
?>