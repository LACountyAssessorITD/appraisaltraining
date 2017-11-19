<?php
/*	This code is used to handle admin's request for downloading reports
	Called from AdminHome.php, download button
	@ Yining Huang

*/
	session_start();

	$file = $_SESSION['view_report_filename'];
	//$_SESSION['download'] = TRUE;
	require_once "Download".$file;

?>
