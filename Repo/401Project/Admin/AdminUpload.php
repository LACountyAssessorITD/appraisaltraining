<?php session_start(); ?>
<!DOCTYPE html>
<!DOCTYPE html>
<html>
<head>
	<title>LAC Website</title>
	<link rel="stylesheet" href="AdminUploadStyle.css">
	<link rel="stylesheet" href="../CSS/DefaultUI.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
	<script src="AdminUploadJS.js"></script>
	<script type="text/javascript">
		function restore_db(db) {
			alert(db + "clicked");
		}
		function update_confirm(db) {
		    var r = confirm("Do you want to update the db file now?");
		    if (r == true) {
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

		<nav class="navigationBar">
			<a id="homeTab" href="AdminHome.php"><i class="fa fa-home" aria-hidden="true"></i> Home</a>
			<a id="uploadTab" href="#"><i class="fa fa-upload" aria-hidden="true"></i> Update</a>
			<a id="uploadTab" href="AdminFAQ.php"><i class="fa fa-question-circle-o" aria-hidden="true"></i> FAQs</a>
			<a id="issueTab" href="#"><i class="fa fa-life-ring" aria-hidden="true"></i> <strong>Support</strong></a>
		</nav>
	</div>

	<div class="content">


		<div class="buttonGroup">
			<button id="uploadSegCtrl">Upload</button>
			<button id="restoreSegCtrl">Restore</button>
		</div>

		<!-- <div class="selectCont" id="accordion"> -->

		<div class="upload">
			<h1 id="UploadTitle">Update</h1>
			<div id="chooseFileDiv">
				<h3>Select File to Upload</h3>
				<form action="../lib/php/admin/AdminUploadPHP.php" method="post" enctype="multipart/form-data" id="uploadForm" target="myFrame">
	    			<input type="file" name="fileToUpload" id="fileToUpload">
	    			<div class="clearRow">
			    		<button id="chooseFileBtn">Choose File</button>
			    	</div>
			    	<p id="chosenFileLabel">File Chosen:</p>
			    	<p id="chosenFileName">None</p>
				    <input type="submit" value="Upload file" name="submit" id="submitNewBtn">
				</form>
			</div>

			<div id="statusDiv">
				<div class="uploadStatus">
					<h2>Upload Status</h2>
					<h2 id="uploadStatusResult"></h2>
				</div>

				<div class="errorDisplay">
					<div class="clearRow">
						<label>Error Log:</label>
					</div>
					<iframe name="myFrame"></iframe>
				</div>
			</div>

		</div>

		<div class="restore">
			<h3>Restore Previous</h3>
			<div id="restoreTableDiv">
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

	<?php include '../commonPHP/Footer.php'; ?>
</body>

</html>