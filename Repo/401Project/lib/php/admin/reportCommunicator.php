<?php

$type = $_GET['type'];
$certNo = $_GET['certNo'];
$year1 = $_GET['year1'];
$year2 = $_GET['year2'];

if ($type == "specific") { // if requesting xpecific year report
	include_once("reportType/1_Specific_Year.php?id=".$certNo."&year=".$year1);
} else if ($type == "course") { // if requesting completed corse summary
	include_once("reportType/2_Completed_Course.php?id=".$certNo."&year1=".$year1."&year2=".$year2);
} else if ($type == "summary") { // if requesting annual total summary
	include_once("reportType/3_Annual_Summary.php?id=".$certNo);
}

?>