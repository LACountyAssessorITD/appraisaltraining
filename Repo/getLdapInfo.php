<?php
include_once "../constants.php";
include_once "authenticate.php";
session_start();

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
							"telephoneNumber","department","title");
		$result = ldap_search($ldap, $basedn, $filter, $attributes);

		if (FALSE !== $result) {
			$info = ldap_get_entries($ldap, $result);
			$inforesult  = array();
			if($info["count"] > 0) {
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
					$inforesult[] =$info[$i]["title"][0];// title

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