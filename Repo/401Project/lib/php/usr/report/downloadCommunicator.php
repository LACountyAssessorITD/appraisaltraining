<?php
/*	This code is used to handle user's request for generating reports
	Handle ajax from web page -> identify requested report type -> pass around the needed info

	@ Yining Huang

*/
	session_start();

	$file = $_SESSION['report_filename'];
	$_SESSION['download'] = TRUE;
	require_once "Download".$file;

?>
