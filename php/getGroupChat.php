<?php
	include 'db_connect.php';
	
	session_start();
	
	$email = $_SESSION["email"];
	$groupId = null;
	$chatUrl = null;
	$msg = null;
	
	//Get group id from email
	$grpid = $conn->prepare("SELECT GROUP_ID FROM USERS WHERE EMAIL = ?");
	$grpid->bind_param('s', $email);
	$grpid->execute();
	$grpid->bind_result($groupId);
	$grpid->fetch();
	$grpid->close();
	
	//Get chaturl from users group
	$sql = $conn->prepare("SELECT CHATURL FROM GROUPS WHERE GROUPID = ?");
	$sql->bind_param('i', $groupId);
	$sql->execute();
	$sql->bind_result($chatUrl);
	$sql->fetch();
	$sql->close();
			
	$msg = array("CHATURL"=>$chatUrl);
			
	header("Content-Type: application/json");
	echo json_encode($msg);
?>