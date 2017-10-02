<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=" />
	<title>test</title>

</head>
<body>
	<table>
		<?php

			/////////////////////////////////// James's mdb - open mdb file ///////////////////////////////////
			//For reference:
			//https://www.sitepoint.com/using-an-access-database-with-php/

			$mdbName = "LosAngeles.mdb";
			if (!file_exists($mdbName)) {
				die("Could not find database file.");
			}
			$mdb = new PDO("odbc:DRIVER={Microsoft Access Driver (*.mdb)}; DBQ=$mdbName; Uid=; Pwd=;");
			/////////////////////////////////// James's mdb - open mdb file finished ///////////////////////////////////


			/////////////////////////////////// Mian's sql server - open connection ///////////////////////////////////
			$serverName = "Assessor"; //serverName\instanceName
			$connectionInfo = array( "Database"=>"ml_LAC_mdb_data", "UID"=>"superadmin", "PWD"=>"admin");
			$conn = sqlsrv_connect( $serverName, $connectionInfo);

			if( $conn ) {
				echo "SQL Server connection established.<br />";
			}
			else {
				echo "SQL Server connection could not be established.<br />";
				die( print_r( sqlsrv_errors(), true));
			}
			/////////////////////////////////// Mian's sql server - open connection finished ///////////////////////////////////
			/* This is the SQL script for creating the 5-table structure from scratch - Mian Lu*/

			// first drop all tables
			// CertHistory
			$drop_CH = "IF OBJECT_ID('dbo.New_CertHistory', 'U') IS NOT NULL DROP TABLE dbo.New_CertHistory";
			// CourseDetail
			$drop_CD = "IF OBJECT_ID('dbo.New_CourseDetail', 'U') IS NOT NULL DROP TABLE dbo.New_CourseDetail";
			// CarryOverLimits
			$drop_COL = "IF OBJECT_ID('dbo.New_CarryoverLimits', 'U') IS NOT NULL DROP TABLE dbo.New_CarryoverLimits";
			// EmployeeID_Xref
			$drop_EX = "IF OBJECT_ID('dbo.New_EmployeeID_Xref', 'U') IS NOT NULL DROP TABLE dbo.New_EmployeeID_Xref";
			// Employee
			$drop_E = "IF OBJECT_ID('dbo.New_Employee', 'U') IS NOT NULL DROP TABLE dbo.New_Employee";

			$srvr_stmt = sqlsrv_query( $conn, $drop_CH );
			if( $srvr_stmt === false ) {
				die( print_r( sqlsrv_errors(), true));
			}
			$srvr_stmt = sqlsrv_query( $conn, $drop_CD );
			if( $srvr_stmt === false ) {
				die( print_r( sqlsrv_errors(), true));
			}
			$srvr_stmt = sqlsrv_query( $conn, $drop_COL );
			if( $srvr_stmt === false ) {
				die( print_r( sqlsrv_errors(), true));
			}
			$srvr_stmt = sqlsrv_query( $conn, $drop_EX );
			if( $srvr_stmt === false ) {
				die( print_r( sqlsrv_errors(), true));
			}
			$srvr_stmt = sqlsrv_query( $conn, $drop_E );
			if( $srvr_stmt === false ) {
				die( print_r( sqlsrv_errors(), true));
			}





			// create table 3 Employee (CREATE THIS FIRST! CertNo need to be a foreign key for all other tables!
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

			// create table 1 CourseDetail
			$create_CD = "CREATE TABLE dbo.New_CourseDetail (
				CertNo float references dbo.New_Employee(CertNo), -- foreign key from Employee
				CourseYear nvarchar(10),			-- from dbo.Details(FiscalYear)
				ItemNumber float,					-- undefined, maybe an index for all possible courses, if so then new table all_courses needed
				CourseName nvarchar(100),			-- from dbo.tbl: Course Title + dbo.Details(Course)
				CourseLocation nvarchar(50),		-- from dbo.Details(Location)
				CourseGrade nvarchar(50),			-- from dbo.Details(Grade)
				HoursEarned float,					-- from dbo.Details(HoursEarned)
				EndDate datetime2(0),				-- from dbo.Details(EndDate)

				primary key (CertNo, CourseYear, CourseName), -- if ItemNumber correctly implemented, switch to it! CertNo should be foreign key
			)";

			// create table 4 CarryoverLimits
			$create_COL = "CREATE TABLE dbo.New_CarryoverLimits (
				[Status] nvarchar(50),				-- primary key from dbo.New_CertHistory, referenced in CertHistory(Status)
				RequiredHours float,				-- undefined
				Year1Limit float,					-- undefined
				Year2Limit float,					-- undefined
				Year3Limit float,					-- undefined

				primary key ([Status]),
			)";

			// create table 2 CertHistory
			$create_CH = "CREATE TABLE dbo.New_CertHistory (
				CertNo float references dbo.New_Employee(CertNo), -- foreign key from Employee
				CertYear nvarchar(10),				-- from dbo.AnnualReq
				CertType nvarchar(50),				-- from dbo.AnnualReq
				[Status] nvarchar(50) references dbo.New_CarryoverLimits([Status]),				-- from dbo.AnnualReq
				HoursEarned float,					-- from dbo.AnnualReq
				CurrentYrBalance float,				-- from dbo.AnnualReq
				PriorYrBalance float,				-- from dbo.AnnualReq
				CarryToYr1 float,					-- from dbo.AnnualReq
				CarryToYr2 float,					-- from dbo.AnnualReq
				CarryToYr3 float,					-- from dbo.AnnualReq
				CarryFwdTotal float,				-- from dbo.AnnualReq

				primary key (CertNo, CertYear), -- CertNo should be foreign key
			)";

			// create table 5 EmployeeID_Xref
			$create_EX = "CREATE TABLE dbo.New_EmployeeID_Xref (
				CertNo float references dbo.New_Employee(CertNo),
				EmployeeID float,
				primary key (CertNo),
				unique (EmployeeID),
			)";

			$srvr_stmt = sqlsrv_query( $conn, $create_E );
			if( $srvr_stmt === false ) {
				die( print_r( sqlsrv_errors(), true));
			}
			$srvr_stmt = sqlsrv_query( $conn, $create_CD );
			if( $srvr_stmt === false ) {
				die( print_r( sqlsrv_errors(), true));
			}
			$srvr_stmt = sqlsrv_query( $conn, $create_COL );
			if( $srvr_stmt === false ) {
				die( print_r( sqlsrv_errors(), true));
			}
			$srvr_stmt = sqlsrv_query( $conn, $create_CH );
			if( $srvr_stmt === false ) {
				die( print_r( sqlsrv_errors(), true));
			}
			$srvr_stmt = sqlsrv_query( $conn, $create_EX );
			if( $srvr_stmt === false ) {
				die( print_r( sqlsrv_errors(), true));
			}




			/////////////////////////////////// read data from mdb and then insert into SQL sever ///////////////////////////////////

			// $sql_mdb_query  = "SELECT t.LastName, t.FirstName, t.MiddleName, t.CertNo, t.CountyCode, t.CountyName, t.TempCertDate,
			// 					t.PermCertDate, t.AdvCertDate, t.CurrentStatus, t.Status, t.CertType, t.FiscalYear, t.EarnedHours,
			// 					t.RequiredHours, t.CurrentYearBalance, t.PriorYearBalance, t.CarryToYear1, t.CarryToYear2, t.CarryToYear3,
			// 					t.CarryForwardTotal ";
			// $sql_mdb_query .= "  FROM AnnualReq t;";

			/////////////////////////////////// 1. Summary ///////////////////////////////////
			// query for mdb to read "Summary"
			$Summary_mdb  = "SELECT t.CountyCode, t.CountyName, t.FiscalYear, t.LastName, t.FirstName, t.MiddleName,
									t.CertNo, t.Auditor, t.CarryForward, t.CertType";
			$Summary_mdb .= "  FROM Summary t;";
			$Summary_data = $mdb->query($Summary_mdb);

			// query for sql server to insert "Summary" into "Employee"
			$Summary_to_Employee = "INSERT INTO New_Employee (CertNo, FirstName, LastName, Auditor) VALUES (?, ?, ?, ?)";
			$Summary_data_row_count_limit = (int)30;
			$Summary_data_row_count = 0;
			echo "===== below is mdb data =====<br />";
			while ($Summary_data_row = $Summary_data->fetch()) {
				if ($Summary_data_row_count==$Summary_data_row_count_limit) break;
				$CertNo = $Summary_data_row["CertNo"];
				$LastName = $Summary_data_row["FirstName"];
				$FirstName = $Summary_data_row["LastName"];
				$Auditor = $Summary_data_row["Auditor"];
				echo "<tr><td>" . $LastName . "</td><td>" . $FirstName . "</td><td>" . (int)$CertNo . "</td><td>" . $Auditor . "</td></tr>";
				$Summary_data_row_count ++;
				// insertion
				$params = array($CertNo, $FirstName, $LastName, $Auditor);
				$stmt = sqlsrv_query( $conn, $Summary_to_Employee, $params);
				if( $stmt === false ) {
					die( print_r( sqlsrv_errors(), true));
				}
			}
			echo "<br />";
			echo "<br />";



		?>
</table>
</body>
</html>
