<?php
/*	This code is used to handle user's request for generating reports
	Handle ajax from web page -> identify requested report type -> pass around the needed info

	@ Yining Huang

*/
	session_start();

	$type = $_POST['yearTypeKey'];
	$specific = $_POST['specificYearInt'];
	$year1 = $_POST['toYearInt'];
	$year2 = $_POST['fromYearInt'];
	$_SESSION['download'] = FALSE;

	if ($type == 1) {
		$_SESSION["specific_year"] = $specific;
		$_SESSION['report_filename'] = $_POST['file_name'];
	} else if ($type == 2) {
		$_SESSION["toYearInt"] = $year1;
		$_SESSION["fromYearInt"] = $year2;
		$_SESSION['report_filename'] = $_POST['file_name'];
	} else if ($type == 0) {
		$_SESSION['report_filename'] = $_POST['file_name'];
	} else {
		echo "!UNDEFINED";
	}

?>
