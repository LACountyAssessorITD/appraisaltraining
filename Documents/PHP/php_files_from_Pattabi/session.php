<?php
include_once "constants.php";

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

	function getName() {
		return $_SESSION["USERNAME"];
	}
	
	function getAppName() {
		return APP_NAME;
	}
	function getUserName() {
		return $_SESSION["NAME"];
	}
?>