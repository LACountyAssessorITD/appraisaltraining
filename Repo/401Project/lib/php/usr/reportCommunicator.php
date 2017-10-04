<?php
/*	This code is used to handle user's request for generating reports
	Handle ajax from web page -> identify requested report type -> pass around the needed info

	@ Yining Huang

*/
	session_start();
	$_SESSION['yearTypeKey'] = $_POST["yearTypeKey"];
	if ($_SESSION['yearTypeKey'] =='specific') {	// if the user request to see the specific year's report
		$_SESSION["specific_year"] = $_POST["specificYearInt"];
	} else if ($_SESSION['yearTypeKey'] == 'range'){ // IF THE USER REQUEST TO GENERATE REPORT FOR A YEAR RANGE
		$_SESSION["toYearInt"]  = $_POST["toYearInt"];
		$_SESSION["fromYearInt"]  = $_POST["fromYearInt"];
	}


?>
