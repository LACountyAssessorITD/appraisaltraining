<?php
	// put include_once statements here:
	include_once "../../constants.php";

	// below would be overridden!
	$var_path_ANNUALREQ = PATH_XLSX_ANNUALREQ;
	$var_path_SUMMARY = PATH_XLSX_SUMMARY;
	$var_path_DETAILS = PATH_XLSX_DETAILS;

	///// below copied from PHP.net tutorial
	$log_file = 'D:/mianlu/most_recent_log.txt';
	$log_append_string = "This is the beginning of log file!\r\n";
	// FILE_APPEND to append content to the end; LOCK_EX flag to prevent anyone else writing to the file at the same time
	if ( false === file_put_contents($log_file, $log_append_string, LOCK_EX) ) die(); // ml: don't use the FILE_APPEND flag here! so the log.txt file would be overwritten on this first writing operation, each time code is run
	///// above copied from PHP.net tutorial


	define("CALLING_FROM_WEB", true);

	if (CALLING_FROM_WEB) {
		// Start the session.
		// session_start();

		// To get uploaded files directory
		// $recv_xlsx_dir = $_POST['dir']; // e.g. dir = "D:/temp/1599028283" which contains 3 xlsx files, uploaded from front end webpage!

		if ( ($handle = opendir((string)$recv_xlsx_dir))) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry != "." && $entry != "..") {
					$log_append_string = "DIR:: ".$entry."\r\n";
					if ( false === file_put_contents($log_file, $log_append_string, FILE_APPEND | LOCK_EX) ) die();
				}
			}
			closedir($handle);
		}
		else {
			$log_append_string = "FAILED TO OPEN DIR, EXITING...\r\n";
			if ( false === file_put_contents($log_file, $log_append_string, FILE_APPEND | LOCK_EX) ) die();
			die();
		}
	}
?>
