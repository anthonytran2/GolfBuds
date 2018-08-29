<?php
	session_start();
	
	$data = $_POST["email"];
	$_SESSION["clickProfileEmail"] = $data;
?>