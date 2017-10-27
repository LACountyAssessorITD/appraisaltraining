<?php
	// for LA County Assessor's Office - Appraisal Training Record Tracking System use only!
	// updated by James Tseng and Mian Lu
	// last edit October 2017

	// TOT remember to add code to close filestream for excel files! and close $conn!
	// mianlu: NOTE: if(true){ } blocks are used to foster code snippit folding with Sublime functionalities.

	// put include_once statements here:
	include_once "../../constants.php";

	////////////////////////////////// Step I: Mian's sql server - open connection //////////////////////////////////
	if(true) {
		$connectionInfo = array( "Database"=>SQL_SERVER_LACDATABASE_ML_DEVELOPMENT, "UID"=>SQL_SERVER_USERNAME, "PWD"=>SQL_SERVER_PASSWORD);
		$conn = sqlsrv_connect( SQL_SERVER_NAME, $connectionInfo);
		if( $conn ) echo "SQL Server connection established.<br />";
		else {
			echo "SQL Server connection could not be established.<br />";
			die( print_r( sqlsrv_errors(), true));
		}
	}


	////////////////////////////////// Step II: Mian's sql server - create 5 empty tables //////////////////////////////////
	if(true) {
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
	}


	////////////////////////////////// Step III: read data from xlsx or mdb, and then insert into SQL sever //////////////////////////////////

	////////////////////////////////// JT: xlsx reading - initialization & open 3 .xlsx files
	if(true) {
		error_reporting(E_ALL ^ E_NOTICE);
		require_once 'Classes/PHPExcel.php';

		$cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
		$cacheSettings = array( ' memoryCacheSize ' => '16MB');
		PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

		/*
		$objReader = PHPExcel_IOFactory::createReader('Excel2007');
		$objReader->setReadDataOnly(true);
		$objPHPExcel = $objReader->load("test.xlsx");
		*/


		$excelReader_Summary	= PHPExcel_IOFactory::createReaderForFile(PATH_XLSX_SUMMARY);
		$excelReader_Summary	= PHPExcel_IOFactory::createReader('Excel2007');
		// $excelReader_Summary	->setReadDataOnly(true);
		$excelObj_Summary		= $excelReader_Summary->load(PATH_XLSX_SUMMARY);
		$summary				= $excelObj_Summary->getActiveSheet();



		$excelReader_AnnualReq	= PHPExcel_IOFactory::createReader('Excel2007');
		// $excelReader_AnnualReq	->setReadDataOnly(true);
		$excelObj_AnnualReq		= $excelReader_AnnualReq->load(PATH_XLSX_ANNUALREQ);
		$annualreq				= $excelObj_AnnualReq->getActiveSheet();

		$excelReader_Details	= PHPExcel_IOFactory::createReader('Excel2007');
		// $excelReader_Details	->setReadDataOnly(true);
		$excelObj_Details		= $excelReader_Details->load(PATH_XLSX_DETAILS);
		$details				= $excelObj_Details->getActiveSheet();
		// */
	}

	// 1. Summary & AnnualReq -> Employee: START
	if(true) {
		echo "===== Start inserting Summary into Employee =====<br />";
		// Tricky work: create table Temp to store all CertNo + 3 Dates; then create Temp2 to store distinct rows from Temp; then
		// insert into Employee row by row from Summary, while querying Temp2 for the 3 dates
		// TrickyWork Pt.1: create table Temp
		if(true) {
			$drop_temp	= "IF OBJECT_ID('dbo.New_Temp', 'U') IS NOT NULL DROP TABLE dbo.New_Temp";
			$srvr_stmt = sqlsrv_query( $conn, $drop_temp );
			if( $srvr_stmt === false ) { die( print_r( sqlsrv_errors(), true)); }
			$create_temp = "CREATE TABLE dbo.New_Temp (
				CertNo float,						--dbo.Summary (dbo.AnnualReq/Details also contain, but should be all duplicates, doublecheck!)
				TempCertDate date,			--dbo.AnnualReq
				PermCertDate date,			--dbo.AnnualReq
				AdvCertDate date,			--dbo.AnnualReq
			)";
			$srvr_stmt = sqlsrv_query( $conn, $create_temp );
			if( $srvr_stmt === false ) { die( print_r( sqlsrv_errors(), true)); }
		}




		// TrickyWork Pt.2: populate Temp
		if(true) {
			$srvr_query = "INSERT INTO New_Temp (CertNo, TempCertDate, PermCertDate, AdvCertDate)";
			$srvr_query .= " VALUES (?,?,?,?)";
			// lazy-loading
			/*
			$excelReader_AnnualReq	= PHPExcel_IOFactory::createReader('Excel2007');
			$excelReader_AnnualReq	->setReadDataOnly(true);
			$excelObj_AnnualReq		= $excelReader_AnnualReq->load(PATH_XLSX_ANNUALREQ);
			$annualreq				= $excelObj_AnnualReq->getActiveSheet();
			*/
			// lazy-loading end
			$row_count = (int)2; // actual data starts at row 2 of Excel spreadsheet
			while ( $row_count <= $annualreq->getHighestRow() ) { // read until the last line
				// select distinct CertID rows from AnnualReq
				$CertNo			= $annualreq->getCell('D'.$row_count)->getValue();
				// 3 tricky dates
				// one weird bug: getCell and getValue naively would result in today's date being inserted into New_Temp! which would mess
				// up everything from this point onwards in php code execution!
				// TempCertDate
				$TempCell		= $annualreq->getCell('G'.$row_count);
				$TempCertDate	= $TempCell->getValue();
				if($TempCertDate == NULL) echo "NOTE: Appraiser ".$CertNo." has NULL in TempCertDate!<br/>";
				// note for below: only convert cell to datetime2 variable if cell is not NULL, otherwise insert NULL into database
				else if(PHPExcel_Shared_Date::isDateTime($TempCell)) $TempCertDate = date($format = "m-d-Y", PHPExcel_Shared_Date::ExcelToPHP($TempCertDate));
				// PermCertDate
				$PermCell		= $annualreq->getCell('H'.$row_count);
				$PermCertDate	= $PermCell->getValue();
				if($PermCertDate == NULL) echo "NOTE: Appraiser ".$CertNo." has NULL in PermCertDate!<br/>";
				else if(PHPExcel_Shared_Date::isDateTime($PermCell)) $PermCertDate = date($format = "m-d-Y", PHPExcel_Shared_Date::ExcelToPHP($PermCertDate));
				// AdvCertDate
				$AdvCell		= $annualreq->getCell('I'.$row_count);
				$AdvCertDate	= $AdvCell->getValue();
				if($AdvCertDate == NULL) echo "NOTE: Appraiser ".$CertNo." has NULL in AdvCertDate!<br/>";
				else if(PHPExcel_Shared_Date::isDateTime($AdvCell)) $AdvCertDate = date($format = "m-d-Y", PHPExcel_Shared_Date::ExcelToPHP($AdvCertDate));
				// inserting operation
				$params 		= array($CertNo, $TempCertDate, $PermCertDate, $AdvCertDate); // TOTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTTT
				$stmt 			= sqlsrv_query( $conn, $srvr_query, $params);
				if( $stmt === false ) { die( print_r(sqlsrv_errors(), true) ); }
				$row_count ++;
			}
		}

		// TrickyWork Pt.3: create table Temp2
		if(true) {
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
		}
		// TrickyWork Pt.4: populate Temp2
		if(true) {
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
		}

		// TrickyWork Pt.5: insert into Employee table from summary.xlsx(line-by-line) & Temp2(querying 3 dates along with each line)
		if(true) {
			$row_count = (int)2;
			while ( $row_count <= $summary->getHighestRow() ) { // read until the last line
				$params		= NULL;
				$CertNo		= $summary->getCell('G'.$row_count)->getValue();
				$LastName	= $summary->getCell('D'.$row_count)->getValue();
				$MiddleName	= $summary->getCell('F'.$row_count)->getValue();
				$FirstName	= $summary->getCell('E'.$row_count)->getValue();
				$Auditor	= $summary->getCell('H'.$row_count)->getValue();
				// echo $row_count."\t".$LastName."\t".$FirstName."\t".(int)$CertNo."\t".$Auditor."<br />"; // debug
				$Select_Temp2_Dates = "SELECT TempCertDate, PermCertDate, AdvCertDate FROM New_Temp2";
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
					// echo $temp_row->CertNo.'<br />';
					$params = array($CertNo, $LastName, $MiddleName, $FirstName, $Auditor, $temp_row->TempCertDate, $temp_row->PermCertDate, $temp_row->AdvCertDate);
				}
				if($errorcheck_counter != 1) echo "ERROR: defective data from annualreq.xlsx! conflicting 3-date tuples";
				$Summary_to_Employee = "INSERT INTO New_Employee (CertNo, LastName, MiddleName, FirstName, Auditor,
																  TempCertDate, PermCertDate, AdvCertDate)
																  VALUES (?,?,?,?,?,?,?,?)";
				if( $stmt = sqlsrv_query( $conn, $Summary_to_Employee, $params) === false ) die( print_r(sqlsrv_errors(), true) );
				$row_count ++;
			}
			echo "===== Summary into Employee finished, ";
			echo $row_count-2;
			echo " rows inserted. =====<br />";
			$row_count = 2; // reset counter
		}

		// */ // ml: DO NOT DELETE THIS LINE! this is a convenient comment ender for anywhere in the php block.

	}  // Summary & AnnualReq -> Employee: DONE


	// 2. AnnualReq -> CertHistory: START
	if(true) {
		// // JT:
		// $excelReader = PHPExcel_IOFactory::createReaderForFile($annualreq_filename);
		// $excelObj = $excelReader->load($annualreq_filename);
		// $annualreq = $excelObj->getActiveSheet();
		// ml:
		$srvr_query = "INSERT INTO New_CertHistory (CertNo, CertYear, CertType, Status, HoursEarned, RequiredHours,
		CurrentYearBalance, PriorYearBalance, CarryToYear1,
		CarryToYear2, CarryToYear3, CarryForwardTotal)";
		$srvr_query .= " VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
		echo "===== Start inserting AnnualReq into CertHistory =====<br />";
		$row_count = (int)2;
		while ( $row_count <= $annualreq->getHighestRow() ) { // read until the last line
			// int counter logic end
			$CertNo				= $annualreq->getCell('D'.$row_count)->getValue();
			$CertYear			= $annualreq->getCell('M'.$row_count)->getValue();
			$CertType			= $annualreq->getCell('L'.$row_count)->getValue();
			$Status				= $annualreq->getCell('K'.$row_count)->getValue();
			$HoursEarned		= $annualreq->getCell('N'.$row_count)->getValue();
			$RequiredHours		= $annualreq->getCell('O'.$row_count)->getValue();
			$CurrentYearBalance	= $annualreq->getCell('P'.$row_count)->getValue();
			$PriorYearBalance	= $annualreq->getCell('Q'.$row_count)->getValue();
			$CarryToYear1		= $annualreq->getCell('R'.$row_count)->getValue();
			$CarryToYear2		= $annualreq->getCell('S'.$row_count)->getValue();
			$CarryToYear3		= $annualreq->getCell('T'.$row_count)->getValue();
			$CarryForwardTotal	= $annualreq->getCell('U'.$row_count)->getValue();
			$params = array($CertNo, $CertYear, $CertType, $Status, $HoursEarned, $RequiredHours,
			$CurrentYearBalance, $PriorYearBalance, $CarryToYear1,$CarryToYear2, $CarryToYear3,
			$CarryForwardTotal);
			$stmt = sqlsrv_query( $conn, $srvr_query, $params);
			if( $stmt === false ) die( print_r(sqlsrv_errors(), true) );
			$row_count ++;
		}
		echo "===== AnnualReq into CertHistory finished, ";
		echo $row_count-2;
		echo " rows inserted. =====<br />";
	}  // AnnualReq pt1 - AnnualReq -> CertHistory: DONE

	// 3. Details -> CourseDetail: START
	if(true) {
		// $create_CD = "CREATE TABLE dbo.New_CourseDetail (
		// 	CertNo float references dbo.New_Employee(CertNo), -- foreign key from Employee
		// 	CourseYear nvarchar(10),			-- from dbo.Details(FiscalYear)
		// 	ItemNumber float,					-- undefined, maybe an index for all possible courses, if so then new table all_courses needed
		// 	CourseName nvarchar(100),			-- from dbo.tbl: Course Title + dbo.Details(Course)
		// 	CourseLocation nvarchar(50),		-- from dbo.Details(Location)
		// 	CourseGrade nvarchar(50),			-- from dbo.Details(Grade)
		// 	CourseHours float,					-- from dbo.Details(HoursEarned)
		// 	EndDate datetime2(0),				-- from dbo.Details(EndDate)
		// 	-- primary key (CertNo, CourseYear, CourseName), -- if ItemNumber correctly implemented, switch to it! CertNo should be foreign key
		// )";
		$srvr_query = "INSERT INTO New_CourseDetail (CertNo, CourseYear, --ItemNumber,
													CourseName, CourseLocation, CourseGrade, CourseHours, EndDate)";
		$srvr_query .= " VALUES (?,?,?,?,?,?,?)";
		echo "===== Start inserting Details into CourseDetail =====<br />";
		$row_count = (int)2;
		while ( $row_count <= $details->getHighestRow() ) { // read until the last line
			$CertNo				= $details->getCell('C'.$row_count)->getValue();
			$CourseYear			= $details->getCell('G'.$row_count)->getValue();
			// $ItemNumber
			$CourseName			= $details->getCell('I'.$row_count)->getValue();
			$CourseLocation		= $details->getCell('J'.$row_count)->getValue();
			$CourseGrade		= $details->getCell('K'.$row_count)->getValue();
			$CourseHours		= $details->getCell('L'.$row_count)->getValue();
			$EndDate			= $details->getCell('H'.$row_count)->getValue();
			$params = array($CertNo, $CourseYear, $CourseName, $CourseLocation, $CourseGrade, $CourseHours, $EndDate);
			$stmt = sqlsrv_query( $conn, $srvr_query, $params);
			if( $stmt === false ) die( print_r(sqlsrv_errors(), true) );
			echo "success for row ".
			$row_count ++;
		}
		echo "===== Details into CourseDetail finished, ";
		echo $row_count-2;
		echo " rows inserted. =====<br />";
	}

	/* // block comment starter
	// */ // ml: DO NOT DELETE THIS LINE! this is a convenient comment ender for anywhere in the php block.
?>
