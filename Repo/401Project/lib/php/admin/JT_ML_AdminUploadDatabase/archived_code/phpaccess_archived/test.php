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

			$dbName = "LosAngeles.mdb";
			if (!file_exists($dbName)) {
				die("Could not find database file.");
			}
			$db = new PDO("odbc:DRIVER={Microsoft Access Driver (*.mdb)}; DBQ=$dbName; Uid=; Pwd=;");
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

			/////////////////////////////////// read data from mdb and then insert into SQL sever ///////////////////////////////////
			$row_count_limit = (int)15;
			$row_count = 0;

			/*
			$sql_mdb_query  = "SELECT t.LastName, t.FirstName, t.MiddleName, t.CertNo, t.CountyCode, t.CountyName, t.TempCertDate,
								t.PermCertDate, t.AdvCertDate, t.CurrentStatus, t.Status, t.CertType, t.FiscalYear, t.EarnedHours,
								t.RequiredHours, t.CurrentYearBalance, t.PriorYearBalance, t.CarryToYear1, t.CarryToYear2, t.CarryToYear3,
								t.CarryForwardTotal ";
			$sql_mdb_query .= "  FROM AnnualReq t;";
			*/

			$sql_mdb_query_summary  = "SELECT t.CountyCode, t.CountyName, t.FiscalYear, t.LastName, t.FirstName, t.MiddleName,
									t.CertNo, t.Auditor, t.CarryForward, t.CertType";
			$sql_mdb_query_summary .= "  FROM Summary t;";

			$sql_server_query = "INSERT INTO New_Employee (CertNo, FirstName, LastName) VALUES (?, ?, ?)";


			$result = $db->query($sql_mdb_query_summary);
			$AnnualReq = array();

			$row_count_limit = (int)15;
			$row_count = 0;

			echo "===== below is mdb data =====<br />";
			while ($row = $result->fetch()) {
				if ($row_count==$row_count_limit) break;
				$LastName = $row["FirstName"];
				$FirstName = $row["LastName"];
				$CertNo = $row["CertNo"];
				echo "<tr><td>" . $LastName . "</td><td>" . $FirstName . "</td><td>" . (int)$CertNo . "</td></tr>";
				$row_count ++;


				// insertion
				$params = array($CertNo, $FirstName, $LastName);
				$stmt = sqlsrv_query( $conn, $sql_server_query, $params);
				if( $stmt === false ) {
					die( print_r( sqlsrv_errors(), true));
				}


			}

		?>
</table>
</body>
</html>
