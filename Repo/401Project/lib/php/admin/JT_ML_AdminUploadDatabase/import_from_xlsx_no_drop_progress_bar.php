<?php
	/*==================================================================================================================
	For LA County Assessor's Office - Appraisal Training Record Tracking System use only!
	Date: Aug. - Dec. 2017
	Contributor for this file: Mian Lu (mianlu@usc.edu, miuralu670@gmail.com) and James Tseng (tsengj@usc.edu)

	Below are some things to note:

	1.	There are a bunch of T/F flags defined in the beginning part of code:
		- CALLING_FROM_WEB:			defaults to True because this php entire php script will be called by front end with
									ajax, from adminUploadPHP.php & adminUploadJS.js; make it false when running this
									php script as a webpage directly (most likely when debugging)
		- do_step_1/2/3/cleanup:	default to all True, to make all 3 steps in the primary work (load data into
									Employee table, CertHistory table and CourseDetails table) happen. 2 & 3 can be set
									to false to make debugging finish faster (since completing all 3 steps will take
									~6mins while step 1 alone takes <30s)

	2.	Since this php script should mainly be called from webpage front end (adminUploadPHP.php -> adminUploadJS.js),
		debugging messages using "echo" statements is difficult (must be "catch"ed from higher level ajax, but not
		implemented in this project). Therefore, debugging messages are written to a log file using this statement:
			if ( false === file_put_contents($log_file, $log_append_string, FILE_APPEND | LOCK_EX) ) die();
		Note that when writing to log file fails, this PHP script immediately terminates and all remaining code not run
		yet will never get executed. However, also note that this PHP script is fail-safe, meaning one execution that
		failed half-way would not affect the "production database" (the one being queried by front end webpages)

	3.	Explanation of "fail-safety" for this PHP script:
		In short, a failed attempt to import xlsx files and update the database, will result in nothing. Everything will
		be as if the attempt was never made, and webpages will continue to show old data. Only when an import is
		successful, then all webpages will show newly imported data.
		To explain in full details: this "safety" relates to the 3-databases structure of this project.
		In SQL Server (if viewed using SSMS), there are 3 databases ending with "00", "01" and "02". "00" is
		called "metadata database"; "01" and "02" are two databases that ideally have the same structure and similar
		data. The "production database", at any point in time, should refer to either "01" or "02", indicated by data
		stored in "00". Each time an import operation is executed by a DB Admin going to adminUploadPHP.php webpage,
		s/he would specify 3 xlsx files that are received from BOE in that specific month. If, say, before the import
		operation starts, the "production database" is "01", then this import operation is going to load these 3 xlsx
		files into "02". If this import operation fails half-way, then "02" would contain incomplete data; however "01"
		is NOT affected by this operation, and all the webpages would continue to fetch old data from "01". On the other
		hand, if this import operation is successful, then "metadata database" (i.e. "00") will update one row in one of
		its tables ("DbTable") to say, "Last import was successful, therefore the new 'production database' should be
		'02' instead of '01'!". Afterwards all webpages would automatically switch to query "02" (when generating
		reports, etc.), and the subsequent import attempt would be done on "01" so that latest "02" won't be affected
		by a failure.

	4.	To enable easy code-folding in Sublime Text, a bunch of "if (true)" statements are used. This might be a bad
		practice, but helps to visualize major steps in the code. In Sublime Text, click on the little downward arrow
		by the line numbers (on the lines with "if(true)" statements) to toggle-fold large chunks of code.

	5.	To implement a progress bar, this php script will periodically write current progress status to a file somewhere
		in D:/ (please see below function updateProgressBar() for actual file path), while front end adminUpdate webpage
		(which calls this php script using ajax) will constantly poll on that same file, updating its progress bar
		display every second. Therefore this php script and the front end webpage both run as separate threads,	one
		writing to a file and the other one reading from it.

	6.	Memory usage for this code is a problem. Normally, recommended memory allocation for PHP (when hosting PHP web-
		pages on Windows Server using IIS) is 128MB, which is specified in php.ini (a text file, sample provided in path
		appraisaltraining/Documents/PHP/V7.1 and this folder is what we use for this project). However, 128MB is not
		enough for this code to work, because this code will have to read from xlsx files that have large amount of
		cells, and the 3rd party library PHPExcel (that we've decided to use) is known to consume a lot of memory. We
		have tested the functionalities of PHPExcel on our code and decided that the least amount of memory to allow our
		code to work is 512MB. This value is set on the //TOT// line of this file. Additionally, we've taken some other
		measures to optimize the memory usage already (and the final minimum requirement comes down to 512MB). Below are
		links to some webpages that give useful information:
			https://stackoverflow.com/questions/3537604/how-to-fix-memory-getting-exhausted-with-phpexcel
			https://stackoverflow.com/questions/4817651/phpexcel-runs-out-of-256-512-and-also-1024mb-of-ram
			https://stackoverflow.com/questions/17161925/how-to-close-excel-file-in-php-excel-reader
		We've employed some measures discussed above, as long as they are appropriate for our project, but one of them
		we decide not to use is shown below:
			$reader_example = PHPExcel_IOFactory::createReader('Excel2007');
			$reader_example->setReadDataOnly(true);
		Setting a "Excel Reader" to "Read Data Only" will force the reader not to load cell formats (for e.g. date/time,
		currency, numerical value, plain text), which saves memory, but will be a problem for cells that contain dates.
		If Excel Reader interpret those cell values as plain numbers instead of dates, then it would be difficult to
		insert those numbers (obtained from xlsx) as dates into SQL Server later on.
		Another note: to reduce memory usage, we used "lazy-reading" to open xlsx files, meaning that we only create
		an instance of "Excel Reader" and start reading data from an xlsx file when it's immediately necessary, but not
		any earlier. After finish reading using that reader, close it immediately, even if that same xlsx file will be
		read from again at a later point in the code. This reduces the lifetime of PHPExcel Readers, which might slow
		down the run time a little bit, but requires less memory. If readers for all 3 xlsx files are simultaneously
		open, memory usage would dramatically increase.

	7.	Front end webpage receive 3 xlsx files uploaded by Admin who got them from BOE every month. Our system does not
		check those xlsx files' file names because those are specified by human and could vary from time to time. We
		decide to implement a simple check to determine which xlsx file is Summary, which one is AnnualReq, and which
		one is Details: read the number of columns from each of the 3 xlsx received. If the number of columns for one
		of the xlsx files is "U", then that xlsx file contains AnnualReq data. If it's "L" then it's Details. If it's
		"J" then it's Summary.
		Below is original output for counting how many columns each xlsx file (monthly from BOE) have:
			Listing received xlsx files: AnnualReq Los Angeles.xlsx
				D:/temp/1512778620/AnnualReq Los Angeles.xlsx
				highest column is: U
			Listing received xlsx files: Los Angeles Training Details.xlsx
				D:/temp/1512778620/Los Angeles Training Details.xlsx
				highest column is: L
			Listing received xlsx files: Summary Los Angeles.xlsx
				D:/temp/1512778620/Summary Los Angeles.xlsx
				highest column is: J
		Therefore, our simple and naive xlsx distinguishing would be to simply look at how many columns each xlsx file
		has, and match it with corresponding Summary/AnnualReq/Details.
	==================================================================================================================*/

	/*----------------------------------------------------------------------------------------------------------------*/
	// define functions to be called in main process
	function checkForExit() { // call in each iteration, so that if front-end "stop" button is pressed, terminate script
		echo "Hello world!";
	}

	function updateProgressBar($percentage, $msg) {
		$arr_content['percent'] = (string)$percentage;
		$arr_content['message'] = (string)$msg;
		// serialize the PHP array into JSON format & write the progress into D:/TrainRec/DatabaseUpdate/ProgressBar.txt
		file_put_contents("D:/TrainRec/DatabaseUpdate/ProgressBar.txt", json_encode($arr_content));
	}

	// below two functions copied directly from a post on StackOverflow
	function StringStartsWith($haystack, $needle) {
		 $length = strlen($needle);
		 return (substr($haystack, 0, $length) === $needle);
	}
	function StringEndsWith($haystack, $needle) {
		$length = strlen($needle);
		return $length === 0 || (substr($haystack, -$length) === $needle);
	}
	// above two functions copied directly from a post on StackOverflow
	/*----------------------------------------------------------------------------------------------------------------*/

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// beginning of actual work

	//	I.		INITIALIZATION
	if(true) {
		//	1.	ALWAYS PUT THIS ON TOP! because constants are referenced everywhere. Put include_once statements here:
		include_once "../../constants.php";

		//	2.	define a bunch of constants & flags; flags are used for branches in following code
		ini_set('memory_limit', '512M'); // TOT optimize more?
		define("CALLING_FROM_WEB", true);
		define("PRINT_NOTES", false);
		define("DO_STEP_1", true);
		define("DO_STEP_2", true);
		define("DO_STEP_3", true);
		define("DO_CLEANUP", true);
		$var_path_ANNUALREQ = PATH_XLSX_ANNUALREQ;
		$var_path_SUMMARY = PATH_XLSX_SUMMARY;
		$var_path_DETAILS = PATH_XLSX_DETAILS;
		$total_num_of_rows = intval(0);
		$row_counter = intval(0);

		//	6.	xlsx reading initialization (see top comment for memory usage issues), then lazy-read from now on!
		error_reporting(E_ALL ^ E_NOTICE);
		require_once 'Classes/PHPExcel.php';
		$cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
		$cacheSettings = array( ' memoryCacheSize ' => '16MB');
		PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

		//	2.1	update progress bar
		updateProgressBar( intval(0), ((string)$row_counter." row(s) processed.") );

		//	3.	define a log text file to replace "echo" debugging statements (see top comment). Template from PHP.net.
		$log_file = 'D:/TrainRec/DatabaseUpdate/most_recent_log.txt';
		$log_append_string = "This is the beginning of log file!\r\n";
		// set flag FILE_APPEND to append content to end of file; LOCK_EX flag prevents others writing at the same time
		if ( false === file_put_contents($log_file, $log_append_string, LOCK_EX) ) die();	// although used in every
																							// subsequent log-write,
																							// don't use the FILE_APPEND
																							// flag here! Not using it
																							// will result in file re-
																							// creation, so log.txt will
																							// be overwritten here

		//	4.	if calling code from web, here are some extra overhead to get everything working. If running this php
		//		as stand-alone webpage, toggle value of flag CALLING_FROM_WEB, then lines below would be skipped
		if (CALLING_FROM_WEB) {
			// get uploaded files directory, passed to this script from front end ajax code (adminUploadJS.js)
			$recv_xlsx_dir = $_POST['dir'];	// e.g. dir = "D:/temp/1599028283" which should contain 3 xlsx files,
											// uploaded from front end adminUploadPHP.php
			if ( ($handle = opendir((string)$recv_xlsx_dir))) { // if read directory success
				$file_counter = intval(0);
				while (false !== ($entry = readdir($handle))) {
					$entry_length = strlen($entry);
					if ($entry != "." && $entry != ".." && StringEndsWith($entry, "xlsx")) {
						$file_counter += 1;
						$log_append_string = "Listing received xlsx files: ".$entry."\r\n";
						if ( false === file_put_contents($log_file, $log_append_string, FILE_APPEND | LOCK_EX) ) die();
						// found a new .xlsx file! see how many columns it has, and determine which xlsx is it (i.e. is
						// it Summary? or AnnualReq? or Details?)
						//////////////////// lazy-reading INIT ////////////////////
						$excelReader	=	PHPExcel_IOFactory::createReader('Excel2007');
						$excelReader	->	setReadDataOnly(true); // okay to use this line here! (see top comment)
						if ( false === file_put_contents($log_file, "I'm here 4 | ".$recv_xlsx_dir.$entry."\r\n", FILE_APPEND | LOCK_EX) ) die();
						$excelObj		=	$excelReader->load($recv_xlsx_dir.$entry);
						$excelSheet		=	$excelObj->getActiveSheet();
						//////////////////// lazy-reading READY ////////////////////
						$log_append_string = "highest column is: ".(string)$excelSheet->getHighestColumn()."\r\n";
						if ( false === file_put_contents($log_file, $log_append_string, FILE_APPEND | LOCK_EX) ) die();
						if		((string)$excelSheet->getHighestColumn() == "U") {
							if ( false === file_put_contents($log_file, "Overriding path for AnnualReq.xlsx...\r\n", FILE_APPEND | LOCK_EX) ) die();
							$var_path_ANNUALREQ = (string)$recv_xlsx_dir.$entry;
						}
						else if	((string)$excelSheet->getHighestColumn() == "L") {
							if ( false === file_put_contents($log_file, "Overriding path for Details.xlsx...\r\n", FILE_APPEND | LOCK_EX) ) die();
							$var_path_DETAILS = (string)$recv_xlsx_dir.$entry;
						}
						else if	((string)$excelSheet->getHighestColumn() == "J") {
							if ( false === file_put_contents($log_file, "Overriding path for Summary.xlsx...\r\n", FILE_APPEND | LOCK_EX) ) die();
							$var_path_SUMMARY = (string)$recv_xlsx_dir.$entry;
						}
					}
				}
				if ($file_counter != 3) { // not seeing 3 xlsx from that directory, write error msg to log and exit!
					$log_append_string = "Fatal error: not exactly 3 xlsx files are provided from front end!\r\n";
					if ( false === file_put_contents($log_file, $log_append_string, FILE_APPEND | LOCK_EX) ) die();
					die();
				}
				closedir($handle);
			}
			else { // read directory fails
				$log_append_string = "FAILED TO OPEN DIR, EXITING...\r\n";
				if ( false === file_put_contents($log_file, $log_append_string, FILE_APPEND | LOCK_EX) ) die();
				die();
			}
		}

		//	7.	count total number of rows from 3 excel sheets, using 3 "lazy-reading" blocks
		if(DO_STEP_1) {
			$excelReader_Summary	=	PHPExcel_IOFactory::createReader('Excel2007');
			// $excelReader_Summary->setReadDataOnly(true);
			$excelObj_Summary		=	$excelReader_Summary->load($var_path_SUMMARY);
			$summary				=	$excelObj_Summary->getActiveSheet();
			$total_num_of_rows		+=	$summary->getHighestRow() - 1; // minus one row because of header row in xlsx
			unset($excelObj_Summary);
		}
		if(DO_STEP_2) {
			$excelReader_AnnualReq	=	PHPExcel_IOFactory::createReader('Excel2007');
			// $excelReader_AnnualReq->setReadDataOnly(true);
			$excelObj_AnnualReq		=	$excelReader_AnnualReq->load($var_path_ANNUALREQ);
			$annualreq				=	$excelObj_AnnualReq->getActiveSheet();
			$total_num_of_rows		+=	$annualreq->getHighestRow() - 1; // minus one row because of header row in xlsx
			unset($excelObj_AnnualReq);
		}

		if(DO_STEP_3) {
			$excelReader_Details	=	PHPExcel_IOFactory::createReader('Excel2007');
			// $excelReader_Details->setReadDataOnly(true);
			$excelObj_Details		=	$excelReader_Details->load($var_path_DETAILS);
			$details				=	$excelObj_Details->getActiveSheet();
			$total_num_of_rows		+=	$details->getHighestRow() - 1; // minus one row because of header row in xlsx
			unset($excelObj_Summary);
		}
		$log_append_string = "Total number of rows to be inserted: ".(string)$total_num_of_rows."; starting now...\r\n";
		if ( false === file_put_contents($log_file, $log_append_string, FILE_APPEND | LOCK_EX) ) die();

		updateProgressBar(intval(5),((string)$row_counter." row(s) out of ".(string)$total_num_of_rows." processed.") );

		// // FOR PROGRESS BAR >>>
		// $percent = intval(5); // roughly say, 5% of work is done just after counting number of all rows in 3 xlsx files
		// $arr_content['percent'] = $percent;
		// $arr_content['message'] = $row_counter." row(s) out of ".(string)$total_num_of_rows." processed.";
		// file_put_contents("D:/mianlu/ProgressBar.txt", json_encode($arr_content)); // Write the progress into D:/mianlu/ProgressBar.txt and serialize the PHP array into JSON format.
		// // FOR PROGRESS BAR END <<<
	}

	//	II.		SQL Server - open connection the correct way
	//			try to open Metadata Database "00" (or create it, if it's not there); make sure "01" & "02" exist;
	//			decide which among "01" & "02" is the obsolete one and run database update that one. (see top comment)
	if(true) {
		// step 1 - connect to SQL Server, NOT specifying any database!
		if(true) {
			$connectionInfo = array( "UID"=>SQL_SERVER_USERNAME, "PWD"=>SQL_SERVER_PASSWORD);
			$conn = sqlsrv_connect( SQL_SERVER_NAME, $connectionInfo);
			if( $conn ) {
				$log_append_string = "SQL Server connection to ".SQL_SERVER_USERNAME." established.\r\n";
				if ( false === file_put_contents($log_file, $log_append_string, FILE_APPEND | LOCK_EX) ) die();
			}
			else {
				$log_append_string = "SQL Server connection to ".SQL_SERVER_USERNAME." cannot be established.\r\n";
				if ( false === file_put_contents($log_file, $log_append_string, FILE_APPEND | LOCK_EX) ) die();
				die( print_r( sqlsrv_errors(), true));
			}
			updateProgressBar( intval(7), ((string)$row_counter." row(s) out of ".(string)$total_num_of_rows." processed.") );
		}

		// step 2 - (if not exist) create metadata database, connect to it, then create & populate DbTable
		if(true) {
			// (if not exist) CREATE METADATA DB
			$create_metadata_db = "
				IF DB_ID(N'".SQL_SERVER_LACDATABASE_ML_DEVELOPMENT_no_drop_00."') IS NULL
					CREATE DATABASE ".SQL_SERVER_LACDATABASE_ML_DEVELOPMENT_no_drop_00.";
			";
			$srvr_stmt = sqlsrv_query( $conn, $create_metadata_db );
			if( $srvr_stmt === false ) { die( print_r( sqlsrv_errors(), true)); }
			// CONNECT TO METADATA DB
			$connectionInfo = array( "Database"=>SQL_SERVER_LACDATABASE_ML_DEVELOPMENT_no_drop_00, "UID"=>SQL_SERVER_USERNAME, "PWD"=>SQL_SERVER_PASSWORD);
			$conn = sqlsrv_connect( SQL_SERVER_NAME, $connectionInfo);
			if( $conn ) {
				$log_append_string = "SQL Server connection to METADATA DATABASE established.\r\n";
				if ( false === file_put_contents($log_file, $log_append_string, FILE_APPEND | LOCK_EX) ) die();
			}
			else {
				$log_append_string = "SQL Server connection to METADATA DATABASE could not be established.\r\n";
				if ( false === file_put_contents($log_file, $log_append_string, FILE_APPEND | LOCK_EX) ) die();
				die( print_r( sqlsrv_errors(), true));
			}
			// (if not exist) CREATE METADATA DB -> METADATA TABLE
			$create_metadata_tbl = "
				IF OBJECT_ID('dbo.DbTable', 'U') IS NULL
				BEGIN;
					CREATE TABLE ".SQL_SERVER_LACDATABASE_ML_DEVELOPMENT_no_drop_00.".dbo.DbTable (
						DbName nvarchar(50),
						IsCurrent int
					);
				END;
			";
			$srvr_stmt = sqlsrv_query( $conn, $create_metadata_tbl );
			if( $srvr_stmt === false ) { die( print_r( sqlsrv_errors(), true)); }
			// (if empty) POPULATE METADATA DB -> METADATA TABLE -> 2 rows
			$insert_metadata_tbl = "
				IF NOT EXISTS (SELECT * FROM DbTable)
				BEGIN;
					INSERT INTO dbo.DbTable (DbName, IsCurrent) VALUES ('".SQL_SERVER_LACDATABASE_ML_DEVELOPMENT_no_drop_01."', 0);
					INSERT INTO dbo.DbTable (DbName, IsCurrent) VALUES ('".SQL_SERVER_LACDATABASE_ML_DEVELOPMENT_no_drop_02."', 1);
				END;
			";
			$srvr_stmt = sqlsrv_query( $conn, $insert_metadata_tbl );
			if( $srvr_stmt === false ) { die( print_r( sqlsrv_errors(), true)); }

			updateProgressBar( intval(9), ((string)$row_counter." row(s) out of ".(string)$total_num_of_rows." processed.") );

			// FOR PROGRESS BAR >>>
			// $percent = intval(9); // roughly say, 9% of work is done up to now
			// $arr_content['percent'] = $percent;
			// $arr_content['message'] = $row_counter." row(s) out of ".(string)$total_num_of_rows." processed.";
			// file_put_contents("D:/mianlu/ProgressBar.txt", json_encode($arr_content)); // Write the progress into D:/mianlu/ProgressBar.txt and serialize the PHP array into JSON format.
			// FOR PROGRESS BAR END <<<
		}

		// step 3 - if not exist create db1
		if(true) {
			$create_db1 = "IF (db_id(N'".SQL_SERVER_LACDATABASE_ML_DEVELOPMENT_no_drop_01."') IS NULL) CREATE DATABASE ".SQL_SERVER_LACDATABASE_ML_DEVELOPMENT_no_drop_01;
			$srvr_stmt = sqlsrv_query( $conn, $create_db1 );
			if( $srvr_stmt === false ) { die( print_r( sqlsrv_errors(), true)); }
		}

		// step 4 - if not exist create db2
		if(true) {
			$create_db2 = "IF (db_id(N'".SQL_SERVER_LACDATABASE_ML_DEVELOPMENT_no_drop_02."') IS NULL) CREATE DATABASE ".SQL_SERVER_LACDATABASE_ML_DEVELOPMENT_no_drop_02;
			$srvr_stmt = sqlsrv_query( $conn, $create_db2 );
			if( $srvr_stmt === false ) { die( print_r( sqlsrv_errors(), true)); }
		}

		// step 5 - all 3 databases and DbTable should exist right now, PLEASE reconnect $conn to metadata db, and start reading
		if(true) {
			$connectionInfo = array( "Database"=>SQL_SERVER_LACDATABASE_ML_DEVELOPMENT_no_drop_00, "UID"=>SQL_SERVER_USERNAME, "PWD"=>SQL_SERVER_PASSWORD);
			$conn = sqlsrv_connect( SQL_SERVER_NAME, $connectionInfo);
			if( $conn ) {
				$log_append_string = "SQL Server connection to METADATA DATABASE established.\r\n";
				if ( false === file_put_contents($log_file, $log_append_string, FILE_APPEND | LOCK_EX) ) die();
			}
			else {
				$log_append_string = "SQL Server connection to METADATA DATABASE could not be established.\r\n";
				if ( false === file_put_contents($log_file, $log_append_string, FILE_APPEND | LOCK_EX) ) die();
				die( print_r( sqlsrv_errors(), true));
			}
			$current_db_query = "SELECT DbName FROM DbTable WHERE IsCurrent = 0";
			$srvr_stmt = sqlsrv_query( $conn, $current_db_query );
			if( $srvr_stmt === false ) { die( print_r( sqlsrv_errors(), true)); }
			$CurrentDatabase = (string)"";
			$error_checker = (int) 0;
			while( $temp_row = sqlsrv_fetch_object( $srvr_stmt )) {
				$error_checker ++;
				$CurrentDatabase = $temp_row->DbName;
			}
			$CurrentDatabase = (string)$CurrentDatabase;
			sqlsrv_close($conn); // close connection to Metadata Database
			unset($conn);
			if($error_checker != 1) die("ERROR in METADATA DATABASE! Please ask development team to troubleshoot! Exactly 1 entry in Metadata Table should contain IsCurrent = 1 and Exactly 1 entry should contain IsCurrent = 0");
		}

		// Step 6 - open connection to the non-current database so that if sth bad occurs during import, roll-back to previous database!
		if(true) {
			$connectionInfo = array( "Database"=>$CurrentDatabase, "UID"=>SQL_SERVER_USERNAME, "PWD"=>SQL_SERVER_PASSWORD);
			$conn = sqlsrv_connect( SQL_SERVER_NAME, $connectionInfo);
			if( $conn ) {
				$log_append_string = "SQL Server connection to CURRENT DATABASE established.\r\n";
				if ( false === file_put_contents($log_file, $log_append_string, FILE_APPEND | LOCK_EX) ) die();
			}
			else {
				$log_append_string = "SQL Server connection to CURRENT DATABASE could not be established.\r\n";
				if ( false === file_put_contents($log_file, $log_append_string, FILE_APPEND | LOCK_EX) ) die();
				die( print_r( sqlsrv_errors(), true));
			}
		}

		// Step 7 - progress bar update
		if(true) {
			updateProgressBar( intval(11), ((string)$row_counter." row(s) out of ".(string)$total_num_of_rows." processed.") );

			// FOR PROGRESS BAR >>>
			// $percent = intval(11); // roughly say, 11% of work is done up to now
			// $arr_content['percent'] = $percent;
			// $arr_content['message'] = $row_counter." row(s) out of ".(string)$total_num_of_rows." processed.";
			// file_put_contents("D:/mianlu/ProgressBar.txt", json_encode($arr_content)); // Write the progress into D:/mianlu/ProgressBar.txt and serialize the PHP array into JSON format.
			// FOR PROGRESS BAR END <<<
		}
	}

	//	III.	SQL Server - create 5 empty tables
	if(true) {
		// 1. create queries to drop existing tables with same names that we're gonna create
		$drop_CH    = "IF OBJECT_ID('dbo.CertHistory', 'U') IS NOT NULL DROP TABLE dbo.CertHistory";
		$drop_CD    = "IF OBJECT_ID('dbo.CourseDetail', 'U') IS NOT NULL DROP TABLE dbo.CourseDetail";
		$drop_COL   = "IF OBJECT_ID('dbo.CarryoverLimits', 'U') IS NOT NULL DROP TABLE dbo.CarryoverLimits";
		$drop_EX    = "IF OBJECT_ID('dbo.EmployeeID_Xref', 'U') IS NOT NULL DROP TABLE dbo.EmployeeID_Xref";
		$drop_E     = "IF OBJECT_ID('dbo.Employee', 'U') IS NOT NULL DROP TABLE dbo.Employee";
		// 2. run the above queries
		$srvr_stmt = sqlsrv_query( $conn, $drop_CH );
		if( $srvr_stmt === false ) { die( print_r( sqlsrv_errors(), true)); }
		$srvr_stmt = sqlsrv_query( $conn, $drop_CD );
		if( $srvr_stmt === false ) { die( print_r( sqlsrv_errors(), true)); }
		$srvr_stmt = sqlsrv_query( $conn, $drop_COL );
		if( $srvr_stmt === false ) { die( print_r( sqlsrv_errors(), true)); }
		$srvr_stmt = sqlsrv_query( $conn, $drop_EX );
		if( $srvr_stmt === false ) { die( print_r( sqlsrv_errors(), true)); }
		$srvr_stmt = sqlsrv_query( $conn, $drop_E );
		if( $srvr_stmt === false ) { die( print_r( sqlsrv_errors(), true)); }
		// 3. create table 3 Employee (DO THIS FIRST! CertNo need to be a foreign key for all other tables!)
		$create_E = "CREATE TABLE dbo.Employee (
			CertNo float,                       --dbo.Summary (dbo.AnnualReq/Details also contain, but should be all duplicates, doublecheck!)
			FirstName nvarchar(50),             --dbo.Summary
			MiddleName nvarchar(50),            --dbo.Summary
			LastName nvarchar(50),              --dbo.Summary
			TempCertDate datetime2(0),          --dbo.AnnualReq
			PermCertDate datetime2(0),          --dbo.AnnualReq
			AdvCertDate datetime2(0),           --dbo.AnnualReq
			CurrentStatus nvarchar(50),         --dbo.AnnualReq
			Auditor nvarchar(50),               --dbo.Summary
			-- ml: below are Mian's suggested extra columns from earlier (not necessary!)
			-- CountyCode nvarchar(2),          --dbo.Summary
			-- CurrFiscalYear nvarchar(10),     --dbo.Summary
			-- CurrCertType                     --contained in dbo.AnnualReq, not unique! i.e. one employee can have multiple types, all active!
			primary key (CertNo),               -- CertNo should be foreign key
		)";
		// 4. create table 1 CourseDetail
		$create_CD = "CREATE TABLE dbo.CourseDetail (
			CertNo float references dbo.Employee(CertNo), -- foreign key from Employee
			CourseYear nvarchar(10),            -- from dbo.Details(FiscalYear)
			ItemNumber float,                   -- undefined, maybe an index for all possible courses, if so then new table all_courses needed
			CourseName nvarchar(100),           -- from dbo.tbl: Course Title + dbo.Details(Course)
			CourseLocation nvarchar(50),        -- from dbo.Details(Location)
			CourseGrade nvarchar(50),           -- from dbo.Details(Grade)
			CourseHours float,                  -- from dbo.Details(HoursEarned)
			EndDate datetime2(0),               -- from dbo.Details(EndDate) ( TOT: datetime2(0) )
			-- primary key (CertNo, CourseYear, CourseName), -- if ItemNumber correctly implemented, switch to it! CertNo should be foreign key
		)";
		// 5. create table 4 CarryoverLimits
		$create_COL = "CREATE TABLE dbo.CarryoverLimits (
			[Status] nvarchar(50),              -- primary key from dbo.CertHistory, referenced in CertHistory(Status)
			RequiredHours float,                -- undefined
			Year1Limit float,                   -- undefined
			Year2Limit float,                   -- undefined
			Year3Limit float,                   -- undefined
			-- primary key ([Status]),
		)";
		// 6. create table 2 CertHistory
		$create_CH = "CREATE TABLE dbo.CertHistory (
			CertNo float references dbo.Employee(CertNo), -- foreign key from Employee
			CertYear nvarchar(10),              -- from dbo.AnnualReq
			CertType nvarchar(50),              -- from dbo.AnnualReq
			-- [Status] nvarchar(50) references dbo.CarryoverLimits([Status]),              -- from dbo.AnnualReq
			[Status] nvarchar(50),              -- from dbo.AnnualReq
			HoursEarned float,                  -- from dbo.AnnualReq
			RequiredHours float,
			CurrentYearBalance float,           -- from dbo.AnnualReq
			PriorYearBalance float,             -- from dbo.AnnualReq
			CarryToYear1 float,                 -- from dbo.AnnualReq
			CarryToYear2 float,                 -- from dbo.AnnualReq
			CarryToYear3 float,                 -- from dbo.AnnualReq
			CarryForwardTotal float,            -- from dbo.AnnualReq
			constraint PK_CERTNO_CERTYEAR primary key (CertNo, CertYear), -- CertNo should be foreign key
		)";
		// 7. create table 5 EmployeeID_Xref
		$create_EX = "CREATE TABLE dbo.EmployeeID_Xref (
			CertNo float references dbo.Employee(CertNo),
			EmployeeID float,
			primary key (CertNo),
			unique (EmployeeID),
		)";
		// 8. run table-creating queries from 3.~7.
		$srvr_stmt = sqlsrv_query( $conn, $create_E );
		if( $srvr_stmt === false ) { die( print_r( sqlsrv_errors(), true)); }
		$srvr_stmt = sqlsrv_query( $conn, $create_CD );
		if( $srvr_stmt === false ) { die( print_r( sqlsrv_errors(), true)); }
		$srvr_stmt = sqlsrv_query( $conn, $create_COL );
		if( $srvr_stmt === false ) { die( print_r( sqlsrv_errors(), true)); }
		$srvr_stmt = sqlsrv_query( $conn, $create_CH );
		if( $srvr_stmt === false ) { die( print_r( sqlsrv_errors(), true)); }
		$srvr_stmt = sqlsrv_query( $conn, $create_EX );
		if( $srvr_stmt === false ) { die( print_r( sqlsrv_errors(), true)); }

		updateProgressBar( intval(15), ((string)$row_counter." row(s) out of ".(string)$total_num_of_rows." processed.") );

		// FOR PROGRESS BAR >>>
		// $percent = intval(15); // roughly say, 15% of work is done up to now
		// $arr_content['percent'] = $percent;
		// $arr_content['message'] = $row_counter." row(s) out of ".(string)$total_num_of_rows." processed.";
		// file_put_contents("D:/mianlu/ProgressBar.txt", json_encode($arr_content)); // Write the progress into D:/mianlu/ProgressBar.txt and serialize the PHP array into JSON format.
		// FOR PROGRESS BAR END <<<
	}

	//	IV.		Read data from xlsx or mdb, and then insert into SQL sever
	if(true) {
		// 1. Summary & AnnualReq -> Employee: START
		if(DO_STEP_1) {
			$log_append_string = "===== Start inserting Summary into Employee =====\r\n";
			if ( false === file_put_contents($log_file, $log_append_string, FILE_APPEND | LOCK_EX) ) die();
			// Tricky work: create table Temp to store all CertNo + 3 Dates + CurrentStatus; then create Temp2 to store distinct rows from Temp;
			// then insert into Employee row by row from Summary, while querying Temp2 for the 3 dates
			// TrickyWork Pt.1: create table Temp
			if(true) {
				$drop_temp  = "IF OBJECT_ID('dbo.Temp', 'U') IS NOT NULL DROP TABLE dbo.Temp";
				$srvr_stmt = sqlsrv_query( $conn, $drop_temp );
				if( $srvr_stmt === false ) { die( print_r( sqlsrv_errors(), true)); }
				$create_temp = "CREATE TABLE dbo.Temp (
					CertNo float,                       --dbo.Summary (dbo.AnnualReq/Details also contain, but should be all duplicates, doublecheck!)
					TempCertDate datetime2(0),          --dbo.AnnualReq
					PermCertDate datetime2(0),          --dbo.AnnualReq
					AdvCertDate datetime2(0),           --dbo.AnnualReq
					CurrentStatus nvarchar(50),         --dbo.AnnualReq
				)";
				$srvr_stmt = sqlsrv_query( $conn, $create_temp );
				if( $srvr_stmt === false ) { die( print_r( sqlsrv_errors(), true)); }
				updateProgressBar( intval(16), ((string)$row_counter." row(s) out of ".(string)$total_num_of_rows." processed.") );
			}
			// TrickyWork Pt.2: populate Temp
			if(true) {
				$srvr_query = "INSERT INTO Temp (CertNo, TempCertDate, PermCertDate, AdvCertDate, CurrentStatus)";
				$srvr_query .= " VALUES (?,?,?,?,?)";
				//////////////////// lazy-reading INIT ////////////////////
				$excelReader_AnnualReq  = PHPExcel_IOFactory::createReader('Excel2007'); // $excelReader_AnnualReq  ->setReadDataOnly(true);
				$excelObj_AnnualReq     = $excelReader_AnnualReq->load($var_path_ANNUALREQ);
				$annualreq              = $excelObj_AnnualReq->getActiveSheet();
				//////////////////// lazy-reading READY ////////////////////
				$row_count = (int)2; // actual data starts at row 2 of Excel spreadsheet
				updateProgressBar( intval(17), ((string)$row_counter." row(s) out of ".(string)$total_num_of_rows." processed.") );
				while ( $row_count <= $annualreq->getHighestRow() ) { // read until the last line
					// select distinct CertID rows from AnnualReq
					$CertNo         = $annualreq->getCell('D'.$row_count)->getValue();
					// 3 tricky dates
					// one weird bug: getCell and getValue naively would result in today's date being inserted into Temp! which would mess
					// up everything from this point onwards in php code execution!
					// TempCertDate
					$TempCell       = $annualreq->getCell('G'.$row_count);
					$TempCertDate   = $TempCell->getValue();
					if($TempCertDate == NULL) {
						if(PRINT_NOTES) {
							$log_append_string = "NOTE: Appraiser ".$CertNo." has NULL in TempCertDate!\r\n";
							if ( false === file_put_contents($log_file, $log_append_string, FILE_APPEND | LOCK_EX) ) die();
						}
					}
					// note for below: only convert cell to datetime2 variable if cell is not NULL, otherwise insert NULL into database
					else if(PHPExcel_Shared_Date::isDateTime($TempCell)) $TempCertDate = date($format = "m-d-Y", PHPExcel_Shared_Date::ExcelToPHP($TempCertDate));
					// PermCertDate
					$PermCell       = $annualreq->getCell('H'.$row_count);
					$PermCertDate   = $PermCell->getValue();
					if($PermCertDate == NULL) {
						if(PRINT_NOTES) {
							$log_append_string = "NOTE: Appraiser ".$CertNo." has NULL in PermCertDate!\r\n";
							if ( false === file_put_contents($log_file, $log_append_string, FILE_APPEND | LOCK_EX) ) die();
						}
					}
					else if(PHPExcel_Shared_Date::isDateTime($PermCell)) $PermCertDate = date($format = "m-d-Y", PHPExcel_Shared_Date::ExcelToPHP($PermCertDate));
					// AdvCertDate
					$AdvCell        = $annualreq->getCell('I'.$row_count);
					$AdvCertDate    = $AdvCell->getValue();
					if($AdvCertDate == NULL) {
						if(PRINT_NOTES) {
							$log_append_string = "NOTE: Appraiser ".$CertNo." has NULL in AdvCertDate!\r\n";
							if ( false === file_put_contents($log_file, $log_append_string, FILE_APPEND | LOCK_EX) ) die();
						}
					}
					else if(PHPExcel_Shared_Date::isDateTime($AdvCell)) $AdvCertDate = date($format = "m-d-Y", PHPExcel_Shared_Date::ExcelToPHP($AdvCertDate));
					// CurrentStatus
					$CurrentStatus  = $annualreq->getCell('J'.$row_count)->getValue();
					// inserting operation
					$params         = array($CertNo, $TempCertDate, $PermCertDate, $AdvCertDate, $CurrentStatus);
					$stmt           = sqlsrv_query( $conn, $srvr_query, $params);
					if( $stmt === false ) { die( print_r(sqlsrv_errors(), true) ); }
					$row_count ++;
				}
				unset($excelObj_AnnualReq); //////////////////// lazy-reading END

				updateProgressBar( intval(18), ((string)$row_counter." row(s) out of ".(string)$total_num_of_rows." processed.") );

				// FOR PROGRESS BAR >>>
				// $percent = intval(18); // roughly say, 18% of work is done up to now
				// $arr_content['percent'] = $percent;
				// $arr_content['message'] = $row_counter." row(s) out of ".(string)$total_num_of_rows." processed.";
				// file_put_contents("D:/mianlu/ProgressBar.txt", json_encode($arr_content)); // Write the progress into D:/mianlu/ProgressBar.txt and serialize the PHP array into JSON format.
				// FOR PROGRESS BAR END <<<
			}
			// TrickyWork Pt.3: create table Temp2
			if(true) {
				$drop_temp2 = "IF OBJECT_ID('dbo.Temp2', 'U') IS NOT NULL DROP TABLE dbo.Temp2";
				$srvr_stmt = sqlsrv_query( $conn, $drop_temp2 );
				if( $srvr_stmt === false ) { die( print_r( sqlsrv_errors(), true)); }
				$create_temp2 = "CREATE TABLE dbo.Temp2 (
					CertNo float,
					TempCertDate datetime2(0),
					PermCertDate datetime2(0),
					AdvCertDate datetime2(0),
					CurrentStatus nvarchar(50),
				)";
				$srvr_stmt = sqlsrv_query( $conn, $create_temp2 );
				if( $srvr_stmt === false ) { die( print_r( sqlsrv_errors(), true)); }
			}
			// TrickyWork Pt.4: populate Temp2
			if(true) {
				$select_query = "SELECT DISTINCT * FROM Temp ORDER BY CertNo";
				$insert_query = "INSERT INTO Temp2 (CertNo, TempCertDate, PermCertDate, AdvCertDate, CurrentStatus) VALUES (?,?,?,?,?)";
				// BLOCK looping thru query selected lines and manipulate data fetched!
				if(($result = sqlsrv_query($conn, $select_query)) !== false){
					while( $temp_row = sqlsrv_fetch_object( $result )) {
						$params = array($temp_row->CertNo, $temp_row->TempCertDate, $temp_row->PermCertDate, $temp_row->AdvCertDate, $temp_row->CurrentStatus);
						$stmt = sqlsrv_query($conn, $insert_query, $params);
						if( $stmt === false ) { die( print_r(sqlsrv_errors(), true) ); }
					}
				}

				updateProgressBar( intval(20), ((string)$row_counter." row(s) out of ".(string)$total_num_of_rows." processed.") );

				// FOR PROGRESS BAR >>>
				// $percent = intval(20); // roughly say, 20% of work is done up to now
				// $arr_content['percent'] = $percent;
				// $arr_content['message'] = $row_counter." row(s) out of ".(string)$total_num_of_rows." processed.";
				// file_put_contents("D:/mianlu/ProgressBar.txt", json_encode($arr_content)); // Write the progress into D:/mianlu/ProgressBar.txt and serialize the PHP array into JSON format.
				// FOR PROGRESS BAR END <<<
			}
			// TrickyWork Pt.5: insert into Employee table from summary.xlsx (line-by-line) & Temp2 (querying 3 dates + CurrentStatus along with each line)
			if(true) {
				//////////////////// lazy-reading INIT ////////////////////
				$excelReader_Summary    = PHPExcel_IOFactory::createReader('Excel2007'); // $excelReader_Summary->setReadDataOnly(true);
				$excelObj_Summary       = $excelReader_Summary->load($var_path_SUMMARY);
				$summary                = $excelObj_Summary->getActiveSheet();
				//////////////////// lazy-reading READY ////////////////////
				$row_count = (int)2;
				while ( $row_count <= $summary->getHighestRow() ) { // read until the last line
					$params     = NULL;
					$CertNo     = $summary->getCell('G'.$row_count)->getValue();
					$LastName   = $summary->getCell('D'.$row_count)->getValue();
					$MiddleName = $summary->getCell('F'.$row_count)->getValue();
					$FirstName  = $summary->getCell('E'.$row_count)->getValue();
					$Auditor    = $summary->getCell('H'.$row_count)->getValue();
					$Select_Temp2_Dates = "SELECT TempCertDate, PermCertDate, AdvCertDate, CurrentStatus FROM Temp2";
					$Select_Temp2_Dates .= " WHERE CertNo = ";
					$Select_Temp2_Dates .= $CertNo;
					// use a counter to check for conflicting dates across Temp2:
					// ideally, Temp2 table should never contain multiple rows with the same CertNo, since in Temp,
					// each person i.e. CertNo should have all rows with the same 3 dates; however if this is not the case,
					// then Temp2 (which contains all distinct rows from Temp) would contain multiple rows with the same CertNo but different
					// 3 dates, which is an error in BOE data. This integer counter would detect it.
					$errorcheck_counter = (int)0;
					$stmt = sqlsrv_query($conn, $Select_Temp2_Dates); // TOT
					if( $stmt === false ) die( print_r(sqlsrv_errors(), true) ); // TOT generic 2-line-template for executing SQL Server query

					while( $temp_row = sqlsrv_fetch_object( $stmt )) { // $temp_row should have 3 columns
						$errorcheck_counter ++;
						$params = array($CertNo, $LastName, $MiddleName, $FirstName, $Auditor, $temp_row->TempCertDate, $temp_row->PermCertDate, $temp_row->AdvCertDate, $temp_row->CurrentStatus);
					}
					if($errorcheck_counter != 1) {
						$log_append_string = "ERROR: defective data from annualreq.xlsx! conflicting 3-date tuples\r\n";
						if ( false === file_put_contents($log_file, $log_append_string, FILE_APPEND | LOCK_EX) ) die();
					}
					$Summary_to_Employee = "INSERT INTO Employee (CertNo, LastName, MiddleName, FirstName, Auditor,
																	  TempCertDate, PermCertDate, AdvCertDate, CurrentStatus)
																	  VALUES (?,?,?,?,?,?,?,?,?)";
					if( $stmt = sqlsrv_query( $conn, $Summary_to_Employee, $params) === false ) die( print_r(sqlsrv_errors(), true) );
					$row_count ++;


					// FOR PROGRESS BAR >>>
					$row_counter += 1;
					updateProgressBar( intval(($row_counter/$total_num_of_rows * 100) * 0.75 + 20), ((string)$row_counter." row(s) out of ".(string)$total_num_of_rows." processed.") );
					// $percent = intval(($row_counter/$total_num_of_rows * 100) * 0.75 + 20); // consider 0.75 weight on actual insertion, plus assigned 20% progress already completed by initialization, plus 5% final clean-up & update metadata work
					// $arr_content['percent'] = $percent;
					// $arr_content['message'] = $row_counter . " row(s) processed.";
					// file_put_contents("D:/mianlu/ProgressBar.txt", json_encode($arr_content)); // Write the progress into D:/mianlu/ProgressBar.txt and serialize the PHP array into JSON format.
					// FOR PROGRESS BAR END <<<
				}
				unset($excelObj_Summary); //////////////////// lazy-reading END
			}
			$log_append_string = "===== Summary into Employee finished, ".($row_count-2)." rows inserted. =====\r\n";
			if ( false === file_put_contents($log_file, $log_append_string, FILE_APPEND | LOCK_EX) ) die();
		}  // Summary & AnnualReq -> Employee: DONE

		// 2. AnnualReq -> CertHistory: START
		if(DO_STEP_2) {
			$srvr_query = "INSERT INTO CertHistory (CertNo, CertYear, CertType, Status, HoursEarned, RequiredHours,
			CurrentYearBalance, PriorYearBalance, CarryToYear1,
			CarryToYear2, CarryToYear3, CarryForwardTotal)";
			$srvr_query .= " VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
			$log_append_string = "===== Start inserting AnnualReq into CertHistory =====\r\n";
			if ( false === file_put_contents($log_file, $log_append_string, FILE_APPEND | LOCK_EX) ) die();
			//////////////////// lazy-reading INIT ////////////////////
			$excelReader_AnnualReq  = PHPExcel_IOFactory::createReader('Excel2007'); // $excelReader_AnnualReq->setReadDataOnly(true);
			$excelObj_AnnualReq     = $excelReader_AnnualReq->load($var_path_ANNUALREQ);
			$annualreq              = $excelObj_AnnualReq->getActiveSheet();
			//////////////////// lazy-reading READY ////////////////////
			$row_count = (int)2;
			while ( $row_count <= $annualreq->getHighestRow() ) { // read until the last line
				// int counter logic end
				$CertNo             = $annualreq->getCell('D'.$row_count)->getValue();
				$CertYear           = $annualreq->getCell('M'.$row_count)->getValue();
				$CertType           = $annualreq->getCell('L'.$row_count)->getValue();
				$Status             = $annualreq->getCell('K'.$row_count)->getValue();
				$HoursEarned        = $annualreq->getCell('N'.$row_count)->getValue();
				$RequiredHours      = $annualreq->getCell('O'.$row_count)->getValue();
				$CurrentYearBalance = $annualreq->getCell('P'.$row_count)->getValue();
				$PriorYearBalance   = $annualreq->getCell('Q'.$row_count)->getValue();
				$CarryToYear1       = $annualreq->getCell('R'.$row_count)->getValue();
				$CarryToYear2       = $annualreq->getCell('S'.$row_count)->getValue();
				$CarryToYear3       = $annualreq->getCell('T'.$row_count)->getValue();
				$CarryForwardTotal  = $annualreq->getCell('U'.$row_count)->getValue();
				$params = array($CertNo, $CertYear, $CertType, $Status, $HoursEarned, $RequiredHours,
				$CurrentYearBalance, $PriorYearBalance, $CarryToYear1,$CarryToYear2, $CarryToYear3,
				$CarryForwardTotal);
				$stmt = sqlsrv_query( $conn, $srvr_query, $params);
				if( $stmt === false ) die( print_r(sqlsrv_errors(), true) );
				$row_count ++;
				// FOR PROGRESS BAR >>>
				$row_counter += 1;
				updateProgressBar( intval(($row_counter/$total_num_of_rows * 100) * 0.75 + 20), ((string)$row_counter." row(s) out of ".(string)$total_num_of_rows." processed.") );

				// $percent = intval(($row_counter/$total_num_of_rows * 100) * 0.75 + 20); // consider 0.75 weight on actual insertion, plus assigned 20% progress already completed by initialization, plus 5% final clean-up & update metadata work
				// $arr_content['percent'] = $percent;
				// $arr_content['message'] = $row_counter . " row(s) processed.";
				// file_put_contents("D:/mianlu/ProgressBar.txt", json_encode($arr_content)); // Write the progress into D:/mianlu/ProgressBar.txt and serialize the PHP array into JSON format.
				// FOR PROGRESS BAR END <<<
			}
			unset($excelObj_AnnualReq); //////////////////// lazy-reading END
			$log_append_string = "===== AnnualReq into CertHistory finished, ".($row_count-2)." rows inserted. =====\r\n";
			if ( false === file_put_contents($log_file, $log_append_string, FILE_APPEND | LOCK_EX) ) die();
		}  // AnnualReq pt1 - AnnualReq -> CertHistory: DONE

		// 3. Details -> CourseDetail: START
		if(DO_STEP_3) {
			$srvr_query = "INSERT INTO CourseDetail (CertNo, CourseYear, --ItemNumber,
														CourseName, CourseLocation, CourseGrade, CourseHours, EndDate)";
			$srvr_query .= " VALUES (?,?,?,?,?,?,?)";
			$log_append_string = "===== Start inserting Details into CourseDetail =====\r\n";
			if ( false === file_put_contents($log_file, $log_append_string, FILE_APPEND | LOCK_EX) ) die();
			//////////////////// lazy-reading INIT ////////////////////
			$excelReader_Details    = PHPExcel_IOFactory::createReader('Excel2007'); // $excelReader_Details->setReadDataOnly(true);
			$excelObj_Details       = $excelReader_Details->load($var_path_DETAILS);
			$details                = $excelObj_Details->getActiveSheet();
			//////////////////// lazy-reading READY ////////////////////
			$row_count = (int)2;
			while ( $row_count <= $details->getHighestRow() ) { // read until the last line
				$CertNo             = $details->getCell('C'.$row_count)->getValue();
				$CourseYear         = $details->getCell('G'.$row_count)->getValue();
				// $ItemNumber
				$CourseName         = $details->getCell('I'.$row_count)->getValue();
				$CourseLocation     = $details->getCell('J'.$row_count)->getValue();
				$CourseGrade        = $details->getCell('K'.$row_count)->getValue();
				$CourseHours        = $details->getCell('L'.$row_count)->getValue();
				// $EndDate         = $details->getCell('H'.$row_count)->getValue();
				// EndDate
				$EndDateCell        = $details->getCell('H'.$row_count);
				$EndDate            = $EndDateCell->getValue();
				if($EndDate == NULL) {
					if(PRINT_NOTES) {
						$log_append_string = "NOTE: appraiser ".$CertNo." has NULL EndDate in course \"".$CourseName."\" at \"".$CourseLocation."\" !\r\n";
						if ( false === file_put_contents($log_file, $log_append_string, FILE_APPEND | LOCK_EX) ) die();
					}
				}
				// note for below: only convert cell to datetime2 variable if cell is not NULL, otherwise insert NULL into database
				else if(PHPExcel_Shared_Date::isDateTime($EndDateCell)) $EndDate = date($format = "m-d-Y", PHPExcel_Shared_Date::ExcelToPHP($EndDate));

				$params = array($CertNo, $CourseYear, $CourseName, $CourseLocation, $CourseGrade, $CourseHours, $EndDate);
				$stmt = sqlsrv_query( $conn, $srvr_query, $params);
				if( $stmt === false ) die( print_r(sqlsrv_errors(), true) );
				$row_count ++;
				// FOR PROGRESS BAR >>>
				$row_counter += 1;
				updateProgressBar( intval(($row_counter/$total_num_of_rows * 100) * 0.75 + 20), ((string)$row_counter." row(s) out of ".(string)$total_num_of_rows." processed.") );

				// $percent = intval(($row_counter/$total_num_of_rows * 100) * 0.75 + 20); // consider 0.75 weight on actual insertion, plus assigned 20% progress already completed by initialization, plus 5% final clean-up & update metadata work
				// $arr_content['percent'] = $percent;
				// $arr_content['message'] = $row_counter . " row(s) processed.";
				// file_put_contents("D:/mianlu/ProgressBar.txt", json_encode($arr_content)); // Write the progress into D:/mianlu/ProgressBar.txt and serialize the PHP array into JSON format.
				// FOR PROGRESS BAR END <<<
			}
			unset($excelObj_Details); //////////////////// lazy-reading END
			$log_append_string = "===== Details into CourseDetail finished, ".($row_count-2)." rows inserted. =====\r\n";
			if ( false === file_put_contents($log_file, $log_append_string, FILE_APPEND | LOCK_EX) ) die();
		}  // Details -> CourseDetail: DONE
	}


	//	V.		Clean up: close "current table" connection and update Metadata DB
	if(DO_STEP_1 && DO_STEP_2 && DO_STEP_3) {
		// delete table Temp and Temp2 from current database
		if(DO_CLEANUP) {
			$drop_temp  = "IF OBJECT_ID('dbo.Temp', 'U') IS NOT NULL DROP TABLE dbo.Temp";
			$srvr_stmt = sqlsrv_query( $conn, $drop_temp );
			if( $srvr_stmt === false ) { die( print_r( sqlsrv_errors(), true)); }
			$drop_temp2 = "IF OBJECT_ID('dbo.Temp2', 'U') IS NOT NULL DROP TABLE dbo.Temp2";
			$srvr_stmt = sqlsrv_query( $conn, $drop_temp2 );
			if( $srvr_stmt === false ) { die( print_r( sqlsrv_errors(), true)); }
		}
		// close connection to the new Current Database
		sqlsrv_close($conn);
		unset($conn);
		// now, since import operation finished without error, update Metadata Database to point to the new Current Database
		$connectionInfo = array( "Database"=>SQL_SERVER_LACDATABASE_ML_DEVELOPMENT_no_drop_00, "UID"=>SQL_SERVER_USERNAME, "PWD"=>SQL_SERVER_PASSWORD);
		$conn = sqlsrv_connect( SQL_SERVER_NAME, $connectionInfo);
		if( $conn ) {
			$log_append_string = "SQL Server connection to METADATA DATABASE established.\r\n";
			if ( false === file_put_contents($log_file, $log_append_string, FILE_APPEND | LOCK_EX) ) die();
		}
		else {
			$log_append_string = "SQL Server connection to METADATA DATABASE could not be established.\r\n";
			if ( false === file_put_contents($log_file, $log_append_string, FILE_APPEND | LOCK_EX) ) die();
			die( print_r( sqlsrv_errors(), true));
		}
		// a clever way to toggle IsCurrent for db1 and db2
		$update_current_db_query = "UPDATE DbTable SET IsCurrent = 1 - IsCurrent"; // previous "current database", gets IsCurrent = 0
		$srvr_stmt = sqlsrv_query( $conn, $update_current_db_query );
		if( $srvr_stmt === false ) { die( print_r( sqlsrv_errors(), true)); }
		$log_append_string = "Switching the old CURRENT DATABASE to the new CURRENT DATABASE done!\r\n";
		if ( false === file_put_contents($log_file, $log_append_string, FILE_APPEND | LOCK_EX) ) die();
		// yh: code to update database information on "which historical upload operation is the current database based on?"
		$d = $_POST['dir'];
		$d = str_replace(UPLOADED_FILES_DIR,"",$d);
		$d = str_replace("/","",$d);
		$update_current_db_query = "UPDATE UploadedDatabaseFiles SET ifCurrentDatabase = 'False';
									UPDATE UploadedDatabaseFiles SET ifCurrentDatabase = 'True' WHERE [uploadedTimestamp] = '".$d."'";
		$srvr_stmt = sqlsrv_query( $conn, $update_current_db_query );
		if( $srvr_stmt === false ) { die( print_r( sqlsrv_errors(), true)); }
		// close connection to Metadata Database
		sqlsrv_close($conn);
		unset($conn);
	}
	// put the final progress bar update outside of step V.
	sleep(1); // give a short delay for front end so admin won't be confused
	updateProgressBar( intval(100), ((string)$row_counter." row(s) out of ".(string)$total_num_of_rows." processed.") ); // 20% initialization + 75% row insertion + 5% clean up

	//	VI.		Other notes below...
	/* // block comment starter
	// */ // ml: DO NOT DELETE THIS LINE! this is a convenient comment ender for anywhere in the php block.
	// 128M: Fatal error: Allowed memory size of 134217728 bytes exhausted (tried to allocate 4096 bytes)
	// 256M: Fatal error: Allowed memory size of 268435456 bytes exhausted (tried to allocate 1576960 bytes)
	// 512M: works just fine for now!
?>
