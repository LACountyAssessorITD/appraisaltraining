<?php
include_once "Conn.php";
include_once "constants.php";
session_start();
global $role;


	$ldapusername = "laassessor"."\\".$_POST["usernameInput"];
	$_SESSION['USERNAME']=$_POST["usernameInput"];
	$ldappassword = $_POST["passwordInput"];
	$_SESSION['password']=$_POST["passwordInput"];

	if (!authenticateUser($ldapusername, $ldappassword)) {
		
		$_SESSION["logged_in"] = FALSE;
		header("Location: " . LOGIN_ERROR_URL);		
	}
	else
	{	
		$servername = SQL_SERVER_NAME;
		$username = SQL_SERVER_USERNAME;
		$password = SQL_SERVER_PASSWORD;
		$dbname = SQL_SERVER_DATABASE;
	
		////$conn = new mysqli($servername, $username, $password, $dbname);
		$connectionInfo = array("UID"=>$username, "PWD"=>$password, "Database"=>$dbname);
		$conn = sqlsrv_connect($servername, $connectionInfo);
		
		if($conn === false) {
			echo "Connection could not be established.";
			die(print_r(sqlsrv_errors(), true));
		}
		
		echo "Connect Successfully\n";	
		$server = LDAP_SERVER_NAME;
		$ldap = ldap_connect($server);
		$userid=$_POST["usernameInput"];
		if ($ldap) 
		{	
			ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
			ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
			$bind = ldap_bind($ldap, $ldapusername, $ldappassword);	
			$basedn = "OU=ASSR,DC=laassessor,DC=co,DC=la,DC=ca,DC=us";
			$filter = '(samaccountname='.$userid.')';
			$attributes = array("displayname");
			$result = ldap_search($ldap, $basedn, $filter, $attributes);

			if (FALSE !== $result)
			{

	
				$info = ldap_get_entries($ldap, $result);
				if($info["count"] > 0)
				{				
					for ($i=0; $i<$info["count"]; $i++) 
					{
						 $_SESSION['NAME']=$info[$i]["displayname"][0];
					}
				}
				else
				{
					echo "error";
				}			
			}
		}
				
		$sql="SELECT * FROM users WHERE Username='".$_POST["usernameInput"]."' AND ActiveStatus=1";
		$params = array();
		$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
		$result = sqlsrv_query($conn, $sql, $params, $options);
		if($result === false) {
			die(print_r(sqlsrv_errors(), true));
		}
		$rows = sqlsrv_num_rows($result);
 		if($rows > 0)
		{
			$row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
			$_SESSION["ROLE"] = $row['Role'];
			$_SESSION["logged_in"] = TRUE;
			header("Location: " . HOME_PAGE_URL);
		}
		else
		{	
			$_SESSION["logged_in"] = FALSE;
			header("Location: " . LOGIN_ERROR_URL);
		}
		////$conn->close();
		sqlsrv_close ($conn);		
	}
?>