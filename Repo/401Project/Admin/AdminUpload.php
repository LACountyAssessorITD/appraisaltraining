<?php session_start(); ?>
<!DOCTYPE html>
<!DOCTYPE html>
<html>
<head>
	<title>LAC Website</title>
	<link rel="stylesheet" href="AdminUploadStyle.css">
	<link rel="stylesheet" href="../CSS/DefaultUI.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
	<script src="AdminUploadJS.js"></script>
</head>

<body>

	<?php include "../common/AdminTop.php"; ?>

	<div class="content">
		<!-- Progress Bar Items -->
		<div id="progress"></div>
		<div id="message"></div>
		<div class="buttonGroup">
			<button id="uploadSegCtrl">Upload</button>
			<button id="restoreSegCtrl">History</button>
			<button id="xrefSegCtrl">Xref</button>
		</div>

		<!-- <div class="selectCont" id="accordion"> -->

		<div class="upload">
			<!-- <h1 id="UploadTitle">Update</h1> -->
			<div id="chooseFileDiv">
				<h3>Select File to Upload</h3>
				<form id="uploadForm">
	    			<input type="file" name="fileToUpload[]" id="fileToUpload" accept=".xlsx" multiple>
	    			<div class="clearRow">
			    		<button id="chooseFileBtn">Choose File</button>
			    	</div>
			    	<p id="chosenFileLabel">File Chosen: (3 Files)</p>
			    	<p id="chosenFileName">None</p>
			    	<div id="effDateDiv">
			    		<p class="clearRow">Effective Date: </p>
			    		<input type='text' placeholder='yyyy' autocomplete='off' id="yearEffInput">
		                <input type='text' placeholder='mm' autocomplete='off' id="monthEffInput">
		            	<input type='text' placeholder='dd' autocomplete='off' id="dayEffInput">
			            <!-- <button id="effDateBtn">Submit</button> -->
			        </div>
				    <input type="submit" value="Upload file" name="submit" id="submitNewBtn">
				</form>
			</div>

			<div id="statusDiv">
				<!-- <div class="uploadStatus">
					<h2>Upload Status</h2>
					<h2 id="uploadStatusResult"></h2>
				</div> -->


			</div>

		</div>

		<div class="restore">
			<div id="restoreTableDiv">
				<table id="uploadedDatabaseTable">
					<thead>
						<tr>
							<th>Timestamp</th>
							<th>EffectiveDate</th>
							<th>ifCurrentDatabase</th>
							<th>Save</th>
							<th>Note</th>
						</tr>
					</thead>

				</table>
			</div>
		</div>

		<div class="xrefDiv">
			<form id='xrefSearchBar'>
                <input type='text' placeholder='Search..' autocomplete='off'>
            </form>
            <div id="mismatchDiv">
            	<label>Mismatched Pairs: <span id="mismatchCount">0</span></label>
            	<div id='mismatchListWrap'>
	            	<ul id="mismatchList">
	            	</ul>
	            </div>
            </div>
			<div id="xrefTableDiv">
				<table id="xrefTable">
					<thead>
						<tr>
							<th width="15%">EmployeeID <button class="numSort"><i class="fa fa-sort" aria-hidden="true"></i></button></th>
							<th width="15%">Name <button class="nameSort"><i class="fa fa-sort" aria-hidden="true"></i></button></th>
							<th width="15%">CertNo <button class="numSort"><i class="fa fa-sort" aria-hidden="true"></i></button></th>
							<th width="15%">Name <button class="nameSort"><i class="fa fa-sort" aria-hidden="true"></i></button></th>
							<th width="35%">Edit</th>
							<th width="5%"></th>
						</tr>
					</thead>
					<tbody>
						<!-- <tr>
							<td class="EmployeeIDData">12345</td>
							<td class="CertNoData">23456</td>
							<td>
								<button class='editRowBtn'><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button>
								<div class="editRowDiv">
									<label>EmployeeID</label>
									<input type="text" name="EmployeeIDInput">
									<br>
									<label>CertNo</label>
									<input type="text" name="CertNoInput">
									<br>
									<button class="confirmEditBtn">Confirm Edit</button>
								</div>
							</td>
							<td><button class="deleteRowBtn"><i class="fa fa-times" aria-hidden="true"></i></button></td>
						</tr>
						<tr>
							<td class="EmployeeIDData">12345</td>
							<td class="CertNoData">23456</td>
							<td>
								<button class='editRowBtn'><i class="fa fa-pencil-square-o" aria-hidden="true"></i></button>
								<div class="editRowDiv">
									<label>EmployeeID</label>
									<input type="text" name="EmployeeIDInput">
									<br>
									<label>CertNo</label>
									<input type="text" name="CertNoInput">
									<br>
									<button class="confirmEditBtn">Confirm Edit</button>
								</div>
							</td>
							<td><button class="deleteRowBtn"><i class="fa fa-times" aria-hidden="true"></i></button></td>
						</tr> -->
					</tbody>
				</table>

			</div>

			<div id="insertNewRowDiv">
				<label>EmployeeID:</label>
				<input type="text" name="InsertEmployeeIDInput">
				<label>CertNo:</label>
				<input type="text" name="InsertCertNoInput">
				<button class="insertRowBtn"><span class="spaceSpan"></span>Insert Row</button>
			</div>
		</div>

	</div>

	<?php include '../common/Footer.php'; ?>
</body>

</html>
