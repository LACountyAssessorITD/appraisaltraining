<?php
include_once "../lib/php/session.php";
// redirect_onAdminPage();
echo "<div class='top'>
		<div class='header'>
			<div class='Welcome'>
				<label>Welcome, </label>
				<label>".$_SESSION['FIRSTNAME']."</label>
				<br>
				<a href='../LDAP/logout.php' class='btn btn-danger square-btn-adjust'>Logout</a>
			</div>
			<h1><strong>Training Record</strong></h1>
			<hr>
			<h3><strong>Los Angeles County Office of the Assessor</strong></h3>
			<br>
			<h4 id='effectiveLabel'>Data effective as of: <span></span></h4>
			<img src='../BGimg/Logo.png' alt='Logo' width='130px' height='130px'>
		</div>

		<nav class='navigationBar'>
			<a id='homeTab' href='AdminHome.php'><i class='fa fa-home' aria-hidden='true'></i> Home</a>
			<a id='updateTab' href='AdminUpload.php'><i class='fa fa-upload' aria-hidden='true'></i> Update</a>
			<a id='faqTab' href='AdminFAQ.php'><i class='fa fa-question-circle-o' aria-hidden='true'></i> FAQs</a>
			<a id='issueTab' href='#'><i class='fa fa-life-ring' aria-hidden='true'></i> <strong>Support</strong></a>
		</nav>
	</div>";
?>
