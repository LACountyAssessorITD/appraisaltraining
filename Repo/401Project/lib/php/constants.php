<?php
	// IMPORTANT: toggle T/F for line below, when switching between Azure and LAC Server!
	define("ON_AZURE", True);

	if(ON_AZURE) {
		// SQL Server Name
		define("SQL_SERVER_NAME", "Assessor");
		// File Paths
		define("USER_HOME_PAGE_URL", "../../User/UserHome.html");
		define("ADMIN_HOME_PAGE_URL", "../../Admin/AdminHome.html");
		define("LOGIN_URL", "../../index.php");
		define("ERROR_URL", "../../error.php");
		// Importing From These XLSX Paths
		define("PATH_XLSX_ANNUALREQ",	"C:/inetpub/wwwroot/appraisaltraining/Repo/401Project/lib/php/admin/JT_ML_AdminUploadDatabase/original_xlsx_files/AnnualReq Los Angeles.xlsx");
		define("PATH_XLSX_SUMMARY",		"C:/inetpub/wwwroot/appraisaltraining/Repo/401Project/lib/php/admin/JT_ML_AdminUploadDatabase/original_xlsx_files/Summary Los Angeles.xlsx");
		define("PATH_XLSX_DETAILS",		"C:/inetpub/wwwroot/appraisaltraining/Repo/401Project/lib/php/admin/JT_ML_AdminUploadDatabase/original_xlsx_files/Los Angeles Training Details.xlsx");
	}
	else {
		// SQL Server Name
		define("SQL_SERVER_NAME", "HTRAINDATADEV-V");
	}

	// Common SQL Server Credentials/DatabaseNames
	define("SQL_SERVER_USERNAME", "superadmin");
	define("SQL_SERVER_PASSWORD", "admin");
	define("SQL_SERVER_BOEDATABASE", "BOE");
	define("SQL_SERVER_LACDATABASE", "temporary_5_table");
	define("SQL_SERVER_LACDATABASE_ML_DEVELOPMENT", "ml_development");

	// LDAP Info
	define("LDAP_SERVER_NAME", "ldap://laassessor.co.la.ca.us");
?>
