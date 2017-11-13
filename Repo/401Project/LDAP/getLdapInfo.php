<?php
include_once "../lib/php/constants.php";
session_start();
	function getTitleString($num) {
		// Appraiser 1962
		// Principal Appraiser 1970
		// Appraiser Specilist 1965
		// Supervising appraiser 1968
		// Appraiser Assistant 1958
		// Appraiser Trainee 1960
		// Chief Appraiser 1974
		if ($num == 1962) {
			return "Appraiser";
		} else if ($num == 1970) {
			return "Principal Appraiser";
		} else if ($num == 1965) {
			return "Appraiser Specilist";
		} else if ($num == 1968) {
			return "Supervising Appraiser";
		} else if ($num == 1958) {
			return "Appraiser Assistant";
		} else if ($num == 1960) {
			return "Appraiser Trainee";
		} else if ($num == 1974) {
			return "Chief Appraiser ";
		} else {
			return "Non-Appraiser";
		}
	}
	$look_up_username = $_POST['empNo'];
	$ldapusername = $_SESSION['USERNAME'];
	$ldappassword = $_SESSION['password'];
	$server = LDAP_SERVER_NAME;
	$ldap = ldap_connect($server);
	if ($ldap) {
		ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
		$bind = ldap_bind($ldap, $ldapusername, $ldappassword);
		$basedn = "OU=ASSR,DC=laassessor,DC=co,DC=la,DC=ca,DC=us";
		$filter = '(samaccountname='.$look_up_username.')';
		$attributes = array("displayname", "samaccountname", "mail", "manager","givenname",
							"telephoneNumber","department","title");
		$result = ldap_search($ldap, $basedn, $filter, $attributes);
		if (FALSE !== $result) {
			$info = ldap_get_entries($ldap, $result);
			$result  = Array();
			if($info["count"] > 0) {
				for ($i=0; $i<$info["count"]; $i++) {
					 $result["name"] = $info[$i]["displayname"][0];
					 $result["email"] = $info[$i]["mail"][0];
					 $result["manager"] = (string)$info[$i]["manager"][0];
					 $result["firstname"] = $info[$i]["givenname"][0];
					 $result["phone"] = $info[$i]["telephoneNumber"][0];	 // phone number
					 $result["department"] = $info[$i]["department"][0];	// pay location
					 $result["title"] = getTitleString((int)$info[$i]["title"][0]);// title

				}
				echo json_encode($result);
			}
			else {
				echo "error when try to connect LDAP";
			}
		}
	}

?>