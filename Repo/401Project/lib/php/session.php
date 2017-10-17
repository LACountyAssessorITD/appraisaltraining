<?php
	session_start();
	include_once "constants.php";

	$_SESSION["id"] = 6995;

	function getEmployeeID() {
		return $_SESSION['EMPLOYEEID'];
	}

	function getUserName() {
		return $_SESSION["username"];
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
