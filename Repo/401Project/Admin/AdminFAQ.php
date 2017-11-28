<?php session_start(); ?>
<!DOCTYPE html>
<!DOCTYPE html>
<html>
<head>
	<title>LAC Website</title>
	<link rel="stylesheet" href="../CSS/DefaultUI.css">
	<link rel="stylesheet" href="../CSS/FAQStyle.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
	<script src="../common/FAQJS.js"></script>
</head>

<body>

	<?php include "../common/AdminTop.php"; ?>

	<div class="content">

		<div class="faqContent">

			<div class="faqSetDiv">
				<button class="questionButton">
					<span class='downArrowSpan'><i class='fa fa-angle-down' aria-hidden='true'></i></span> How do I use the filters?
				</button>

				<div class="answerDiv">
					<p>Select from the list of filters from the left column of the webpage and click on "Apply Filter" after selections are made. The results will be displayed in the results table.</p>
				</div>
			</div>

			<div class="faqSetDiv">
				<button class="questionButton">
					<span class='downArrowSpan'><i class='fa fa-angle-down' aria-hidden='true'></i></span> How do I view an employee's report?
				</button>

				<div class="answerDiv">
					<p>Once you see an employee in the results table, click on the view report button at the right end of the row.</p>
				</div>
			</div>

			<div class="faqSetDiv">
				<button class="questionButton">
					<span class='downArrowSpan'><i class='fa fa-angle-down' aria-hidden='true'></i></span> How do I view an employee's information?
				</button>

				<div class="answerDiv">
					<p>Once you see an employee in the results table, click on the info icon to view that employee's information. To close, click on the icon again.</p>
				</div>
			</div>

	</div>

	<?php include '../common/Footer.php'; ?>
</body>

</html>
