<?php
	include 'db_connect.php';
	
	session_start();
	$email = $_SESSION["email"];
	$groups_id = null;
	
	//Get open groups
	$sql = $conn->prepare("SELECT GROUP_ID FROM USERS WHERE EMAIL=?");
	$sql->bind_param('s', $email);
	$sql->execute();
	$sql->bind_result($groups_id);
	$sql->fetch();
	$sql->close();
	
	if($groups_id === null) {
		$msg = array("success" => "false");
	} else {
		$UONE = null;
		$UTWO = null;
		$UTHREE = null;
		$UFOUR = null;

		$sql = $conn->prepare("SELECT UONE, UTWO, UTHREE, UFOUR FROM GROUPS WHERE GROUPID=?");
		$sql->bind_param('i', $groups_id);
		$sql->execute();
		$sql->bind_result($UONE, $UTWO, $UTHREE, $UFOUR);
		$sql->fetch();
		$sql->close();
		
		$msg = array("UONE"=>$UONE, "UTWO"=>$UTWO, "UTHREE"=>$UTHREE, "UFOUR"=>$UFOUR);
	}	
	header("Content-Type: application/json");
	echo json_encode($msg);
?>