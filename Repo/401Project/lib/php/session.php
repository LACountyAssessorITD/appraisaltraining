<?php
	session_start();

	$_SESSION["id"] = 6995;

	function getUserID() {
		return $_SESSION["id"];
	}

	function getUserName() {
		return $_SESSION["NAME"];
	}
?>
