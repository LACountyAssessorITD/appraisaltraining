<?php
/*	This code is used to handle user's request for downloading reports
	Called from UserHome.php, download button
	@ Yining Huang

*/
	session_start();

	$file = $_SESSION['report_filename'];
	$_SESSION['download'] = TRUE;
	require_once "Download".$file;

?>
