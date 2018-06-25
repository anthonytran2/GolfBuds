<?php
	include "db_connect.php";

	session_start();
	
	//Get user reservations to display.
	$sql = $conn->prepare("select PARK_ID, REQUESTTIME, REQUESTDATE from REQUEST where EMAIL=?");
    $sql->bind_param("s", $_SESSION["email"]);
    $sql->execute();
	$res = $sql->bind_result($park, $time, $date);
	
	//Send as array of associated arrays
	$rows = array();
	while($sql->fetch()) {
		$rows[] = array("PARK_ID"=>$park, "REQUESTTIME"=>$time, "REQUESTDATE"=>$date);
    }
	$sql->free_result();
    $sql->close();
	header("Content-Type: application/json");
	print json_encode($rows);
?>