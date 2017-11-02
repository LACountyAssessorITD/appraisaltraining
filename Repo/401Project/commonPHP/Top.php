<?php
echo "<div class='top'>
		<div class='header'>
			<div class='Welcome'>
				<label>Welcome, </label>
				<label>".$_SESSION['FN']."</label>
			</div>
			<h1><strong>Training Record</strong></h1>
			<hr>
			<h3><strong>Los Angeles County Office of the Assessor</strong></h3>
			<img src='../BGimg/Logo.png' alt='Logo' width='130px' height='130px'>
		</div>

		<nav class='navigationBar'>
			<a id='homeTab' href='AdminHome.php'><i class='fa fa-home' aria-hidden='true'></i> Home</a>
			<a id='uploadTab' href='AdminUpload.php'><i class='fa fa-upload' aria-hidden='true'></i> Update</a>
			<a id='uploadTab' href='AdminFAQ.php'><i class='fa fa-question-circle-o' aria-hidden='true'></i> FAQs</a>
			<a id='issueTab' href='#'><i class='fa fa-life-ring' aria-hidden='true'></i> <strong>Support</strong></a>
		</nav>
	</div>";
?>