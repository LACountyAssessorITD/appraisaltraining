<?php
/* 
	This code retrieve LDAP information for both Admin and User
	Handle the post method (by specifying an employee number) from an ajax call
	@ Yining Huang

	0 - display name (full name)
	1 - Email
	2 - Magager Name
	3 - Given Name (firstName)
	4 - phone number
	5 - Department / Pay location
	6 - Title
	7 - Item Number

*/
include_once "../constants.php";
include_once "authenticate.php";
session_start();

	function getItemNumber($title) {
		if ($title == "Appraiser") return 1962;
		else if (strpos( $title, "Principal Appraiser" )) return 1970;
		else if (strpos( $title, "Appraiser Specialist" )) return 1965;
		else if (strpos( $title, "Supervising Appraiser" )) return 1968;
		else if (strpos( $title, "Appraiser Assistant" )) return 1958;
		else if (strpos( $title, "Appraiser Trainee" )) return 1960;
		else if (strpos( $title, "Chief Appraiser" )) return 1974;
	}

	$look_up_username = (string)$_POST['empNo'];
	$ldapusername = "laassessor"."\\".$_SESSION['USERNAME'];
	$ldappassword = $_SESSION['password'];

	$server = LDAP_SERVER_NAME;
	$ldap = ldap_connect($server);
	if ($ldap) {
		ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
		$bind = ldap_bind($ldap, $ldapusername, $ldappassword);
		$basedn = "OU=ASSR,DC=laassessor,DC=co,DC=la,DC=ca,DC=us";
		$filter = '(samaccountname='.$look_up_username.')';
		// $filter = '(samaccountname=243141)';
		$attributes = array("displayname", "samaccountname", "mail", "manager","givenname",
							"telephoneNumber","department","title","sn");
		$result = ldap_search($ldap, $basedn, $filter, $attributes);

		if (FALSE !== $result) {
			$info = ldap_get_entries($ldap, $result);
			$inforesult  = array();
			if($info["count"] > 0) {
				for ($i=0; $i<$info["count"]; $i++) {
					/*
						0 - display name (full name)
						1 - Email
						2 - Magager Name
						3 - Given Name (firstName)
						4 - phone number
						5 - Department / Pay location
						6 - Title
						7 - Item Number
					*/
					$inforesult[] = $info[$i]["displayname"][0];
					$inforesult[] =$info[$i]["mail"][0];

					$managerArrOne = explode(",", (string)$info[$i]["manager"][0], 3);
					$tempName = (string)$managerArrOne[0]." ".(string)$managerArrOne[1];
					$managerArrTwo = explode("=", $tempName, 2);
					$inforesult[] = (string)$managerArrTwo[1];
					// $inforesult[] = $info[$i]["manager"][0];

					$inforesult[] = $info[$i]["givenname"][0];
					$inforesult[] = $info[$i]["telephonenumber"][0];	 // phone number
					$inforesult[] = $info[$i]["department"][0];	// pay location

					$title_string = (string)$info[$i]["title"][0];
					$inforesult[] = $title_string; // title
					$inforesult[] = getItemNumber($title_string);
					$inforesult[] = $info[$i]["sn"][0];	//index == 8
				}
				echo json_encode($inforesult);
			}
			else {
				echo "error when try to connect LDAP";
			}
			 ldap_close($ldap);
		}
	}

?>