<?php
	// for LA County Assessor's Office - Appraisal Training Record Tracking System use only!
	// updated by James Tseng and Mian Lu
	// last edit October 2017

	// TOT remember to add code to close filestream for excel files! and close $conn!

	// put include_once statements here:
	include_once "../../constants.php";

	////////////////////////////////// Mian's sql server - open connection //////////////////////////////////
	$serverName = SQL_SERVER_NAME;
	$connectionInfo = array( "Database"=>SQL_SERVER_LACDATABASE, "UID"=>SQL_SERVER_USERNAME, "PWD"=>SQL_SERVER_PASSWORD);
	$conn = sqlsrv_connect( $serverName, $connectionInfo);

	if( $conn ) echo "SQL Server connection established.<br />";
	else {
		echo "SQL Server connection could not be established.<br />";
		die( print_r( sqlsrv_errors(), true));
	}
	////////////////////////////////// Mian's sql server - create 5 empty tables //////////////////////////////////
	// 1. create queries to drop existing tables with same names that we're gonna create
	$drop_CH	= "IF OBJECT_ID('dbo.New_CertHistory', 'U') IS NOT NULL DROP TABLE dbo.New_CertHistory";
	$drop_CD	= "IF OBJECT_ID('dbo.New_CourseDetail', 'U') IS NOT NULL DROP TABLE dbo.New_CourseDetail";
	$drop_COL	= "IF OBJECT_ID('dbo.New_CarryoverLimits', 'U') IS NOT NULL DROP TABLE dbo.New_CarryoverLimits";
	$drop_EX	= "IF OBJECT_ID('dbo.New_EmployeeID_Xref', 'U') IS NOT NULL DROP TABLE dbo.New_EmployeeID_Xref";
	$drop_E		= "IF OBJECT_ID('dbo.New_Employee', 'U') IS NOT NULL DROP TABLE dbo.New_Employee";
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
	// 3. create table 3 Employee (DO THIS FIRST! CertNo need to be a foreign key for all other tables!)
	$create_E = "CREATE TABLE dbo.New_Employee (
		CertNo float,						--dbo.Summary (dbo.AnnualReq/Details also contain, but should be all duplicates, doublecheck!)
		FirstName nvarchar(50),				--dbo.Summary
		MiddleName nvarchar(50),			--dbo.Summary
		LastName nvarchar(50),				--dbo.Summary
		TempCertDate datetime2(0),			--dbo.AnnualReq
		PermCertDate datetime2(0),			--dbo.AnnualReq
		AdvCertDate datetime2(0),			--dbo.AnnualReq
		CurrentStatus nvarchar(50),			--dbo.AnnualReq
		Auditor nvarchar(50),				--dbo.Summary
		-- ml: below are suggested extra columns
		-- CountyCode nvarchar(2),			--dbo.Summary
		-- CurrFiscalYear nvarchar(10),		--dbo.Summary
		-- CurrCertType 					-- contained in dbo.AnnualReq, not unique! i.e. one employee can have multiple types, all active!
		primary key (CertNo), -- CertNo should be foreign key
	)";
	// 4. create table 1 CourseDetail
	$create_CD = "CREATE TABLE dbo.New_CourseDetail (
		CertNo float references dbo.New_Employee(CertNo), -- foreign key from Employee
		CourseYear nvarchar(10),			-- from dbo.Details(FiscalYear)
		ItemNumber float,					-- undefined, maybe an index for all possible courses, if so then new table all_courses needed
		CourseName nvarchar(100),			-- from dbo.tbl: Course Title + dbo.Details(Course)
		CourseLocation nvarchar(50),		-- from dbo.Details(Location)
		CourseGrade nvarchar(50),			-- from dbo.Details(Grade)
		CourseHours float,					-- from dbo.Details(HoursEarned)
		EndDate datetime2(0),				-- from dbo.Details(EndDate)
		-- primary key (CertNo, CourseYear, CourseName), -- if ItemNumber correctly implemented, switch to it! CertNo should be foreign key
	)";
	// 5. create table 4 CarryoverLimits
	$create_COL = "CREATE TABLE dbo.New_CarryoverLimits (
		[Status] nvarchar(50),				-- primary key from dbo.New_CertHistory, referenced in CertHistory(Status)
		RequiredHours float,				-- undefined
		Year1Limit float,					-- undefined
		Year2Limit float,					-- undefined
		Year3Limit float,					-- undefined
		-- primary key ([Status]),
	)";
	// 6. create table 2 CertHistory
	$create_CH = "CREATE TABLE dbo.New_CertHistory (
		CertNo float references dbo.New_Employee(CertNo), -- foreign key from Employee
		CertYear nvarchar(10),				-- from dbo.AnnualReq
		CertType nvarchar(50),				-- from dbo.AnnualReq
		-- [Status] nvarchar(50) references dbo.New_CarryoverLimits([Status]),				-- from dbo.AnnualReq
		[Status] nvarchar(50),				-- from dbo.AnnualReq
		HoursEarned float,					-- from dbo.AnnualReq
		RequiredHours float,
		CurrentYearBalance float,			-- from dbo.AnnualReq
		PriorYearBalance float,				-- from dbo.AnnualReq
		CarryToYear1 float,					-- from dbo.AnnualReq
		CarryToYear2 float,					-- from dbo.AnnualReq
		CarryToYear3 float,					-- from dbo.AnnualReq
		CarryForwardTotal float,			-- from dbo.AnnualReq
		constraint PK_CERTNO_CERTYEAR primary key (CertNo, CertYear), -- CertNo should be foreign key
	)";
	// 7. create table 5 EmployeeID_Xref
	$create_EX = "CREATE TABLE dbo.New_EmployeeID_Xref (
		CertNo float references dbo.New_Employee(CertNo),
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
	////////////////////////////////// read data from xlsx or mdb, and then insert into SQL sever //////////////////////////////////
	// JamesT's xlsx reading - initialization
	error_reporting(E_ALL ^ E_NOTICE);
	require_once 'Classes/PHPExcel.php';
	$summary_filename 		= "summary.xlsx";
	$details_filename 		= "details.xlsx";
	$annualreq_filename 	= "annualreq.xlsx";

	// 1. Summary
	// JT: open xlsx summary
	$excelReader = PHPExcel_IOFactory::createReaderForFile($summary_filename);
	$excelObj = $excelReader->load($summary_filename);
	$summary = $excelObj->getActiveSheet();
	// open xlsx annualreq for additional data!
	$excelReader_ar = PHPExcel_IOFactory::createReaderForFile($annualreq_filename);
	$excelObj_ar = $excelReader_ar->load($annualreq_filename);
	$annualreq = $excelObj_ar->getActiveSheet();

	$row_count = 2; // JT: actual data starts at row 2 of Excel spreadsheet
	echo "===== Start inserting Summary into Employee =====<br />";
	// now do the tricky work: create a whole new table called dbo.temp,
	// insert all distinct by CertID rows from AnnualReq
	// then within the while loop, select from it and insert along with each row
	$drop_temp	= "IF OBJECT_ID('dbo.New_Temp', 'U') IS NOT NULL DROP TABLE dbo.New_Temp";
	$srvr_stmt = sqlsrv_query( $conn, $drop_temp );
	if( $srvr_stmt === false ) { die( print_r( sqlsrv_errors(), true)); }
	$create_temp = "CREATE TABLE dbo.New_Temp (
		CertNo float,						--dbo.Summary (dbo.AnnualReq/Details also contain, but should be all duplicates, doublecheck!)
		TempCertDate datetime2(0),			--dbo.AnnualReq
		PermCertDate datetime2(0),			--dbo.AnnualReq
		AdvCertDate datetime2(0),			--dbo.AnnualReq
	)";
	$srvr_stmt = sqlsrv_query( $conn, $create_temp );
	if( $srvr_stmt === false ) { die( print_r( sqlsrv_errors(), true)); }
	// open annualreq.xlsx
	$excelReader = PHPExcel_IOFactory::createReaderForFile("annualreq_date_formatted.xlsx");
	$excelObj = $excelReader->load("annualreq_date_formatted.xlsx");
	$annualreq = $excelObj->getActiveSheet();
	$srvr_query = "INSERT INTO New_Temp (CertNo, TempCertDate, PermCertDate, AdvCertDate)";
	$srvr_query .= " VALUES (?,?,?,?)";
	$row_count = (int)2;
	while ( $row_count <= $annualreq->getHighestRow() ) { // read until the last line
		// select distinct CertID rows from AnnualReq
		$CertNo			= $annualreq->getCell('D'.$row_count)->getValue();
		// 3 tricky dates

		// one weird bug: getCell and getValue naively would result in today's date being inserted into New_Temp! which would mess
		// up everything from this point onwards in php code execution!
		$TempCell		= $annualreq->getCell('G'.$row_count);
		$TempCertDate	= $TempCell->getValue();
		if($TempCertDate == NULL) echo "ERROR: Appraiser ".$CertNo." has NULL in TempCertDate!<br/>";
		if(PHPExcel_Shared_Date::isDateTime($TempCell)) $TempCertDate = date($format = "m-d-Y", PHPExcel_Shared_Date::ExcelToPHP($TempCertDate));

		$PermCell		= $annualreq->getCell('H'.$row_count);
		$PermCertDate	= $PermCell->getValue();
		if($PermCertDate == NULL) echo "ERROR: Appraiser ".$CertNo." has NULL in PermCertDate!<br/>";
		if(PHPExcel_Shared_Date::isDateTime($PermCell)) $PermCertDate = date($format = "m-d-Y", PHPExcel_Shared_Date::ExcelToPHP($PermCertDate));

		$AdvCell		= $annualreq->getCell('I'.$row_count);
		$AdvCertDate	= $AdvCell->getValue();
		if($AdvCertDate == NULL) echo "ERROR: Appraiser ".$CertNo." has NULL in AdvCertDate!<br/>";
		if(PHPExcel_Shared_Date::isDateTime($AdvCell)) $AdvCertDate = date($format = "m-d-Y", PHPExcel_Shared_Date::ExcelToPHP($AdvCertDate));

		$params 		= array($CertNo, $TempCertDate, $PermCertDate, $AdvCertDate); // TOTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTT
		$stmt 			= sqlsrv_query( $conn, $srvr_query, $params);
		if( $stmt === false ) { die( print_r(sqlsrv_errors(), true) ); }
		$row_count ++;
	}
	// tricky work pt.2: eliminate duplicates across all columns in dbo.Temp, and then make sure all CertID's are distinct!
	// if BOE xlsx data contains erroneous duplicates, this part of code would detect it!
	// first drop & create dbo.Temp2
	$drop_temp2 = "IF OBJECT_ID('dbo.New_Temp2', 'U') IS NOT NULL DROP TABLE dbo.New_Temp2";
	$srvr_stmt = sqlsrv_query( $conn, $drop_temp2 );
	if( $srvr_stmt === false ) { die( print_r( sqlsrv_errors(), true)); }
	$create_temp2 = "CREATE TABLE dbo.New_Temp2 (
		CertNo float,
		TempCertDate datetime2(0),
		PermCertDate datetime2(0),
		AdvCertDate datetime2(0),
	)";
	$srvr_stmt = sqlsrv_query( $conn, $create_temp2 );
	if( $srvr_stmt === false ) { die( print_r( sqlsrv_errors(), true)); }
	// drop & create dbo.Temp2 finished
	$select_query = "SELECT DISTINCT * FROM New_Temp ORDER BY CertNo";
	$insert_query = "INSERT INTO New_Temp2 (CertNo, TempCertDate, PermCertDate, AdvCertDate) VALUES (?,?,?,?)";
	// BLOCK looping thru query selected lines and manipulate data fetched!
	if(($result = sqlsrv_query($conn, $select_query)) !== false){
		while( $temp_row = sqlsrv_fetch_object( $result )) {
			// echo $temp_row->CertNo.'<br />';
			$params = array($temp_row->CertNo, $temp_row->TempCertDate, $temp_row->PermCertDate, $temp_row->AdvCertDate);
			$stmt = sqlsrv_query($conn, $insert_query, $params);
			if( $stmt === false ) { die( print_r(sqlsrv_errors(), true) ); }
		}
	}
	// BLOCK End
	// tricky work pt.3: now New_Temp2 contains duplicate-eliminated CertNo + 3 dates; start inserting Summary->Employee
	// and within each row insertion, query the 3 dates from New_Temp2 and insert along with other information
	// reset row count and loop again to insert everything in Summary
	$row_count = (int)2;
	while ( $row_count <= $summary->getHighestRow() ) { // read until the last line
		$params = NULL;
		$CertNo		= $summary->getCell('G'.$row_count)->getValue();
		$LastName	= $summary->getCell('D'.$row_count)->getValue();
		$FirstName	= $summary->getCell('E'.$row_count)->getValue();
		$Auditor	= $summary->getCell('H'.$row_count)->getValue();
		// echo $row_count."\t".$LastName."\t".$FirstName."\t".(int)$CertNo."\t".$Auditor."<br />"; // debug
		// tricky work pt.3
		$Select_Temp2_Dates = "SELECT TempCertDate, PermCertDate, AdvCertDate FROM New_Temp2";
		$Select_Temp2_Dates .= " WHERE CertNo = ";
		$Select_Temp2_Dates .= $CertNo;
		// $Dates = $conn->query($Select_Temp2_Dates);

		(int)$errorcheck_counter = 0;
		if(($result = sqlsrv_query($conn, $Select_Temp2_Dates)) !== false){
			while( $temp_row = sqlsrv_fetch_object( $result )) { // $temp_row should have 3 columns
				$errorcheck_counter ++;
				// echo $temp_row->CertNo.'<br />';
				$params = array($CertNo, $LastName, $FirstName, $Auditor, $temp_row->TempCertDate, $temp_row->PermCertDate, $temp_row->AdvCertDate);
			}
		}
		if($errorcheck_counter != 1) echo "ERRORNEOUS DATA FROM ANNUALREQ.XLSX!!!!! CONFLICTING 3-DATE TUPLES";
		$Summary_to_Employee = "INSERT INTO New_Employee (CertNo, LastName, FirstName, Auditor,
														  TempCertDate, PermCertDate, AdvCertDate)
														  VALUES (?,?,?,?,?,?,?)";
		$stmt 		= sqlsrv_query( $conn, $Summary_to_Employee, $params);
		if( $stmt === false ) { die( print_r(sqlsrv_errors(), true) ); }
		$row_count ++;
	}
	echo "===== Summary into Employee finished, ";
	echo $row_count-2;
	echo " rows inserted. =====<br />";

	/* // block comment starter

	// 2. AnnualReq pt1 - AnnualReq -> CertHistory
	// JT:
	$excelReader = PHPExcel_IOFactory::createReaderForFile($annualreq_filename);
	$excelObj = $excelReader->load($annualreq_filename);
	$annualreq = $excelObj->getActiveSheet();
	// ml:
	$srvr_query = "INSERT INTO New_CertHistory (CertNo, CertYear, CertType, Status, HoursEarned, RequiredHours,
	CurrentYearBalance, PriorYearBalance, CarryToYear1,
	CarryToYear2, CarryToYear3, CarryForwardTotal)";
	$srvr_query .= " VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
	$row_count = (int)2;
	echo "===== Start inserting AnnualReq into CertHistory =====<br />";
	while ( $row_count <= $annualreq->getHighestRow() ) { // read until the last line
		// int counter logic end
		$CertNo = $annualreq->getCell('D'.$row_count)->getValue();
		$CertYear = $annualreq->getCell('M'.$row_count)->getValue();
		$CertType = $annualreq->getCell('L'.$row_count)->getValue();
		$Status = $annualreq->getCell('K'.$row_count)->getValue();
		$HoursEarned = $annualreq->getCell('N'.$row_count)->getValue();
		$RequiredHours = $annualreq->getCell('O'.$row_count)->getValue();
		$CurrentYearBalance = $annualreq->getCell('P'.$row_count)->getValue();
		$PriorYearBalance = $annualreq->getCell('Q'.$row_count)->getValue();
		$CarryToYear1 = $annualreq->getCell('R'.$row_count)->getValue();
		$CarryToYear2 = $annualreq->getCell('S'.$row_count)->getValue();
		$CarryToYear3 = $annualreq->getCell('T'.$row_count)->getValue();
		$CarryForwardTotal = $annualreq->getCell('U'.$row_count)->getValue();
		$params = array($CertNo, $CertYear, $CertType, $Status, $HoursEarned, $RequiredHours,
		$CurrentYearBalance, $PriorYearBalance, $CarryToYear1,$CarryToYear2, $CarryToYear3,
		$CarryForwardTotal);
		$srvr_exec = sqlsrv_query( $conn, $srvr_query, $params);
		if( $srvr_exec == false ) { die( print_r(sqlsrv_errors(), true) ); }
		$row_count ++;
	}
	echo "===== AnnualReq into CertHistory finished, ";
	echo $row_count-2;
	echo " rows inserted. =====<br />";

	// 2) AnnualReq -> Employee
	// very tricky, do it later; how to match CertNo in AnnualReq to CertNo in Employee(PK)? using SELECT WHERE (match) would be too slow...?

	// */ // ml: DO NOT DELETE THIS LINE! this is a convenient comment ender for anywhere in the php block.
?>
