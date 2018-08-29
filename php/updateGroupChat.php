<?php
	include "db_connect.php";
	
	session_start();
	
	$email = $_SESSION["email"];
	$chatUrl = $_POST["chatUrl"];
	
	$grpid = $conn->prepare("SELECT GROUPID FROM GROUPS WHERE LEADER = ?");
	$grpid->bind_param("s", $email);
	$grpid->execute();
	$grpid->bind_result($groupId);
	$grpid->fetch();
	$grpid->close();
	
	$sql = $conn->prepare("UPDATE GROUPS SET CHATURL = ? WHERE GROUPID = ?");
	$sql->bind_param("si", $chatUrl, $groupId);
	$sql->execute();
	$sql->close();	
	
?>