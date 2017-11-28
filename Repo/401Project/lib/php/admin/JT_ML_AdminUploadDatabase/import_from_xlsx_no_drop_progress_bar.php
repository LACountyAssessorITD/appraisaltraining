<?php
	// Start the session.
	session_start();

	// To get uploaded files directory
	$dir = $_POST['dir']; // e.g. dir = "D:/temp/1599028283" which contains 3 xlsx files

	// We have to make sure there is no garbage left here.
	// Failed process will leave the file undeleted.
	// So, we need to delete the file that older than 2 days.
	// $files = glob("D:/t/*");
	// $now   = time();

	// foreach ($files as $file) {
	//   if (is_file($file)) {
	//     if ($now - filemtime($file) >= 60 * 60 * 24 * 2) { // 2 days and older
	//       unlink($file);
	//     }
	//   }
	// }

	// // The example total processes.
	// // $total = 20;

	// // The array for storing the progress.
	// $arr_content = array();

	// // Loop through process
	// for($i=1; $i<=$total; $i++){
	// 	// Calculate the percentation
	// 	$percent = intval($overall_row_counter/$total_num_of_rows * 100);

	// 	// Put the progress percentage and message to array.
	// 	$arr_content['percent'] = $percent;
	// 	$arr_content['message'] = $i . " row(s) processed.";

	// 	// Write the progress into file and serialize the PHP array into JSON format.
	// 	// The file name is the session id.
	// 	file_put_contents("D:/t/log.txt", json_encode($arr_content));

	// 	// Sleep one second so we can see the delay
	// 	sleep(1);
	// }

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// for LA County Assessor's Office - Appraisal Training Record Tracking System use only!
	// updated by James Tseng and Mian Lu
	// last edit October 2017

	// mianlu: NOTE: if(true/$variable){ } blocks are used to foster code snippit folding with Sublime functionalities.

	// put include_once statements here:
	include_once "../../constants.php";

	// Initialization Pt 1 - declare variables, constants, and flags
	ini_set('memory_limit', '512M'); // TOT optimize more?
	$do_step_1  = true;
	$do_step_2  = false;
	$do_step_3  = false;
	$do_cleanup = false;
	$total_num_of_rows = intval(0);
	$overall_row_counter = intval(0);
	$progress_bar_arr_content = array(); // variable to write out current percentage (of line-by-line insertion) to a file, which is then read from importing webpage's progress bar
	// FOR PRORESS BAR >>>
	$percent = intval(0); // make the progress bar show a 0% early on!
	$arr_content['percent'] = $percent;
	$arr_content['message'] = $overall_row_counter . " row(s) processed.";
	file_put_contents("D:/t/log.txt", json_encode($arr_content)); // Write the progress into D:/t/log.txt and serialize the PHP array into JSON format.
	// FOR PROGRESS BAR END <<<

	// Initialization Pt 2 - xlsx reading initialization
	if(true) {
		error_reporting(E_ALL ^ E_NOTICE);
		require_once 'Classes/PHPExcel.php';
		// enable caching to reduce memory usage for PHPExcel (tips/trick from StackOverflow)
		$cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
		$cacheSettings = array( ' memoryCacheSize ' => '16MB');
		PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
		// lazy-reading from now on! all Excel file reading are done immediately before using its data, not earlier!
	}

	// Initialization Pt 3 - count total number of rows from 3 excel sheets, using 3 "lazy-reading" blocks
	$excelReader_AnnualReq	=	PHPExcel_IOFactory::createReader('Excel2007'); // $excelReader_AnnualReq	->setReadDataOnly(true);
	$excelObj_AnnualReq		=	$excelReader_AnnualReq->load(PATH_XLSX_ANNUALREQ);
	$annualreq				=	$excelObj_AnnualReq->getActiveSheet();
	$total_num_of_rows		+=	$annualreq->getHighestRow() - 1; // minus one row because of header row in xlsx
	unset($excelObj_AnnualReq);
	$excelReader_Summary	=	PHPExcel_IOFactory::createReader('Excel2007'); // $excelReader_Summary		->setReadDataOnly(true);
	$excelObj_Summary		=	$excelReader_Summary->load(PATH_XLSX_SUMMARY);
	$summary				=	$excelObj_Summary->getActiveSheet();
	$total_num_of_rows		+=	$summary->getHighestRow() - 1; // minus one row because of header row in xlsx
	unset($excelObj_Summary);
	$excelReader_Details	=	PHPExcel_IOFactory::createReader('Excel2007'); // $excelReader_Details		->setReadDataOnly(true);
	$excelObj_Details		=	$excelReader_Details->load(PATH_XLSX_DETAILS);
	$details				=	$excelObj_Details->getActiveSheet();
	$total_num_of_rows		+=	$details->getHighestRow() - 1; // minus one row because of header row in xlsx
	unset($excelObj_Summary);
	echo "Total number of rows to be inserted: ".(string)$total_num_of_rows."; starting insertion operation...<br />";


	////////////////////////////////// Step I: Mian's sql server - open connection //////////////////////////////////
	// initialization, then open connection to metadata table which tells me which among the 2 tables shall I connect to next!
	if(true) {
		// step 1 - connect to SQL Server, NOT specifying any database!
		if(true) {
			$connectionInfo = array( "UID"=>SQL_SERVER_USERNAME, "PWD"=>SQL_SERVER_PASSWORD);
			$conn = sqlsrv_connect( SQL_SERVER_NAME, $connectionInfo);
			if( $conn ) echo "SQL Server connection to ".SQL_SERVER_USERNAME." established.<br />";
			else {
				echo "SQL Server connection to ".SQL_SERVER_USERNAME." cannot be established.<br />";
				die( print_r( sqlsrv_errors(), true));
			}
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
			if( $conn ) echo "SQL Server connection to METADATA DATABASE established.<br />";
			else {
				echo "SQL Server connection to METADATA DATABASE could not be established.<br />";
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
			if( $conn ) echo "SQL Server connection to METADATA DATABASE established.<br />";
			else {
				echo "SQL Server connection to METADATA DATABASE could not be established.<br />";
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
			if( $conn ) echo "SQL Server connection to CURRENT DATABASE established.<br />";
			else {
				echo "SQL Server connection to CURRENT DATABASE could not be established.<br />";
				die( print_r( sqlsrv_errors(), true));
			}
		}
	}

	////////////////////////////////// Step II: Mian's sql server - create 5 empty tables //////////////////////////////////
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
	}

	////////////////////////////////// Step III: read data from xlsx or mdb, and then insert into SQL sever //////////////////////////////////


	// 1. Summary & AnnualReq -> Employee: START
	if($do_step_1) {
		echo "===== Start inserting Summary into Employee =====<br />";
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
		}
		// TrickyWork Pt.2: populate Temp
		if(true) {
			$srvr_query = "INSERT INTO Temp (CertNo, TempCertDate, PermCertDate, AdvCertDate, CurrentStatus)";
			$srvr_query .= " VALUES (?,?,?,?,?)";
			//////////////////// lazy-reading INIT ////////////////////
			$excelReader_AnnualReq  = PHPExcel_IOFactory::createReader('Excel2007'); // $excelReader_AnnualReq  ->setReadDataOnly(true);
			$excelObj_AnnualReq     = $excelReader_AnnualReq->load(PATH_XLSX_ANNUALREQ);
			$annualreq              = $excelObj_AnnualReq->getActiveSheet();
			//////////////////// lazy-reading READY ////////////////////
			$row_count = (int)2; // actual data starts at row 2 of Excel spreadsheet
			while ( $row_count <= $annualreq->getHighestRow() ) { // read until the last line
				// select distinct CertID rows from AnnualReq
				$CertNo         = $annualreq->getCell('D'.$row_count)->getValue();
				// 3 tricky dates
				// one weird bug: getCell and getValue naively would result in today's date being inserted into Temp! which would mess
				// up everything from this point onwards in php code execution!
				// TempCertDate
				$TempCell       = $annualreq->getCell('G'.$row_count);
				$TempCertDate   = $TempCell->getValue();
				if($TempCertDate == NULL) { if($print_notes) echo "NOTE: Appraiser ".$CertNo." has NULL in TempCertDate!<br/>"; }
				// note for below: only convert cell to datetime2 variable if cell is not NULL, otherwise insert NULL into database
				else if(PHPExcel_Shared_Date::isDateTime($TempCell)) $TempCertDate = date($format = "m-d-Y", PHPExcel_Shared_Date::ExcelToPHP($TempCertDate));
				// PermCertDate
				$PermCell       = $annualreq->getCell('H'.$row_count);
				$PermCertDate   = $PermCell->getValue();
				if($PermCertDate == NULL) { if($print_notes) echo "NOTE: Appraiser ".$CertNo." has NULL in PermCertDate!<br/>"; }
				else if(PHPExcel_Shared_Date::isDateTime($PermCell)) $PermCertDate = date($format = "m-d-Y", PHPExcel_Shared_Date::ExcelToPHP($PermCertDate));
				// AdvCertDate
				$AdvCell        = $annualreq->getCell('I'.$row_count);
				$AdvCertDate    = $AdvCell->getValue();
				if($AdvCertDate == NULL) { if($print_notes) echo "NOTE: Appraiser ".$CertNo." has NULL in AdvCertDate!<br/>"; }
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
		}
		// TrickyWork Pt.5: insert into Employee table from summary.xlsx (line-by-line) & Temp2 (querying 3 dates + CurrentStatus along with each line)
		if(true) {
			//////////////////// lazy-reading INIT ////////////////////
			$excelReader_Summary    = PHPExcel_IOFactory::createReader('Excel2007'); // $excelReader_Summary->setReadDataOnly(true);
			$excelObj_Summary       = $excelReader_Summary->load(PATH_XLSX_SUMMARY);
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
				if($errorcheck_counter != 1) echo "ERROR: defective data from annualreq.xlsx! conflicting 3-date tuples";
				$Summary_to_Employee = "INSERT INTO Employee (CertNo, LastName, MiddleName, FirstName, Auditor,
																  TempCertDate, PermCertDate, AdvCertDate, CurrentStatus)
																  VALUES (?,?,?,?,?,?,?,?,?)";
				if( $stmt = sqlsrv_query( $conn, $Summary_to_Employee, $params) === false ) die( print_r(sqlsrv_errors(), true) );
				$row_count ++;
				// FOR PRORESS BAR >>>
				$overall_row_counter += 1;
				$percent = intval($overall_row_counter/$total_num_of_rows * 100);
				$arr_content['percent'] = $percent;
				$arr_content['message'] = $overall_row_counter . " row(s) processed.";
				file_put_contents("D:/t/log.txt", json_encode($arr_content)); // Write the progress into D:/t/log.txt and serialize the PHP array into JSON format.
				// FOR PROGRESS BAR END <<<
			}
			unset($excelObj_Summary); //////////////////// lazy-reading END
		}
		echo "===== Summary into Employee finished, ".($row_count-2)." rows inserted. =====<br />";
	}  // Summary & AnnualReq -> Employee: DONE

	// 2. AnnualReq -> CertHistory: START
	if($do_step_2) {
		$srvr_query = "INSERT INTO CertHistory (CertNo, CertYear, CertType, Status, HoursEarned, RequiredHours,
		CurrentYearBalance, PriorYearBalance, CarryToYear1,
		CarryToYear2, CarryToYear3, CarryForwardTotal)";
		$srvr_query .= " VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
		echo "===== Start inserting AnnualReq into CertHistory =====<br />";
		//////////////////// lazy-reading INIT ////////////////////
		$excelReader_AnnualReq  = PHPExcel_IOFactory::createReader('Excel2007'); // $excelReader_AnnualReq->setReadDataOnly(true);
		$excelObj_AnnualReq     = $excelReader_AnnualReq->load(PATH_XLSX_ANNUALREQ);
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
			// FOR PRORESS BAR >>>
			$overall_row_counter += 1;
			$percent = intval($overall_row_counter/$total_num_of_rows * 100);
			$arr_content['percent'] = $percent;
			$arr_content['message'] = $overall_row_counter . " row(s) processed.";
			file_put_contents("D:/t/log.txt", json_encode($arr_content)); // Write the progress into D:/t/log.txt and serialize the PHP array into JSON format.
			// FOR PROGRESS BAR END <<<
		}
		unset($excelObj_AnnualReq); //////////////////// lazy-reading END
		echo "===== AnnualReq into CertHistory finished, ".($row_count-2)." rows inserted. =====<br />";
	}  // AnnualReq pt1 - AnnualReq -> CertHistory: DONE

	// 3. Details -> CourseDetail: START
	if($do_step_3) {
		$srvr_query = "INSERT INTO CourseDetail (CertNo, CourseYear, --ItemNumber,
													CourseName, CourseLocation, CourseGrade, CourseHours, EndDate)";
		$srvr_query .= " VALUES (?,?,?,?,?,?,?)";
		echo "===== Start inserting Details into CourseDetail =====<br />";
		//////////////////// lazy-reading INIT ////////////////////
		$excelReader_Details    = PHPExcel_IOFactory::createReader('Excel2007'); // $excelReader_Details->setReadDataOnly(true);
		$excelObj_Details       = $excelReader_Details->load(PATH_XLSX_DETAILS);
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
			if($EndDate == NULL) { if($print_notes) echo "NOTE: appraiser ".$CertNo." has NULL EndDate in course \"".$CourseName."\" at \"".$CourseLocation."\" !<br/>";}
			// note for below: only convert cell to datetime2 variable if cell is not NULL, otherwise insert NULL into database
			else if(PHPExcel_Shared_Date::isDateTime($EndDateCell)) $EndDate = date($format = "m-d-Y", PHPExcel_Shared_Date::ExcelToPHP($EndDate));

			$params = array($CertNo, $CourseYear, $CourseName, $CourseLocation, $CourseGrade, $CourseHours, $EndDate);
			$stmt = sqlsrv_query( $conn, $srvr_query, $params);
			if( $stmt === false ) die( print_r(sqlsrv_errors(), true) );
			$row_count ++;
			// FOR PRORESS BAR >>>
			$overall_row_counter += 1;
			$percent = intval($overall_row_counter/$total_num_of_rows * 100);
			$arr_content['percent'] = $percent;
			$arr_content['message'] = $overall_row_counter . " row(s) processed.";
			file_put_contents("D:/t/log.txt", json_encode($arr_content)); // Write the progress into D:/t/log.txt and serialize the PHP array into JSON format.
			// FOR PROGRESS BAR END <<<
		}
		unset($excelObj_Details); //////////////////// lazy-reading END
		echo "===== Details into CourseDetail finished, ".($row_count-2)." rows inserted. =====<br />";
	}  // Details -> CourseDetail: DONE


	////////////////////////////////// Step IV: clean up: close "current table" connection and update Metadata DB //////////////////////////////////
	if($do_step_1 && $do_step_2 && $do_step_3) {
		// delete table Temp and Temp2 from current database
		if($do_cleanup) {
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
		if( $conn ) echo "SQL Server connection to METADATA DATABASE established.<br />";
		else {
			echo "SQL Server connection to METADATA DATABASE could not be established.<br />";
			die( print_r( sqlsrv_errors(), true));
		}
		// a clever way to toggle IsCurrent for db1 and db2
		$update_current_db_query = "UPDATE DbTable SET IsCurrent = 1 - IsCurrent"; // previous "current database", gets IsCurrent = 0
		$srvr_stmt = sqlsrv_query( $conn, $update_current_db_query );
		if( $srvr_stmt === false ) { die( print_r( sqlsrv_errors(), true)); }
		echo "Switching the old CURRENT DATABASE to the new CURRENT DATABASE done!<br />";
		// close connection to Metadata Database
		sqlsrv_close($conn);
		unset($conn);
	}


	/* // block comment starter
	// */ // ml: DO NOT DELETE THIS LINE! this is a convenient comment ender for anywhere in the php block.
	// 128M: Fatal error: Allowed memory size of 134217728 bytes exhausted (tried to allocate 4096 bytes)
	// 256M: Fatal error: Allowed memory size of 268435456 bytes exhausted (tried to allocate 1576960 bytes)
	// 512M: works just fine for now!
?>
</body>
</html>
