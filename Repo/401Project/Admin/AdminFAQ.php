<?php session_start(); ?>
<!DOCTYPE html>
<!DOCTYPE html>
<html>
<head>
	<title>LAC Website</title>
	<!-- <link rel="stylesheet" href="AdminFAQStyle.css"> --> 
	<link rel="stylesheet" href="../CSS/DefaultUI.css">
	<link rel="stylesheet" href="../CSS/FAQStyle.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	
	<!-- <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css"> -->
  	<!-- <link rel="stylesheet" href="/resources/demos/style.css"> -->
  	<!-- <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> -->

	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  	<!-- <script src="https://code.jquery.com/jquery-1.12.4.js"></script> -->
	<script src="AdminFAQJS.js"></script>
	<script type="text/javascript">
		$(function(){
		    $.datepicker.setDefaults(
		      $.extend( $.datepicker.regional[ '' ] )
		    );
		    $( '#datepicker' ).datepicker();
		  });
	</script>
</head>

<body>

	<?php include "../common/AdminTop.php"; ?>

	<div class="content">
		<!-- <div id="datePickerDiv" style="z-index: 1000"> -->
			<!-- <p>Date: <input type="text" id="datepicker"></p> -->
		<!-- </div> -->

		<div class="faqContent" id="accordion">

			<p class="pFiltersLabel">How do I filter what report I want to see?
			</p>

			<div class="employeeFilters">

				<div class="dropDownFilter">
					<p>
						You may use the selection tools to the left of the report, under “Fiscal Year” to select if you would like to view either one specific fiscal year’s report, or a range of fiscal years.
						<br><br>
						For one year, select “Specific Year” and then choose the fiscal year you would like to view, and click “Generate Report”.
						<br><br>
						For a range of years, select “Range of Years” and specify the time range you would like to view. “From Year” will begin from Jan 1st of that year, and “To Year” will end on Dec 31st of that year. Click “Generate Report” to view your reports.
						<br><br>
					</p>
				</div>
			</div>

			<p class="pFiltersLabel">How do I filter what report I want to see?
			</p>

			<div class="employeeFilters">

				<div class="dropDownFilter">
					<p>
						You may use the selection tools to the left of the report, under “Fiscal Year” to select if you would like to view either one specific fiscal year’s report, or a range of fiscal years.
						<br>
						For one year, select “Specific Year” and then choose the fiscal year you would like to view, and click “Generate Report”.
						<br>
						For a range of years, select “Range of Years” and specify the time range you would like to view. “From Year” will begin from Jan 1st of that year, and “To Year” will end on Dec 31st of that year. Click “Generate Report” to view your reports.
					</p>
				</div>
			</div>

		</div>

	</div>

	<?php include '../common/Footer.php'; ?>
	<!-- <div class="footer">
		<p class="links">
			<span id="contactUs"><a href="#">Contact Us</a></span>
			<span id="Disclaimer"><a href="#">Disclaimer</a></span>
			<span id="FAQs"><a href="#">FAQs</a><span></span>
		</p>
		<p>2017 - Present © Los Angeles County Office of the Assessor</p>
	</div> -->
</body>

</html>
