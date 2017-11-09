<?php
	// IMPORTANT: toggle T/F for line below, when switching between Azure and LAC Server!
	define("ON_AZURE", True);

	define("PREFIX", "http://localhost/");


	if(ON_AZURE) {
		define("DIR", "appraisaltraining/Repo/401Project/");

		// SQL Server Name
		define("SQL_SERVER_NAME", "Assessor");

		// Importing From These XLSX Paths
		define("PATH_XLSX_ANNUALREQ",	"C:/inetpub/wwwroot/appraisaltraining/Repo/401Project/lib/php/admin/JT_ML_AdminUploadDatabase/original_xlsx_files/AnnualReq Los Angeles.xlsx");
		define("PATH_XLSX_SUMMARY",		"C:/inetpub/wwwroot/appraisaltraining/Repo/401Project/lib/php/admin/JT_ML_AdminUploadDatabase/original_xlsx_files/Summary Los Angeles.xlsx");
		define("PATH_XLSX_DETAILS",		"C:/inetpub/wwwroot/appraisaltraining/Repo/401Project/lib/php/admin/JT_ML_AdminUploadDatabase/original_xlsx_files/Los Angeles Training Details.xlsx");
	}
	else {
		define("DIR", "git_appraisal_training/appraisaltraining/Repo/401Project/");

		// SQL Server Name
		define("SQL_SERVER_NAME", "HTRAINDATADEV-V");
		define("SQL_SERVER_LACDATABASE", "ml_LAC_mdb_data");
	}

	// File Paths
	define("USER_HOME_PAGE_URL", PREFIX.DIR."User/UserHome.php");
	define("ADMIN_HOME_PAGE_URL", PREFIX.DIR."Admin/AdminHome.php");
	define("LOGIN_URL", PREFIX.DIR."index.php");
	define("ERROR_URL", PREFIX.DIR."error.php");

	// Common SQL Server Credentials/DatabaseNames
	define("SQL_SERVER_USERNAME", "superadmin");
	define("SQL_SERVER_PASSWORD", "admin");
	define("SQL_SERVER_BOEDATABASE", "BOE");
	define("SQL_SERVER_LACDATABASE", "temporary_5_table");
	// define("SQL_SERVER_LACDATABASE_ML_DEVELOPMENT", "ml_development");
	define("SQL_SERVER_LACDATABASE_ML_DEVELOPMENT_no_drop_00", "ml_development_no_drop_00"); // TOT add this db in SQL server!
	define("SQL_SERVER_LACDATABASE_ML_DEVELOPMENT_no_drop_01", "ml_development_no_drop_01"); // TOT add this db in SQL server!
	define("SQL_SERVER_LACDATABASE_ML_DEVELOPMENT_no_drop_02", "ml_development_no_drop_02"); // TOT add this db in SQL server!

	// LDAP Info
	define("LDAP_SERVER_NAME", "ldap://laassessor.co.la.ca.us");
?>
