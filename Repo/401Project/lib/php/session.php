<?php
	include_once "constants.php";

	$_SESSION["CERTNO"] = 6995;

	function getCertNo() {
		return $_SESSION['CERTNO'];
	}
	function getEmployeeID() {
		return $_SESSION['EMPLOYEEID'];
	}

	function getName() {
		return $_SESSION["displayname"];
	}

	function ifUserLoggedIn() {
		return $_SESSION["logged_in"];
	}

	function checkRole() {
		return $_SESSION["ROLE"];
	}

	function ifAppraiser() {
		if (getCertNo() != 0) 
			return true;
		else return false;
	}


	function redirect() {
		if (ifUserLoggedIn() == true) {
			if (checkRole() == 1) {
				header("Location: " . ADMIN_HOME_PAGE_URL);
			} else if (checkRole() == 0) {
				header("Location: " . USER_HOME_PAGE_URL);
			} else {
				$_SESSION["logged_in"] = false;
				header("Location: " . ERROR_URL);
			}

		} else {
			header("Location: " . LOGIN_URL);
		}
	}


?>
