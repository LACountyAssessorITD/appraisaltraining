<?php
session_start();
$_SESSION['view_certNo'] = $_POST["certNo"];
// $type = $_POST['type'];
// $year1 = $_POST['year1'];
// $year2 = $_POST['year2'];



/*
if ($type == "specific") { // if requesting xpecific year report
	include_once("reportType/1_Specific_Year.php?id=".$certNo."&year=".$year1);
} else if ($type == "course") { // if requesting completed corse summary
	include_once("reportType/2_Completed_Course.php?id=".$certNo."&year1=".$year1."&year2=".$year2);
} else if ($type == "summary") { // if requesting annual total summary
	include_once("reportType/3_Annual_Summary.php?id=".$certNo);
}
*/
?>