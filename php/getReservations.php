<?php
	include "db_connect.php";

	session_start();
	
	//Get user reservations to display.
	$sql = $conn->prepare("select PARK_ID from REQUEST where EMAIL=?");
    $sql->bind_param("s", $_SESSION["email"]);
    $sql->execute();
	$res = $sql->bind_result($park);
	
	//Send as array of associated arrays
	$rows = array();
	while($sql->fetch()) {
		$rows[] = array("PARK_ID"=>$park);
    }
	$sql->free_result();
    $sql->close();
	header("Content-Type: application/json");
	print json_encode($rows);
?>