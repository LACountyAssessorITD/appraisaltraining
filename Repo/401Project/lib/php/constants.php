<?php
	// IMPORTANT: toggle between the below two lines when switching between Azure and LAC Server!
	define("SQL_SERVER_NAME", "Assessor");				// SQL Server Name for Azure
	// define("SQL_SERVER_NAME", "HTRAINDATADEV-V");		// SQL Server Name for LAC Server

	// SQL Server Common Credentials/DatabaseNames
	define("SQL_SERVER_USERNAME", "superadmin");
	define("SQL_SERVER_PASSWORD", "admin");
	define("SQL_SERVER_BOEDATABASE", "BOE");
	define("SQL_SERVER_LACDATABASE", "ml_LAC_mdb_data");

	// LDAP Info
	define("LDAP_SERVER_NAME", "ldap://laassessor.co.la.ca.us");

	define("USER_HOME_PAGE_URL", "../../User/UserHome.html");
	define("ADMIN_HOME_PAGE_URL", "../../Admin/AdminHome.html");
    define("LOGIN_URL", "../../index.php");
?>