<?php session_start(); ?>
<!DOCTYPE html>
<!DOCTYPE html>
<html>
<head>
	<title>LAC Website</title>
	<link rel="stylesheet" href="../CSS/FAQStyle.css">
	<link rel="stylesheet" href="../CSS/DefaultUI.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
	<script src="../common/FAQJS.js"></script>
</head>

<body>

	<?php include "../common/UserTop.php"; ?>

	<div class="content">

		<div class="faqContent">

			<div class="faqSetDiv">
				<button class="questionButton">
					<span class='downArrowSpan'><i class='fa fa-angle-down' aria-hidden='true'></i></span> How do I use the filters?
				</button>

				<div class="answerDiv">
					<p>Select from the report type you want to view, then select the year or year range you want to see (if applicable). Finally, click on "Generate Report" to preview the report.</p>
				</div>
			</div>

		</div>

	</div>

	<?php include '../common/Footer.php'; ?>
</body>

</html>
