<?php
	include "db_connect.php";
	
	session_start();
	
	$email = $_POST["email"];
	
	$dataA = $id = $fname = $lname = $smoking = $gambling = $drinking = $music = $handicap = $age = $golf_type = null;
	
	$sql = $conn->prepare("SELECT FNAME, LNAME, ID, SMOKING, GAMBLING, DRINKING, MUSIC, HANDICAP, AGE, GOLF_TYPE FROM USERS WHERE EMAIL = ?");
	$sql->bind_param("s", $email);
	$sql->execute();
	$sql->bind_result($fname, $lname, $id, $smoking, $gambling, $drinking, $music, $handicap, $age, $golf_type);
	$sql->fetch();
	$sql->close();

	$dataA = array("FNAME"=>$fname, "LNAME"=>$lname, "ID"=>$id, "EMAIL"=>$email ,"SMOKING"=>$smoking, "GAMBLING"=>$gambling, "DRINKING"=>$drinking, "MUSIC"=>$music, "HANDICAP"=>$handicap, "AGE"=>$age, "GOLF_TYPE"=>$golf_type);
	
	header("Content-Type: application/json");
	echo json_encode($dataA);
?>