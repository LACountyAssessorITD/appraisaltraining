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

	function checkForActiveSession() {
		if (!userLoggedIn()) {
			header("Location: " . LOGIN_URL);
		}
	}

	function userLoggedIn() {
		return $_SESSION["logged_in"];
	}

	function checkRole() {
		return $_SESSION["ROLE"];
	}


?>
