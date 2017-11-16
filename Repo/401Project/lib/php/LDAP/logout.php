<?php

include_once "../constants.php";
session_start();
$_SESSION["logged_in"] = FALSE;
header("Location: " . LOGIN_URL);

?>

