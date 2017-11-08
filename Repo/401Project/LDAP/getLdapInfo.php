<?php
include_once "authenticate.php";
include_once "../lib/php/constants.php";
session_start();

	$ldapusername = $_SESSION['USERNAME'];
	$ldappassword = $_SESSION['password'];
	$server = LDAP_SERVER_NAME;
	$ldap = ldap_connect($server);
	if ($ldap) {
		$look_up_username;
		// SQL to get Employeeid in XRef table
		$serverName = SQL_SERVER_NAME;
		$uid = SQL_SERVER_USERNAME;
		$pwd = SQL_SERVER_PASSWORD;
		$db = SQL_SERVER_LACDATABASE;
		$connectionInfo = array( "UID"=>$uid,
					"PWD"=>$pwd,
					"Database"=>$db,
					"ReturnDatesAsStrings"=>true);  // convert datetime to string
		/* Connect using SQL Server Authentication. */
		$conn = sqlsrv_connect( $serverName, $connectionInfo);
		if( $conn === false ) {
			echo "Unable to connect.</br>";
			die( print_r( sqlsrv_errors(), true));
		}
		// query to get look_up_username

		// $tsql = "SELECT * FROM [XREF] WHERE EmpNo=".$employeeid;
		// $stmt = sqlsrv_query( $conn, $tsql);
		// if( $stmt === false ){
		// 	echo "Error in executing query.</br>";
		// 	die( print_r( sqlsrv_errors(), true));
		// } else {
		// 	$rows = sqlsrv_num_rows($stmt);
		// 	sqlsrv_free_stmt($stmt);
		// 	sqlsrv_close($conn);
		// 	if($rows > 0) { // if exists in the appraiser databse
		// 		$_SESSION["logged_in"] = TRUE;
		// 		$_SESSION["CERTNO"] = $row['CertNo'];
		// 		if ($row['Role'] == admin) {
		// 			$_SESSION["ROLE"] = "admin";
		// 			header("Location: " . ADMIN_HOME_PAGE_URL);
		// 		} else { // if appraiser who are not admin
		// 			header("Location: " . USER_HOME_PAGE_URL);
		// 		}
		// 	}
		// 	else {	 // if not an appraiser or admin
		// 		$_SESSION["logged_in"] = FALSE;
		// 		header("Location: " . ERROR_PAGE_URL);
		// 	}
		// }

		ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
		$bind = ldap_bind($ldap, $ldapusername, $ldappassword);
		$basedn = "OU=ASSR,DC=laassessor,DC=co,DC=la,DC=ca,DC=us";
		$filter = '(samaccountname='.$look_up_username.')';
		$attributes = array("displayname", "samaccountname", "mail", "manager","givenname",
							"telephoneNumber","title");
		$result = ldap_search($ldap, $basedn, $filter, $attributes);
		if (FALSE !== $result) {
			$info = ldap_get_entries($ldap, $result);
			$result[];
			if($info["count"] > 0) {
				for ($i=0; $i<$info["count"]; $i++) {
					 $result[] = $info[$i]["displayname"][0];
					 $result[] = $info[$i]["mail"][0];
					 $result[] = (string)$info[$i]["manager"][0];
					 $result[] = $info[$i]["givenname"][0];
					 // phone number
					 $result[] = $info[$i]["telephoneNumber"][0];
					 // pay location

					 // title
					 $result[] = $info[$i]["title"][0];
				}
				echo json_encode($result);
			}
			else {
				echo "error when try to connect LDAP";
			}
		}
	}

?>