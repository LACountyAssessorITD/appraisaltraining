<?php session_start(); 
include_once "../lib/php/session.php";;
redirect();
?>
<!DOCTYPE html>
<!DOCTYPE html>
<html lang="en-US">
<head>
	<title>LAC Website</title>
	<link rel="stylesheet" href="AdminHomeStyle.css">
	<link rel="stylesheet" href="../CSS/splashStyle.css">
	<link rel="stylesheet" href="../CSS/DefaultUI.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
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

				<p class="pFiltersLabel">Employee Info</p>

				<div class="employeeFilters" name="[New_Employee]">

					<div class="dropDownFilter">
						<button class="dropDownBtn" name="Name">Full Name</button>
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
						<button class="dropDownBtn" name="CurrentStatus">Current Status</button>
					</div>

					<div class="dropDownFilter">
						<button class="dropDownBtn" name="Auditor">Auditor</button>
					</div>
				</div>

			</div>


			<div id="filterApplyBtn">
				<button>Apply Filter</button>
			</div>

		</div>

		<div class="rightContent">

			<div class="viewAndOption">

				<div class="midColumn">
					<div>
						<h3 id="previewLabel" class="banLabel">Preview Report</h3>
					</div>
					<div class="pdfView">
						<div id="infoLabelWrap">
							<h3 id="effectiveLabel" class="infoLabel">Effective as of: <span></span></h3>
							<h3 id="hoursNeedLabel" class="infoLabel">Hours needed: <span></span></h3>
						</div>

						<object id="pdfBox" data="../LACLogo.pdf" type="application/pdf" width="100%" height="600px">
							<embed src="../LACLogo.pdf" type="application/pdf"></embed>
						</object>
					</div>
				</div>

				<div class="rightContentColumn">
					<div id="buttonTabDiv">
						<button id="Download"><i class="fa fa-download" aria-hidden="true"></i> Download</button>
						<!-- <button id="Print"><i class="fa fa-print" aria-hidden="true"></i> Print</button> -->
						<button id="EmailAll"><i class="fa fa-envelope" aria-hidden="true"></i> Email</button>
					</div>
					<div id="optionTabDiv">
						<div id="emailDiv">
							<p>Subject</p>
							<textarea id="emailSubjectTA" placeholder="Email Subject..." rows="1"></textarea>
							<p>Content</p>
							<textarea id="emailContentTA" placeholder="Email content..."></textarea>
							<div id="sendEmailBtnDiv">
								<div class="toolTipParent">
									<button id="sendEmailSelectedBtn"><i class="fa fa-paper-plane" aria-hidden="true"></i> Send Selected</button><span class="toolTip">Send email to the appraisers selected in the table below</span>
								</div>
							</div>
						</div>
						<div id="downloadDiv"><!--  -->
							<a id="downloadLink" href="../lib/php/admin/report/downloadCommunicator.php" target="_blank">
								<button class="optionBtn" id="downloadCurrent"><i class="fa fa-download" aria-hidden="true"></i>Download Previewing Report</button>
							</a>
							<a id="downloadLink" href="../lib/php/admin/report/Download_ALL/DownloadReport_allUsersCurrentYearReports.php" target="_blank">
								<button class="optionBtn" id="downloadSelected"><i class="fa fa-download" aria-hidden="true"></i>Download County Current Year Report</button>
							</a>
						</div>
					</div>
				</div>



			</div>

			<div class="filterOverview">
				<h3 id="overviewLabel" class="banLabel">Filter Overview</h3>
				<div id="reportType">
					<div id="reportTypeLabel"><label >Select Report Type</label></div>
					<select id="reportTypeSelect"></select>
					<i class='fa fa-question-circle-o toolTipParent' aria-hidden='true'><span class="toolTip"></span></i>

				</div>
				<!-- <button class="resetAll" id="resetAllTable"><i class="fa fa-times" aria-hidden="true"></i> Reset Selections</button> -->
				<div id="overviewTableDiv">
					<table id="overviewTable">
						<thead>
				            <tr>
				                <th width="5%">Select</th>
				                <th width="20%">Name</th>
				                <th width="25%">Email</th>
				                <th width="15%">CertNo</th>
				                <th width="20%">Year</th>
				                <th width="15%">View</th>
				            </tr>
				        </thead>
				        <tbody>
				        	<tr id="overviewSelectAll">
				        		<td><input type='checkbox' name='tableSelectAll'></td>
				        		<td colspan="5">Select All</td>
				        	</tr>
				        </tbody>
					</table>
				</div>
			</div>

		</div>
	</div>

	<?php include '../common/Footer.php'; ?>

</body>

</html>
