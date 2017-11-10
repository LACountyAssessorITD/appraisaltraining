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

	<?php include "../common/AdminTop.php"; ?>

	<div class="content">


		<div class="buttonGroup">
			<button id="uploadSegCtrl">Upload</button>
			<button id="restoreSegCtrl">History</button>
			<button id="xrefSegCtrl">Xref</button>
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
			<h3>Past Files</h3>
			<div id="restoreTableDiv">
				<table>
					<thead>
						<tr>
							<th>File</th>
							<th>Save</th>
						</tr>
					</thead>
				<?php
					if ($handle = opendir("D:/temp/")) {
					    while (false !== ($entry = readdir($handle))) {
					        if ($entry != "." && $entry != "..") {
					        	echo "<tr>";
					            echo "<td>"."$entry"."</td>";
					            // echo '<td>'.'<input type="button" value="click me" onclick="restore_db('."'$entry'".')"'.'/></td>';
					            // echo '<td><button class="saveBtn"><i class="fa fa-floppy-o" aria-hidden="true"></i></button></td>';
					            echo '<td><a id="downloadLink" href="../lib/php/admin/report/downloadCommunicator.php" target="_blank">
											<button class="saveBtn"><i class="fa fa-floppy-o" aria-hidden="true"></i></button>
										</a></td>';
					            echo "</tr>";
					        }
					    }
				    closedir($handle);
					}
				?>
				</table>
			</div>
		</div>

		<div class="xrefDiv">
			<div id="xrefTableDiv">
				<table id="xrefTable">
					<thead>
						<tr>
							<th width="20%">EmployeeID</th>
							<th width="20%">CertNo</th>
							<th width="60%">Edit</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>12345</td>
							<td>23456</td>
							<td>
								<button class='editRowBtn'><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button>
								<div class="editRowDiv">
									<label>EmployeeID</label>
									<input type="text" name="EmployeeIDInput">
									<br>
									<label>CertNo</label>
									<input type="text" name="CertNoInput">
									<br>
									<button>Confirm Edit</button>
								</div>
							</td>
						</tr>
						<tr>
							<td>12345</td>
							<td>23456</td>
							<td>
								<button class='editRowBtn'><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button>
								<div class="editRowDiv">
									<label>EmployeeID</label>
									<input type="text" name="EmployeeIDInput">
									<br>
									<label>CertNo</label>
									<input type="text" name="CertNoInput">
									<br>
									<button>Confirm Edit</button>
								</div>
							</td>
						</tr>
					</tbody>
				</table>	
			</div>
		</div>

	</div>

	<?php include '../common/Footer.php'; ?>
</body>

</html>
