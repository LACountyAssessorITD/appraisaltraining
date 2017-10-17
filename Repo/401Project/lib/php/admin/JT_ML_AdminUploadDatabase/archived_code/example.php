<?php
error_reporting(E_ALL ^ E_NOTICE);
require_once 'excel_reader2.php';
$data = new Spreadsheet_Excel_Reader("summary.xls", false); //FALSE makes it not care about formatting --> saves memory
?>
<html>
<head>
<style>

</style>
</head>

<body>
<?php //echo $data->val(2,'B');				//THIS RETRIEVES DATA FROM ROW 2 COLUMN B
		//echo $data->dump(true,true);		//THIS OUTPUTS ALL DATA


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
			CourseHours float,					-- from dbo.Details(HoursEarned)
			EndDate datetime2(0),				-- from dbo.Details(EndDate)

			-- primary key (CertNo, CourseYear, CourseName), -- if ItemNumber correctly implemented, switch to it! CertNo should be foreign key
		)";

		// create table 4 CarryoverLimits
		$create_COL = "CREATE TABLE dbo.New_CarryoverLimits (
			[Status] nvarchar(50),				-- primary key from dbo.New_CertHistory, referenced in CertHistory(Status)
			RequiredHours float,				-- undefined
			Year1Limit float,					-- undefined
			Year2Limit float,					-- undefined
			Year3Limit float,					-- undefined

			-- primary key ([Status]),
		)";

		// create table 2 CertHistory
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

		////////////////////////////////// read data from mdb and then insert into SQL sever ///////////////////////////////////



		/////////////////////////////////// 1. Summary ///////////////////////////////////

		$Summary_to_Employee = "INSERT INTO New_Employee (CertNo, FirstName, LastName, Auditor) VALUES (?, ?, ?, ?)";

		$row_limit = (int)10000; // TOT modify later! make large enuf so that all rows gets inserted!
		$row_count = 1;

		echo "===== below is mdb data =====<br />";
		while ($data->rowcount($sheet_index=0) != $row_count ) {

			if ($row_count==$row_limit) break;
			$row_count ++;

			$CertNo = $data->val($row_count,'G');
			$LastName = $data->val($row_count,'D');
			$FirstName = $data->val($row_count,'E');
			$Auditor = $data->val($row_count,'H');
			echo "<tr><td>" . $LastName . "</td><td>" . $FirstName . "</td><td>" . (int)$CertNo . "</td><td>" . $Auditor . "</td></tr>";
			// insertion
			$params = array($CertNo, $FirstName, $LastName, $Auditor);
			$stmt = sqlsrv_query( $conn, $Summary_to_Employee, $params);
			if( $stmt === false ) {
				die( print_r(sqlsrv_errors(), true) );
			}
		}
		echo "<br />";
?>
</body>
</html>
