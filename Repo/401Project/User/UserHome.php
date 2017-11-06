<?php
session_start();
?>
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

	<?php include "../common/UserTop.php"; ?>

	<div class="content">

		<div id="filterBackground"></div>

		<div class="leftContentColumn">
			<div>
				<h3 id="filterLabel" class="banLabel">Fiscal Years</h3>
			</div>

			<div class="filterListCol">
				<label>Select Report Type</label>
				<div class="yearType">
					<select id="yearTypeSelect"></select>
				</div>

				<div id="reportDescription">
					<p></p>
				</div>

				<div id="yearDropDowns">
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
				</div>

				<div id="genReportDiv"><button id="genReportBtn">Generate Report</button></div>
			</div>

			<div class="optionContent">
				<div>
					<h3 id="optionLabel" class="banLabel">Options</h3>
				</div>
				<a id="downloadLink" href="../lib/php/usr/report/downloadCommunicator.php" target="_blank">
					<button id="Download"><i class="fa fa-download" aria-hidden="true"></i> Download</button>
				</a>
				<!-- <button id="Print">Print</button> -->
			</div>

		</div>

		<div class="midColumn">
			<div>
				<h3 id="previewLabel" class="banLabel">Preview Report</h3>
			</div>
			<div class="pdfView">
				<embed id="pdfBox" src="../LACLogo.pdf" width="100%" height="800px"></embed>
			</div>
		</div>

	</div>

	<?php include '../common/Footer.php'; ?>

</body>

</html>
