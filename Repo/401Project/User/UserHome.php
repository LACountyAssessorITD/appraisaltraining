<?php session_start(); ?>
<!DOCTYPE html>
<!DOCTYPE html>
<html lang="en-US">
<head>
	<title>LAC Website</title>
	<link rel="stylesheet" href="UserHomeStyle.css">
	<link rel="stylesheet" href="../CSS/DefaultUI.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script src="UserHomeJS.js"></script>
</head>

<body>
	<div class="top">
		<div class="header">
			<div class="Welcome">
				<label>Welcome, </label>
				<label>Name</label>
			</div>
			<div id="pageTitle">
				<h1><strong>Training Record</strong></h1>
				<hr>
				<h3><strong>Los Angeles County Office of the Assessor</strong></h3>
			</div>
			<img src="../BGimg/Logo.png" alt="Logo" width="130px" height="130px">
		</div>

		<nav class="navigationBar">
			<a id="homeTab" href="#"><i class="fa fa-home" aria-hidden="true"></i> Home</a>
			<a id="FAQTab" href="UserFAQ.php"><i class="fa fa-question-circle-o" aria-hidden="true"></i> FAQs</a>
			<a id="issueTab" href="#"><i class="fa fa-life-ring" aria-hidden="true"></i> <strong>Support</strong></a>
		</nav>
	</div>

	<div class="content">

		<div id="filterBackground"></div>

		<div class="leftContentColumn">
			<div>
				<h3 id="filterLabel" class="banLabel">Fiscal Years</h3>
			</div>

			<div class="filterListCol">
				<label>Select Report Time Range</label>
				<div class="yearType">
					<select id="yearTypeSelect">
						<option>Specific Year</option>
						<option>Annual Totals</option>
						<option>Completed Course</option>
					</select>
				</div>

				<p id="fromYearLabel">From Year:</p>
				<div class="dropDownFilter" id="fromYearDiv">
					<select id="fromYearSelect"></select>
				</div>

				<p id="toYearLabel">To Year:</p>
				<div class="dropDownFilter" id="toYearDiv">
					<select id="toYearSelect"></select>
				</div>

				<p id="specificYearLabel">Specific Year:</p>
				<div class="dropDownFilter" id="specificYearDiv">
					<select id="specificYearSelect"></select>
				</div>

				<div id="genReportDiv"><button id="genReportBtn">Generate Report</button></div>
			</div>

			<div class="optionContent">
				<div>
					<h3 id="optionLabel" class="banLabel">Options</h3>
				</div>

				<button id="Download">Download</button>
				<button id="Print">Print</button>
			</div>

		</div>

		<div class="midColumn">
			<div>
				<h3 id="previewLabel" class="banLabel">Preview Report</h3>
			</div>
			<div class="pdfView">
				<embed id="pdfBox" src="../Non Disclosure Agreement.pdf" width="100%" height="800px"></embed>
			</div>
		</div>

	</div>

	<?php include '../commonPHP/Footer.php'; ?>

</body>

</html>
