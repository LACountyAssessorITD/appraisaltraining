<?php session_start();
// include_once "../lib/php/session.php";
 // redirect_onAdminPage();
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
	<title>LAC Website</title>
	<link rel="stylesheet" href="AdminHomeStyle.css">
	<link rel="stylesheet" href="../CSS/splashStyle.css">
	<link rel="stylesheet" href="../CSS/DefaultUI.css">
  	<link rel="stylesheet" href="font-awesome-animation.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

  	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

	<script src="AdminHomeJS.js"></script>

</head>

<body>

	<div id="splashScreen">
		<div id="splash">
			<div id="lineOne"></div>
			<div id="lineTwo"></div>
			<h1>Training Records</h1>
		</div>
		<img src="../BGimg/Logo.png" alt="Logo" width="130px" height="130px">
		<iframe class="cover"></iframe>
	</div>

	<?php include "../common/AdminTop.php"; ?>

	<div class="content">

		<div id="filterBackground"></div>
		<div class="leftContentColumn">

			<div>
				<h3 id="filterLabel" class="banLabel">Filters</h3>
			</div>

			<button class="resetAll" id="resetAllBtn"><i class="fa fa-times" aria-hidden="true"></i> Reset All Filters</button>

			<div class="filterListCol" id="accordion">

				<!-- <p class="pFiltersLabel">Employee Info</p> -->

				<div class="employeeFilters" name="[New_Employee]">

					<div class="dropDownFilter">
						<button class="dropDownBtn" name="Name">Full Name</button>
					</div>

					<div class="dropDownFilter">
						<button class="dropDownBtn" name="CertNo">Certification Number</button>
					</div>

					<div class="dropDownFilter">
						<button class="dropDownBtn" name="TempCertDate">Temporary Certification Date</button>
					</div>

					<div class="dropDownFilter">
						<button class="dropDownBtn" name="PermCertDate">Permanent Certification Date</button>
					</div>

					<div class="dropDownFilter">
						<button class="dropDownBtn" name="AdvCertDate">Advanced Certification Date</button>
					</div>

					<div class="dropDownFilter">
						<button class="dropDownBtn" name="CurrentStatus">Certification Status</button>
					</div>

					<div class="dropDownFilter">
						<button class="dropDownBtn" name="Auditor">Auditor</button>
					</div>
				</div>

				<div class="employeeFilters" name="[New_CertHistory]">
					<!-- <div class="dropDownFilter">
						<button class="dropDownBtn" name="Status">Employment Status</button>
					</div> -->
	                <!--  Flter for advanced certification progess -->
	                <!-- TO DO -->

				</div>

			</div>


			<div id="filterApplyBtn">
				<button>Apply Filter</button>
			</div>

		</div>

		<div class="rightContent">

			<div class="filterOverview">
				<h3 id="overviewLabel" class="banLabel">Filtered Results</h3>
				<div id="reportType">
					<div id="reportTypeLabel"><label >Select Report Type</label></div>
					<select id="reportTypeSelect"></select>
					<i class='fa fa-question-circle-o toolTipParent' aria-hidden='true'><span class="toolTip"></span></i>
					<a id="downloadLink" href="../lib/php/admin/report/Download_ALL/DownloadReport_allUsersCurrentYearReports.php" target="_blank">
						<button class="downloadBtn" id="downloadSelected">
							<i class="fa fa-download" aria-hidden="true"></i> Download All Current Year Report
						</button>
					</a>
				</div>
				<!-- <button class="resetAll" id="resetAllTable"><i class="fa fa-times" aria-hidden="true"></i> Reset Selections</button> -->
				<div id='hoursAndTableSizeDiv'>
					<div id="hoursShortDiv">
						<label>Hours Short:</label>
						<label><input type='checkbox' name="zero"><span> 0</span></label>
						<label><input type='checkbox' name="one"><span> 1-5</span></label>
						<label><input type='checkbox' name="six"><span> 6-10</span></label>
						<label><input type='checkbox' name="eleven"><span> 11-15</span></label>
						<label><input type='checkbox' name="sixteen"><span> >15</span></label>
					</div>
					<div id="tableSizeDiv">
						<label>Filtered: <span id="tableSizeSpan">0</span> Selected: <span id="selectedSizeSpan">0</span></label>
					</div>
				</div>
				<div id="overviewTableDiv">
					<table id="overviewTable">
						<thead>
				            <tr>
				                <th width="5%"><input type='checkbox' name='tableSelectAll'></th>
				                <th width="5%">Info</th>
				                <th width="20%">Name <button id="nameSort"><i class="fa fa-sort" aria-hidden="true"></i></button></th>
				                <th width="20%">Hours Short <button id="hoursSort"><i class="fa fa-sort" aria-hidden="true"></i></button></th>
				                <th width="35%">Year</th>
				                <th width="15%">View</th>
				            </tr>
				        </thead>
				        <tbody>
				        	<!-- <tr id="overviewSelectAll">
				        		<td><input type='checkbox' name='tableSelectAll'></td>
				        		<td colspan="5">Select All</td>
				        	</tr> -->
				        </tbody>
					</table>
				</div>

				<div class="rightContentColumn">
					<button id="EmailAll"><i class="fa fa-envelope" aria-hidden="true"></i> Email</button>
					<!-- <div id="optionTabDiv"> -->
						<div id="emailDiv">
							<p>Subject</p>
							<textarea id="emailSubjectTA" placeholder="Email Subject..." rows="1"></textarea>
							<p>Content</p>
							<textarea id="emailContentTA" placeholder="Email content..."></textarea>
							<div id="sendEmailBtnDiv">
								<div class="toolTipParent">
									<button id="sendEmailSelectedBtn"><i class="fa fa-paper-plane" aria-hidden="true"></i> Send Selected</button><span class="toolTip">Send email to the appraisers selected in the Result Table</span>
								</div>
							</div>
						</div>
					<!-- </div> -->
				</div>
			</div>

			<div class="viewAndOption">

				<div class="midColumn">
					<div>
						<h3 id="previewLabel" class="banLabel">Preview Report</h3>
					</div>
					<div class="pdfView">
						<div id="infoLabelWrap">
							<!-- <h3 id="effectiveLabel" class="infoLabel">Effective as of: <span></span></h3> -->
							<h3 id="hoursNeededLabel">Hours needed: <span></span></h3>
							<a id="downloadLink" href="../lib/php/admin/report/downloadCommunicator.php" target="_blank">
								<button class="downloadBtn" id="downloadCurrent">
									<i class="fa fa-download" aria-hidden="true"></i> Download Previewing Report
								</button>
							</a>
						</div>

						<!-- <object id="pdfBox" data="../LACLogo.pdf" type="application/pdf" width="100%" height="600px">
							<p>It appears you don't have Adobe Reader or PDF support in this web browser.
							<a href="singleUserReport.php">Click here to download the PDF</a>. Or
							<a href="http://get.adobe.com/reader/" target="_blank">click here to install Adobe Reader</a>.</p>
							<embed src="../LACLogo.pdf" type="application/pdf">
						</object> -->
					</div>
				</div>



			</div>


		</div>
	</div>

	<?php include '../common/Footer.php'; ?>

</body>

</html>
