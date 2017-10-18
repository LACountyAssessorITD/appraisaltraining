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

		<div class="navigationBar">
			<a id="homeTab" href="AdminHome.html"><img src="../iconImg/homeicon.png" alt="homeicon" width="20px" height="20px"> Home</a>
			<a id="uploadTab" href="#"><img src="../iconImg/uploadicon.png" alt="updateicon" width="20px" height="20px"> Update</a>
			<a id="uploadTab" href="AdminFAQ.html"><img src="../iconImg/faqicon.png" alt="faqicon" width="20px" height="20px"> FAQs</a>
			<a id="issueTab" href="#"><img src="../iconImg/issueicon.png" alt="issueicon" width="20px" height="20px"> <strong>Support</strong></a>
		</div>
	</div>

	<div class="content">
		

		<div class="buttonGroup">
			<button id="uploadSegCtrl">Upload</button>
			<button id="restoreSegCtrl">Restore</button>
		</div>

		<!-- <div class="selectCont" id="accordion"> -->

		<div class="upload">
			<h1 id="UploadTitle">Update</h1>
			<h3>Select File to Upload</h3>
			<form action="../lib/php/admin/AdminUploadPHP.php" method="post" enctype="multipart/form-data" id="uploadForm" target="myFrame">
				<label>
		    		<input type="file" name="fileToUpload" id="fileToUpload">
		    		<span id="uploadBtn">Choose yo file</span>
		    	</label>
		    	<input id="chosenFile" placeholder="Chosen File" disabled="disabled" />
			    <input type="submit" value="Upload file" name="submit" id="submitNewBtn">
			</form>

			<iframe name="myFrame"></iframe>

			<div class="uploadStatus">
				<h2>Upload Status</h2>
				<h1 id="uploadStatusResult">Successful</h1>
			</div>

			<div class="errorDisplay">
				<label>Error Log:</label>
				<iframe></iframe>
			</div>

		</div>

		<div class="restore">
			<h3>Restore Previous</h3>
			<div class="accordionCont">
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
		<!-- </div> -->

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