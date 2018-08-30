<?php
	session_start();
	
	if(isset($_SESSION["email"])) {
		$msg = array("success" => "true");	
	} else {
		$msg = array("success" => "false");	
	}
	
	header("Content-Type: application/json");
	echo json_encode($msg);
?>