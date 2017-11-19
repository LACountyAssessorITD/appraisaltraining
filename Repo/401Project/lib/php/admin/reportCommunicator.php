<?php
/*	This code is used to handle admin's request for generating reports
	Handle ajax from web page -> identify requested report type -> pass around the needed info

	@ Yining Huang

*/
session_start();

$_SESSION['view_certNo'] = $_POST["certNo"];

$type = $_POST['year_type'];
$year1 = $_POST['year1'];
$year2 = $_POST['year2'];
$filename = $_POST['report_file_name'];

if ($type == 1) {
	$_SESSION['view_specific_year'] = $year1;
	$_SESSION['view_report_filename']  = $filename;
} else if ($type == 2) {
	$_SESSION['view_year1'] = $year1;
	$_SESSION['view_year2'] = $year2;
	$_SESSION['view_report_filename']  = $filename;
} else if ($type == 0) {
	$_SESSION['view_report_filename']  = $filename;
} else {
	echo "!UNDEFINED";
}

?>