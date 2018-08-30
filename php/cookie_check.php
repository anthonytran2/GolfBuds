<?php
	session_start();
	
	$msg = "";
	//Check if user log in was saved as cookie
	if(isset($_COOKIE["email"])){
		$msg = array("set" => "true");
	} else {
		$msg = array("set" => "false");
	}

	//Send msg back for html's js to handle.
	header("Content-Type: application/json");
	echo json_encode($msg);
?>
