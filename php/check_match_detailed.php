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

		//Get group users.
		$sql = $conn->prepare("SELECT UONE, UTWO, UTHREE, UFOUR FROM groups WHERE GROUPID=?");
		$sql->bind_param('i', $groups_id);
		$sql->execute();
		$sql->bind_result($UONE, $UTWO, $UTHREE, $UFOUR);
		$sql->fetch();
		$sql->close();
		
		//Get each users details.
		$FNAME1 = $LNAME1 = $ID1 = $FNAME2 = $LNAME2 = $ID2 = $FNAME3 = $LNAME3 = $ID3 = $FNAME4 = $LNAME4 = $ID4 = null;
		$SIZE = 0;
		if($UONE != null) {
			$sql = $conn->prepare("SELECT FNAME, LNAME, ID FROM USERS WHERE EMAIL=?");
			$sql->bind_param('s', $UONE);
			$sql->execute();
			$sql->bind_result($FNAME1, $LNAME1, $ID1);
			$sql->fetch();
			$sql->close();
			$SIZE++;
		}
		if($UTWO != null) {
			$sql = $conn->prepare("SELECT FNAME, LNAME, ID FROM USERS WHERE EMAIL=?");
			$sql->bind_param('s', $UTWO);
			$sql->execute();
			$sql->bind_result($FNAME2, $LNAME2, $ID2);
			$sql->fetch();
			$sql->close();
			$SIZE++;
	    }
		if($UTHREE != null)	{	
			$sql = $conn->prepare("SELECT FNAME, LNAME, ID FROM USERS WHERE EMAIL=?");
			$sql->bind_param('s', $UTHREE);
			$sql->execute();
			$sql->bind_result($FNAME3, $LNAME3, $ID3);
			$sql->fetch();
			$sql->close();
			$SIZE++;
		}
		if($UFOUR != null){		
			$sql = $conn->prepare("SELECT FNAME, LNAME, ID FROM USERS WHERE EMAIL=?");
			$sql->bind_param('s', $UFOUR);
			$sql->execute();
			$sql->bind_result($FNAME4, $LNAME4, $ID4);
			$sql->fetch();
			$sql->close();
			$SIZE++;
		}
		
		//Return assoc array of assoc arrays.
		$emails = array("UONE"=>$UONE, "UTWO"=>$UTWO, "UTHREE"=>$UTHREE, "UFOUR"=>$UFOUR);
		$names = array("NAME1"=>$FNAME1." ".$LNAME1, "NAME2"=>$FNAME2." ".$LNAME2, "NAME3"=>$FNAME3." ".$LNAME3, "NAME4"=>$FNAME4." ".$LNAME4);
		$ids = array("ID1"=>$ID1, "ID2"=>$ID2, "ID3"=>$ID3, "ID4"=>$ID4);
		$sz = array("SIZE"=>$SIZE);
		$msg = array("EMAILS"=>$emails, "NAMES"=>$names, "IDS"=>$ids, "SZ"=>$sz);
	}	
	header("Content-Type: application/json");
	echo json_encode($msg);
?>