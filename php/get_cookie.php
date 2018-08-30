<?php
	$msg = "";
	session_start();

	//Check if user log in was saved as cookie
	if(isset($_COOKIE["email"])){
		$msg = array("cookie" => $_COOKIE["email"]);
	}

	//Send msg back for html's js to handle.
	header("Content-Type: application/json");
	echo json_encode($msg);
?>
