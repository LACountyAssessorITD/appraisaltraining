<?php
	error_reporting(0);
	session_start();
	include_once "constants.php";

	$_SESSION["CERTNO"] = 6466;
	$_SESSION["EMPLOYEEID"] = 6466;

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


	function redirect_onAdminPage() {
		if ($_SESSION["logged_in"] == true) {
			if ($_SESSION["ROLE"] == 1) {
				//header("Location: " . ADMIN_HOME_PAGE_URL);
			} else if ($_SESSION["ROLE"] == 0) {
				header("Location: " . USER_HOME_PAGE_URL);
			} else {
				$_SESSION["logged_in"] = false;
				header("Location: " . ERROR_URL);
			}
		} else {
			header("Location: " . LOGIN_URL);
		}
	}
	function redirect_onUserPage() {
		if ($_SESSION["logged_in"] == true) {
			if ($_SESSION["ROLE"] == 1) {
				header("Location: " . ADMIN_HOME_PAGE_URL);
			} else if ($_SESSION["ROLE"] == 0) {
				// header("Location: " . USER_HOME_PAGE_URL);
			} else {
				$_SESSION["logged_in"] = false;
				header("Location: " . ERROR_URL);
			}
		} else {
			header("Location: " . LOGIN_URL);
		}
	}
	session_write_close();

?>
