<?php
	include 'db_connect.php';
	
	session_start();
	$email = $_POST["user"];
	$imageLink = "";
	$msg = "";
	
	$sql0 = $conn->prepare("SELECT IMAGELINK FROM USERS WHERE EMAIL = ?");
	$sql0->bind_param('s', $email);
	$sql0->execute();
	$sql0->bind_result($imageLink);
	$sql0->fetch();
	$sql0->close();
	
	if($imageLink === null) {
		$msg = null;
	} else {
		$msg = $imageLink;
	}
	
	$dataA = array("MSG"=>$msg);
	header("Content-Type: application/json");
	echo json_encode($dataA);
?>