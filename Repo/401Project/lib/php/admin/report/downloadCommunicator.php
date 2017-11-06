<?php
/*	This code is used to handle admin's request for downloading reports

	@ Yining Huang

*/
	session_start();

	$file = $_SESSION['view_report_filename'];
	//$_SESSION['download'] = TRUE;
	require_once "Download".$file;

?>
