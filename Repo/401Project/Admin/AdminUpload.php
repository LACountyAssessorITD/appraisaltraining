<!DOCTYPE html>
<!DOCTYPE html>
<html>
<head>
	<title>LAC Website</title>
	<link rel="stylesheet" href="AdminUploadStyle.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
	<script src="AdminUploadJS.js"></script>
	<script type="text/javascript">
		function restore_db(db) {
			alert(db + "clicked");
		}
	</script>
</head>

<body>
	<div class="top">
		<div class="header">
			<div class="Welcome">
				<label>Welcome, </label>
				<label>Name</label>
			</div>
			<h1><strong>Training Record</strong></h1>
			<hr>
			<h3><strong>Los Angeles County Office of the Assessor</strong></h3>
			<img src="../BGimg/Logo.png" alt="Logo" width="130px" height="130px">
		</div>

		<div class="navigationBar">
			<a id="homeTab" href="AdminHome.html"><img src="../iconImg/homeicon.png" alt="homeicon" width="20px" height="20px"> Home</a>
			<a id="uploadTab" href="#"><img src="../iconImg/uploadicon.png" alt="updateicon" width="20px" height="20px"> Update</a>
			<a id="uploadTab" href="AdminFAQ.html"><img src="../iconImg/faqicon.png" alt="faqicon" width="20px" height="20px"> FAQs</a>
			<a id="issueTab" href="#"><img src="../iconImg/issueicon.png" alt="issueicon" width="20px" height="20px"> <strong>Cherwell</strong></a>
		</div>
	</div>

	<div class="content">
		<!-- <div class="upload">
			<h1 id="UploadTitle">Update</h1>
			<h3>Select File to Upload</h3>
			<button id="chooseComputer">Choose From Computer</button>
			<br>

			<p>File Chosen: new_file.mdb</p>

			<button id="uploadButton">Update</button>

			<div class="uploadStatus">
				<h2>Upload Status</h2>
				<h1 id="uploadStatusResult">Successful</h1>
			</div>

			<div class="errorDisplay">
				<label>Error: <span class="errorMsg">Wrong file type</span></label>
			</div>
		</div> -->

		<div class="selectCont" id="accordion">

			<p>Update New</p>
			<div class="accordionCont">
				<h2>Select databse file to upload:</h2>
				<form action="" method="post" enctype="multipart/form-data">
				    <input type="file" name="fileToUpload" id="fileToUpload">
				    <input type="submit" value="Upload file" name="submit">
				</form>
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
				        	echo '<script>alert("File Uploaded!")</script>';
				            echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
				            echo " Renamed as ".basename("BOE".'_'.time().$FileType);
				            //echo '<script>alert("File Uploaded!")</script>';
				        } else {
				            echo "Sorry, there was an error uploading your file.". $_FILES["fileToUpload"]['error'];
				        }
				    }
				}
				?>
			</div>

			<p>Restore Previous</p>
			<div class="accordionCont">
				<h2>兩行字</h2>
				<h3>兩行字</h3>
				<?php
					if ($handle = opendir("D:/temp/")) {
						echo "<table>";
					    while (false !== ($entry = readdir($handle))) {
					        if ($entry != "." && $entry != "..") {
					        	echo "<tr>";
					            echo "<td>"."$entry"."</td>";
					            echo '<td>'.'<input type="button" value="click me" onclick="restore_db('."'$entry'".')"'.'/></td>';
					            echo "</tr>";
					        }
					    }
					    closedir($handle);
					    echo "</table>";
					}
				?>
			</div>

		</div>

	</div>

	<div class="footer">
		<p class="links">
			<span id="contactUs"><a href="#">Contact Us</a></span>
			<span id="Disclaimer"><a href="#">Disclaimer</a></span>
			<span id="FAQs"><a href="#">FAQs</a><span></span>
		</p>
		<p>2017 - Present © Los Angeles County Office of the Assessor</p>
	</div>
</body>

</html>