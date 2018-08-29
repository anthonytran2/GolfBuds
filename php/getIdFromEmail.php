<?php
    include "db_connect.php";
	
	$email = $_POST["email"];
	$dataA = $id = null;
	
	$sql = $conn->prepare("SELECT ID FROM USERS WHERE EMAIL = ?");
	$sql->bind_param("s", $email);
	$sql->execute();
	$sql->bind_result($id);
	$sql->fetch();
	$sql->close();

	$dataA = array("ID"=>$id);
	
	header("Content-Type: application/json");
	echo json_encode($dataA);
?>