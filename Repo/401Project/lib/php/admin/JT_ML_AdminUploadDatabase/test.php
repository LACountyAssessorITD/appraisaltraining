<?php
	// for LA County Assessor's Office - Appraisal Training Record Tracking System use only!
	// updated by James Tseng and Mian Lu
	// last edit October 2017

	ini_set('memory_limit', '512M'); // TOT optimize more?

	// TOT remember to close $conn!
	// mianlu: NOTE: if(true){ } blocks are used to foster code snippit folding with Sublime functionalities.

	// put include_once statements here:
	include_once "../../constants.php";

	$connectionInfo = array( "UID"=>SQL_SERVER_USERNAME, "PWD"=>SQL_SERVER_PASSWORD);
	$conn = sqlsrv_connect( SQL_SERVER_NAME, $connectionInfo);
	if( $conn ) echo "SQL Server connection to SQL Server established.<br />";
	else {
		echo "SQL Server connection to SQL Server could not be established.<br />";
		die( print_r( sqlsrv_errors(), true));
	}

	$create_metadata = "IF (db_id(N'".SQL_SERVER_LACDATABASE_ML_DEVELOPMENT_no_drop_00."') IS NULL) CREATE DATABASE ".SQL_SERVER_LACDATABASE_ML_DEVELOPMENT_no_drop_00;
	echo ($create_metadata);
	$srvr_stmt = sqlsrv_query( $conn, $create_metadata );
	if( $srvr_stmt == false ) die( print_r( sqlsrv_errors(), true));
	else echo ("<br />Query completed successfully.<br/>")


	/* // block comment starter

	////////////////////////////////// Step I: Mian's sql server - open connection //////////////////////////////////
	// open connectio to metadata table which tells me which among the 2 tables shall I connect to next!
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

		// open connection to the non-current database so that if sth bad occurs during import, roll-back to previous database!
		$connectionInfo = array( "Database"=>$CurrentDatabase, "UID"=>SQL_SERVER_USERNAME, "PWD"=>SQL_SERVER_PASSWORD);
		$conn = sqlsrv_connect( SQL_SERVER_NAME, $connectionInfo);
		if( $conn ) echo "SQL Server connection to CURRENT DATABASE established.<br />";
		else {
			echo "SQL Server connection to CURRENT DATABASE could not be established.<br />";
			die( print_r( sqlsrv_errors(), true));
		}
	}

	////////////////////////////////// Step II: Mian's sql server - create 5 empty tables //////////////////////////////////
	if(true) {
		// 1. create queries to drop existing tables with same names that we're gonna create
		$drop_CH	= "IF OBJECT_ID('dbo.CertHistory', 'U') IS NOT NULL DROP TABLE dbo.CertHistory";
		$drop_CD	= "IF OBJECT_ID('dbo.CourseDetail', 'U') IS NOT NULL DROP TABLE dbo.CourseDetail";
		$drop_COL	= "IF OBJECT_ID('dbo.CarryoverLimits', 'U') IS NOT NULL DROP TABLE dbo.CarryoverLimits";
		$drop_EX	= "IF OBJECT_ID('dbo.EmployeeID_Xref', 'U') IS NOT NULL DROP TABLE dbo.EmployeeID_Xref";
		$drop_E		= "IF OBJECT_ID('dbo.Employee', 'U') IS NOT NULL DROP TABLE dbo.Employee";
		// $rename_CH	= "sp_rename 'dbo.CertHistory', 'Prev_CertHistory'";
		// $rename_CD	= "sp_rename 'dbo.CourseDetail', 'Prev_CourseDetail'";
		// $rename_COL	= "sp_rename 'dbo.CarryoverLimits', 'Prev_CarryoverLimits'";
		// $rename_EX	= "sp_rename 'dbo.EmployeeID_Xref', 'Prev_EmployeeID_Xref'";
		// $rename_E	= "sp_rename 'dbo.Employee', 'Prev_Employee'";
		// 2. run the above queries
		$srvr_stmt = sqlsrv_query( $conn, $drop_CH );
		// if( $srvr_stmt === false ) { die( print_r( sqlsrv_errors(), true)); }
		$srvr_stmt = sqlsrv_query( $conn, $drop_CD );
		// if( $srvr_stmt === false ) { die( print_r( sqlsrv_errors(), true)); }
		$srvr_stmt = sqlsrv_query( $conn, $drop_COL );
		// if( $srvr_stmt === false ) { die( print_r( sqlsrv_errors(), true)); }
		$srvr_stmt = sqlsrv_query( $conn, $drop_EX );
		// if( $srvr_stmt === false ) { die( print_r( sqlsrv_errors(), true)); }
		$srvr_stmt = sqlsrv_query( $conn, $drop_E );
		// if( $srvr_stmt === false ) { die( print_r( sqlsrv_errors(), true)); }
		// 3. create table 3 Employee (DO THIS FIRST! CertNo need to be a foreign key for all other tables!)
		$create_E = "CREATE TABLE dbo.Employee (
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
		$create_CD = "CREATE TABLE dbo.CourseDetail (
			CertNo float references dbo.Employee(CertNo), -- foreign key from Employee
			CourseYear nvarchar(10),			-- from dbo.Details(FiscalYear)
			ItemNumber float,					-- undefined, maybe an index for all possible courses, if so then new table all_courses needed
			CourseName nvarchar(100),			-- from dbo.tbl: Course Title + dbo.Details(Course)
			CourseLocation nvarchar(50),		-- from dbo.Details(Location)
			CourseGrade nvarchar(50),			-- from dbo.Details(Grade)
			CourseHours float,					-- from dbo.Details(HoursEarned)
			EndDate datetime2(0),				-- from dbo.Details(EndDate) ( TOT: datetime2(0) )
			-- primary key (CertNo, CourseYear, CourseName), -- if ItemNumber correctly implemented, switch to it! CertNo should be foreign key
		)";
		// 5. create table 4 CarryoverLimits
		$create_COL = "CREATE TABLE dbo.CarryoverLimits (
			[Status] nvarchar(50),				-- primary key from dbo.CertHistory, referenced in CertHistory(Status)
			RequiredHours float,				-- undefined
			Year1Limit float,					-- undefined
			Year2Limit float,					-- undefined
			Year3Limit float,					-- undefined
			-- primary key ([Status]),
		)";
		// 6. create table 2 CertHistory
		$create_CH = "CREATE TABLE dbo.CertHistory (
			CertNo float references dbo.Employee(CertNo), -- foreign key from Employee
			CertYear nvarchar(10),				-- from dbo.AnnualReq
			CertType nvarchar(50),				-- from dbo.AnnualReq
			-- [Status] nvarchar(50) references dbo.CarryoverLimits([Status]),				-- from dbo.AnnualReq
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


	sqlsrv_close($conn); // close connection to the new Current Database
	unset($conn);

	// */ // ml: DO NOT DELETE THIS LINE! this is a convenient comment ender for anywhere in the php block.
?>
