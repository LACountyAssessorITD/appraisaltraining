<?php
/*
	This code provides a function to retrieve LDAP information for both Admin and User
	By specifying an Employee ID
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

	function getItemNumber($title) {
		$title = strtolower($title);
		if (strpos($title, strtolower("Appraiser"))!==FALSE) return 1962;
		else if (strpos($title, strtolower("Principal Appraiser"))!==FALSE) return 1970;
		else if (strpos($title, strtolower("Appraiser Specialist"))!==FALSE) return 1965;
		else if (strpos($title, strtolower("Supervising Appraiser"))!==FALSE) return 1968;
		else if (strpos($title, strtolower("Appraiser Assistant"))!==FALSE) return 1958;
		else if (strpos($title, strtolower("Appraiser Trainee"))!==FALSE) return 1960;
		else if (strpos($title, strtolower("Chief Appraiser"))!==FALSE) return 1974;
	}

	function getInfo($empNo) {
		error_reporting(0);
		if ($empNo == "") {
			$inforesult  = array();
			for ($i=0; $i<8; $i ++)
				$inforesult[] = "NA";
			return $inforesult;
		} else {
			$look_up_username = (string)$empNo;
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
									"telephoneNumber","department","title");
				$result = ldap_search($ldap, $basedn, $filter, $attributes);
				if (FALSE !== $result) {
					$info = ldap_get_entries($ldap, $result);
					$inforesult  = array();
					if($info["count"] > 0 /* && sizeof($attributes)==8 */) {
						// echo $look_up_username."\n";
						for ($i=0; $i<$info["count"]; $i++) {
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
						}
						ldap_close($ldap);
						return $inforesult;
					}
					else {
						$inforesult[] = "NA";
						$inforesult[] = "NA";
						$inforesult[] = "NA";
						$inforesult[] = "NA";
						$inforesult[] = "NA";
						$inforesult[] = "NA";
						$inforesult[] = "NA";
						$inforesult[] = "NA";
						// echo $look_up_username;
						return $inforesult;
					}

				}
			}
		}
	}

	// function getInfo($empNo) {
	// 	$inforesult  = array();
	// 	if ($empNo == "") {
	// 		for ($i=0; $i<8; $i ++)
	// 			$inforesult[] = "NA";
	// 	}
	// 	else {
	// 		$inforesult[] ="Yining Huang";
	// 		$inforesult[] ="test@123.com";
	// 		$inforesult[] = "Manager abc";
	// 		$inforesult[] = "Yining";
	// 		$inforesult[] = "123123"; // phone number
	// 		$inforesult[] = "Best. Depart";	// pay location
	// 		$title_string = "Appraiser";
	// 		$inforesult[] = $title_string; // title
	// 		$inforesult[] = getItemNumber($title_string);
	// 	}

	// 	return $inforesult;
	// }

?>
