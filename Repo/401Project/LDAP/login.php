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
	} else {
		// if authencate successfully, get relative info
		$server = LDAP_SERVER_NAME;
		$ldap = ldap_connect($server);
		$user_name=$_POST["username"];
		if ($ldap) {
			ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
			ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
			$bind = ldap_bind($ldap, $ldapusername, $ldappassword);
			$basedn = "OU=ASSR,DC=laassessor,DC=co,DC=la,DC=ca,DC=us";
			$filter = '(samaccountname='.$user_name.')';
			$attributes = array("displayname", "samaccountname", "mail", "manager","givenname");
			$result = ldap_search($ldap, $basedn, $filter, $attributes);
			if (FALSE !== $result) {
				$info = ldap_get_entries($ldap, $result);
				if($info["count"] > 0) {
					for ($i=0; $i<$info["count"]; $i++) {
						$_SESSION['NAME']=$info[$i]["displayname"][0];
						$_SESSION['EMAIL']=$info[$i]["mail"][0];
						$_SESSION['MANAGER']=$info[$i]["manager"][0];
						$_SESSION['FIRSTNAME']=$info[$i]["givenname"][0];

						echo "manager_info: ".$_SESSION['MANAGER'];
						$string_for_manager_info = (string)$_SESSION['MANAGER'];
						$manager_name=str_replace('CN=','',strstr($string_for_manager_info,',OU',true));
						echo "manager_info!!!!!!: ".$manager_name;


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

						echo "EmployeeID is: ".$user_name."\n";

						// $tsql = "SELECT * FROM [New_EmployeeID_Xref] WHERE EmployeeID=".(int)$user_name;
						$tsql = "SELECT * FROM [New_EmployeeID_Xref] WHERE EmployeeID=".$user_name;
						// $tsql = "SELECT * FROM [New_EmployeeID_Xref] WHERE EmployeeID = 603253"; // Angel's EmployeeID!
						// echo "looking in XREF for employeeID: "
						$stmt = sqlsrv_query( $conn, $tsql);
						if( $stmt === false )
						{
						     echo "Error in executing query.</br>";
						     die( print_r( sqlsrv_errors(), true));
						}
						else {
							// $rows = sqlsrv_num_rows($stmt);


							$rows = sqlsrv_fetch_array($stmt);

							// echo "row is: ".$rows."\n";

							sqlsrv_free_stmt($stmt);
							sqlsrv_close($conn);
						 	// if($rows > 0) { // if exists in the appraiser databse
						 	if($rows) { // if exists in the appraiser databse

								// echo "\ni'm here!\n";


						 		$_SESSION["logged_in"] = true;
						 		$_SESSION["CERTNO"] = $rows['CertNo'];
						 		$_SESSION["ROLE"] = $rows['IsAdmin'];
						 		$_SESSION['EMPLOYEEID'] = $user_name;
						 		if ($_SESSION["ROLE"] == 1) {
									// header("Location: " . ADMIN_HOME_PAGE_URL);
								} else if ($_SESSION["ROLE"] == 0) {
									header("Location: " . USER_HOME_PAGE_URL);
								} else {
									$_SESSION["logged_in"] = false;
									header("Location: " . ERROR_URL);
								}

								// */
							}
							else {	 // if not an appraiser or admin
								$_SESSION["logged_in"] = false;
								header("Location: " . LOGIN_URL);
							}
						}
					}
				}
				else {
					echo "error when try to connect LDAP";
				}
			}
		}
	}
?>