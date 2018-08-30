<?php
	include "db_connect.php";
	
	$id = $_POST["id"];
	$dataA = $email = null;
	
	$sql = $conn->prepare("SELECT EMAIL FROM USERS WHERE ID = ?");
	$sql->bind_param("i", $id);
	$sql->execute();
	$sql->bind_result($email);
	$sql->fetch();
	$sql->close();

	$dataA = array("EMAIL"=>$email);
	
	header("Content-Type: application/json");
	echo json_encode($dataA);
?>