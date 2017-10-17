<?php
include_once "authenticate.php";
include_once "../lib/php/constants.php";
session_start();

	$ldapusername = "laassessor"."\\".$_POST["username"];
	$_SESSION['USERNAME']=$_POST["username"];
	$ldappassword = $_POST["password"];
	$_SESSION['password']=$_POST["password"];

	if (!authenticateUser($ldapusername, $ldappassword)) {
		$_SESSION["logged_in"] = FALSE;
		header("Location: " . LOGIN_URL);
		echo "authenticateUser failure";
	} else {
		// Get display name from LDAP
		// Get Employee ID from LDAP
		$server = LDAP_SERVER_NAME;
		$ldap = ldap_connect($server);
		$user_name=$_POST["username"];
		$_SESSION['EMPLOYEEID']=$user_name;
		if ($ldap) {
			ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
			ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
			$bind = ldap_bind($ldap, $ldapusername, $ldappassword);
			$basedn = "OU=ASSR,DC=laassessor,DC=co,DC=la,DC=ca,DC=us";
			$filter = '(samaccountname='.$user_name.')';
			$attributes = array("displayname", "samaccountname", "mail", "manager");
			$result = ldap_search($ldap, $basedn, $filter, $attributes);
			if (FALSE !== $result) {
				$info = ldap_get_entries($ldap, $result);
				if($info["count"] > 0) {
					for ($i=0; $i<$info["count"]; $i++) {
						 $_SESSION['NAME']=$info[$i]["displayname"][0];
						 $_SESSION['EMAIL']=$info[$i]["mail"][0];
						 $_SESSION['MANAGER']=$info[$i]["manager"][0];
						 
						 echo "The Name is ".$_SESSION['NAME']." with ID ".$_SESSION['EMPLOYEEID']." with email ".$_SESSION['EMAIL']." with manager ".$_SESSION['MANAGER'];
					}
				}
				else {
					echo "error when try to connect LDAP 38";
				}
			}
		}

		// See if it is an appraiser
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
		if( $conn === false )
		{
		     echo "Unable to connect.</br>";
		     die( print_r( sqlsrv_errors(), true));
		}
		$tsql = "SELECT * FROM [tblCertificate Nos] WHERE EmpNo=".$_SESSION['EMPLOYEEID'];
		$stmt = sqlsrv_query( $conn, $tsql);
		if( $stmt === false )
		{
		     echo "Error in executing query.</br>";
		     die( print_r( sqlsrv_errors(), true));
		}
		else {
			$rows = sqlsrv_num_rows($stmt);
		 	if($rows > 0) {
				$_SESSION["ROLE"] = $row['Role'];
				$_SESSION["logged_in"] = TRUE;
				if ($_SESSION["ROLE"] == "admin") { // if Admin
					header("Location: " . ADMIN_HOME_PAGE_URL);
				}
				else { // if User
					header("Location: " . USER_HOME_PAGE_URL);
				}
			}
			else {	
				$_SESSION["logged_in"] = FALSE;
				header("Location: " . LOGIN_URL);
			}
		}
		sqlsrv_free_stmt($stmt);
		sqlsrv_close($conn);
	}
?>