<?php session_start(); ?>
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

	<!-- <div class="top">
		<div class="header">
			<div class="Welcome">
				<label>Welcome, </label>
				<label><?php echo $_SESSION['FN']; ?></label>
			</div>
			<h1><strong>Training Record</strong></h1>
			<hr>
			<h3><strong>Los Angeles County Office of the Assessor</strong></h3>
			<img src="../BGimg/Logo.png" alt="Logo" width="130px" height="130px">
		</div>

		<nav class="navigationBar">
			<a id="homeTab" href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a>
			<a id="uploadTab" href="AdminUpload.php"><i class="fa fa-upload" aria-hidden="true"></i> Update</a>
			<a id="uploadTab" href="AdminFAQ.php"><i class="fa fa-question-circle-o" aria-hidden="true"></i> FAQs</a>
			<a id="issueTab" href="#"><i class="fa fa-life-ring" aria-hidden="true"></i> <strong>Support</strong></a>
		</nav>
	</div> -->

	<?php include "../commonPHP/Top.php"; ?>

	<div class="content">

		<div id="filterBackground"></div>
		<div class="leftContentColumn">

			<div>
				<h3 id="filterLabel" class="banLabel">Filters</h3>
			</div>

			<button id="resetAllBtn">Reset All Filters</button>
			<p class="toolTipParent">Hover please<span class="toolTip">Hello</span></p>

			<div class="filterListCol" id="accordion">

				<div id="reportType">
					<label id="reportTypeLabel">Select Report Type</label>
					<select id="reportTypeSelect">
						<option>!ERROR</option>
					</select>
				</div>


				<!-- <p class="pFiltersLabel">Employee</p>

				<div class="employeeFilters" name="[New_Employee]">
					<div class="dropDownFilter">
						<button class="dropDownBtn" name="CertNo">Certification Number</button>
					</div>
					<div class="dropDownFilter">
						<button class="dropDownBtn" name="FirstName">First Name</button>
					</div>
					<div class="dropDownFilter">
						<button class="dropDownBtn" name="LastName">Last Name</button>
					</div>
					<div class="dropDownFilter">
						<button class="dropDownBtn" name="CurrentStatus">Current Status</button>
					</div>
					<div class="dropDownFilter">
						<button class="dropDownBtn" name="Auditor">Auditor</button>
					</div>
				</div> -->


				<p class="pFiltersLabel">Employee Info</p>

				<div class="employeeFilters" name="[New_Employee]">

					<!-- <div class="dropDownFilter">
						<button class="dropDownBtn" name="CertNo">Certification Number</button>
					</div> -->

					<!-- <div class="dropDownFilter">
						<button class="dropDownBtn">EmployeeID</button>
						<div class="DPBCont">
							<div class="tableWrap">
								<form class="leftInput">
									<input type="text" placeholder="Search.." autocomplete="off">
								</form>
								<div class="filterContTableBG"></div>
								<table class="filterContTable">
									<col width="20">
									<thead>
										<tr>
											<td><input type="checkbox" name="selectAll"></td>
											<td>Select All</td>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td><input type="checkbox" name="selected"></td>
											<td>12345</td>
										</tr>
										<tr>
											<td><input type="checkbox" name="selected"></td>
											<td>22145</td>
										</tr>
										<tr>
											<td><input type="checkbox" name="selected"></td>
											<td>134245</td>
										</tr>
									</tbody>
								</table>
							</div>
							<div class="filterDisplayList">
								<label>Selections:</label>
								<ul></ul>
							</div>
							<iframe class="cover" src="about:blank"></iframe>
						</div>
					</div> -->

<!-- 					<div class="dropDownFilter">
						<button class="dropDownBtn" name="FirstName">First Name</button>
					</div>

					<div class="dropDownFilter">
						<button class="dropDownBtn" name="MiddleName">Middle Name</button>
					</div>

					<div class="dropDownFilter">
						<button class="dropDownBtn" name="LastName">Last Name</button>
					</div> -->

					<div class="dropDownFilter">
						<button class="dropDownBtn" name="Name">Full Name</button>
					</div>
<!--
					<div class="dropDownFilter">
						<button class="dropDownBtn">Title</button>
					</div>

					<div class="dropDownFilter">
						<button class="dropDownBtn">Item</button>
					</div>

					<div class="dropDownFilter">
						<button class="dropDownBtn">Pay Location</button>
					</div>

					<div class="dropDownFilter">
						<button class="dropDownBtn">Region/Area</button>
					</div>

					<div class="dropDownFilter">
						<button class="dropDownBtn">Office/District</button>
					</div>

					<div class="dropDownFilter">
						<button class="dropDownBtn">Manager(s)</button>
					</div>

					<div class="dropDownFilter">
						<button class="dropDownBtn">Phone Number</button>
					</div>

					<div class="dropDownFilter">
						<button class="dropDownBtn">Email Address</button>
					</div>
 -->
					<div class="dropDownFilter">
						<button class="dropDownBtn" name="TempCertDate">Temporary Certification Date</button>
						<!-- <div class="DPBCont">
							<div class="fromDateWrap">
								<div class="tableWrap">
									<form class="leftInput">
										<input type="text" placeholder="Search.." autocomplete="off">
									</form>
									<div class="filterContTableBG"></div>
									<table class="filterContTable">
										<col width="20">
										<thead>
											<tr>
												<td><input type="checkbox" name="selectAll"></td>
												<td>Select All</td>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td><input type="checkbox" name="selected"></td>
												<td>12345</td>
											</tr>
											<tr>
												<td><input type="checkbox" name="selected"></td>
												<td>22145</td>
											</tr>
										</tbody>
									</table>
								</div>
								<div class="filterDisplayList">
									<label>Selections:</label>
									<ul></ul>
								</div>
							</div>

							<div class="toDateWrap">
								<div class="tableWrap">
									<form class="leftInput">
										<input type="text" placeholder="Search.." autocomplete="off">
									</form>
									<div class="filterContTableBG"></div>
									<table class="filterContTable">
										<col width="20">
										<thead>
											<tr>
												<td><input type="checkbox" name="selectAll"></td>
												<td>Select All</td>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td><input type="checkbox" name="selected"></td>
												<td>asd45</td>
											</tr>
											<tr>
												<td><input type="checkbox" name="selected"></td>
												<td>22145</td>
											</tr>
										</tbody>
									</table>
								</div>
								<div class="filterDisplayList">
									<label>Selections:</label>
									<ul></ul>
								</div>
							</div>
							<iframe class="cover" src="about:blank"></iframe>
						</div> -->
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


			<!-- 	<p class="pFiltersLabel">Certification History</p>

				<div class="certHistoryFilters" name="[New_CertHistory]">
					<div class="dropDownFilter">
						<button class="dropDownBtn" name="CertNo">Certification Number</button>
					</div>

					<div class="dropDownFilter">
						<button class="dropDownBtn" name="CertYear">Fiscal Year</button>
					</div>

					<div class="dropDownFilter">
						<button class="dropDownBtn" name="CertType">Certification Type</button>
					</div>

					<div class="dropDownFilter">
						<button class="dropDownBtn" name="Status">Status</button>
					</div>

					<div class="dropDownFilter">
						<button class="dropDownBtn" name="HoursEarned">Hours Earned</button>
					</div>

					<div class="dropDownFilter">
						<button class="dropDownBtn" name="CurrentYearBalance">Current Year Balance</button>
					</div>

					<div class="dropDownFilter">
						<button class="dropDownBtn" name="PriorYearBalance">Prior Year Balance</button>
					</div>

					<div class="dropDownFilter">
						<button class="dropDownBtn" name="CarryToYear1">Carry To Year 1</button>
					</div>

					<div class="dropDownFilter">
						<button class="dropDownBtn" name="CarryToYear2">Carry To Year 2</button>
					</div>

					<div class="dropDownFilter">
						<button class="dropDownBtn" name="CarryToYear3">Carry To Year 3</button>
					</div>

					<div class="dropDownFilter">
						<button class="dropDownBtn" name="CarryForwardTotal">Carry Forward Total</button>
					</div>
				</div>


				<p class="pFiltersLabel">Carry Over Limits</p>

				<div class="carryOverLimitFilters" name="[New_CarryoverLimits]">
					<div class="dropDownFilter">
						<button class="dropDownBtn" name="RequiredHours">Required Hours</button>
					</div>

					<div class="dropDownFilter">
						<button class="dropDownBtn" name="Year1Limit">Year 1 Limit</button>
					</div>

					<div class="dropDownFilter">
						<button class="dropDownBtn" name="Year2Limit">Year 2 Limit</button>
					</div>

					<div class="dropDownFilter">
						<button class="dropDownBtn" name="Year3Limit">Year 3 Limit</button>
					</div>
				</div>


				<p class="pFiltersLabel">Course Detail</p>

				<div class="courseDetailFilters" name="[New_CourseDetail]">
					<div class="dropDownFilter">
						<button class="dropDownBtn" name="ItemNumber">Item Number</button>
					</div>

					<div class="dropDownFilter">
						<button class="dropDownBtn" name="CourseName">Course Name</button>
					</div>

					<div class="dropDownFilter">
						<button class="dropDownBtn" name="CourseLocation">Course Location</button>
					</div>

					<div class="dropDownFilter">
						<button class="dropDownBtn" name="CourseGrade">Course Grade</button>
					</div>

					<div class="dropDownFilter">
						<button class="dropDownBtn" name="CourseHoursEarned">Course Hours Earned</button>
					</div>

					<div class="dropDownFilter">
						<button class="dropDownBtn" name="EndDate">End Date</button>
					</div>
				</div> -->

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
						<!-- <embed id="pdfBox" src="../OrderDetails.pdf" width="100%" height="650px"></embed> -->
						<!-- <a href="singleUserReport.php">Click here to download the PDF</a>. Or -->
						<!-- <a href="http://get.adobe.com/reader/" target="_blank">click here to install Adobe Reader</a>.</p> -->
						<!-- <iframe id="pdfBox" title="PDF in an i-Frame" src="../LACLogo.pdf" frameborder="0" scrolling="auto" width="100%" height="800px"></iframe> -->
						<!-- <embed id="pdfBox" src="../LACLogo.pdf" width="100%" height="800px"></embed>; -->
						<object id="pdfBox" data="../LACLogo.pdf" type="application/pdf" width="100%" height="600px">
							<embed src="../LACLogo.pdf" type="application/pdf"></embed>
						</object>
					</div>
				</div>

				<div class="rightContentColumn">
					<!-- <div>
						<h3 id="optionLabel" class="banLabel">Options</h3>
					</div> -->

					<button id="MyReport"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> My Report</button>
					<button id="Download"><i class="fa fa-download" aria-hidden="true"></i> Download</button>
					<button id="Print"><i class="fa fa-print" aria-hidden="true"></i> Print</button>
					<button id="EmailAll"><i class="fa fa-envelope" aria-hidden="true"></i> Email</button>
				</div>
				<div id="emailDiv">
					<p>Subject</p>
					<textarea id="emailSubjectTA" placeholder="Email Subject..." rows="1"></textarea>
					<p>Content</p>
					<textarea id="emailContentTA" placeholder="Email content..."></textarea>
					<div id="sendEmailBtnDiv">
						<button id="sendEmailSelectedBtn"><i class="fa fa-paper-plane" aria-hidden="true"></i> Send Selected</button>
						<!-- <button id="sendEmailAllBtn"><i class="fa fa-paper-plane" aria-hidden="true"></i> Send All</button> -->
					</div>
				</div>

			</div>

			<div class="filterOverview">
				<h3 id="overviewLabel" class="banLabel">Filter Overview</h3>
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

	<?php include '../commonPHP/Footer.php'; ?>
	<!-- <div class="footer">
		<p class="links">
			<span id="contactUs"><a href="#">Support</a></span>
			<span id="Disclaimer"><a href="#">Disclaimer</a></span>
			<span id="FAQs"><a href="#">FAQs</a><span></span>
		</p>
		<p>2017 - Present &copy; Los Angeles County Office of the Assessor</p>
	</div> -->

</body>

</html>