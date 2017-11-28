<?php
	if (session_status() == PHP_SESSION_NONE) {
    	session_start();
	}
	// IMPORTANT: toggle T/F for line below, when switching between Azure and LAC Server!
	define("ON_AZURE", True);

	define("HTTP_PREFIX", "http://localhost/");
	define("DIR_PREFIX", "C:/inetpub/wwwroot/");

	if(ON_AZURE) {
		// git repo file path
		define("DIR", "appraisaltraining/Repo/401Project/");
		// SQL Server Name
		define("SQL_SERVER_NAME", "Assessor");
		define("SQL_SERVER_LACDATABASE", $_SESSION['SQL_SERVER_LACDATABASE']); // ""
	}
	else {
		// git repo file path
		define("DIR", "git_appraisal_training/appraisaltraining/Repo/401Project/");
		// SQL Server Name
		define("SQL_SERVER_NAME", "HTRAINDATADEV-V");
		// define("SQL_SERVER_LACDATABASE", "ml_LAC_mdb_data"); // "temporary_5_table"
	}
	// Importing From These XLSX Paths
	define("PATH_XLSX_ANNUALREQ",	DIR_PREFIX.DIR."lib/php/admin/JT_ML_AdminUploadDatabase/original_xlsx_files/AnnualReq Los Angeles.xlsx");
	define("PATH_XLSX_SUMMARY",		DIR_PREFIX.DIR."lib/php/admin/JT_ML_AdminUploadDatabase/original_xlsx_files/Summary Los Angeles.xlsx");
	define("PATH_XLSX_DETAILS",		DIR_PREFIX.DIR."lib/php/admin/JT_ML_AdminUploadDatabase/original_xlsx_files/Los Angeles Training Details.xlsx");

	// File Paths
	define("USER_HOME_PAGE_URL",	HTTP_PREFIX.DIR."User/UserHome.php");
	define("ADMIN_HOME_PAGE_URL",	HTTP_PREFIX.DIR."Admin/AdminHome.php");
	define("LOGIN_URL",				HTTP_PREFIX.DIR."index.php");
	define("ERROR_URL",				HTTP_PREFIX.DIR."error.php");

	// Common SQL Server Credentials/DatabaseNames
	define("SQL_SERVER_USERNAME", "superadmin");
	define("SQL_SERVER_PASSWORD", "admin");
	define("SQL_SERVER_MASTERDATABASE", "ml_development_no_drop_00");

	// define("SQL_SERVER_LACDATABASE_ML_DEVELOPMENT", "ml_development");
	define("SQL_SERVER_LACDATABASE_ML_DEVELOPMENT_no_drop_00", "ml_development_no_drop_00"); // TOT add this db in SQL server!
	define("SQL_SERVER_LACDATABASE_ML_DEVELOPMENT_no_drop_01", "ml_development_no_drop_01"); // TOT add this db in SQL server!
	define("SQL_SERVER_LACDATABASE_ML_DEVELOPMENT_no_drop_02", "ml_development_no_drop_02"); // TOT add this db in SQL server!

	// LDAP Info
	define("LDAP_SERVER_NAME", "ldap://laassessor.co.la.ca.us");
	session_write_close();
?>
