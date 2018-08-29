<?php
   include "db_connect.php";
   
   
	session_start();
	$email = $_SESSION["email"];
	
	//Get user reservations to display.
	$sql = $conn->prepare("DELETE from REQUEST where EMAIL=?");
    $sql->bind_param("s", $email);
    if(!$sql->execute()) {
		array_push($report, "Delete reservation error.");
	}
	array_push($report, $email);
    $sql->close();
	
	$col = null;
	$sql = $conn->prepare("select col
				from 
				(
				  select col, 
					case s.col
					  when 'UONE' then UONE
					  when 'UTWO' then UTWO
					  when 'UTHREE' then UTHREE
					  when 'UFOUR' then UFOUR
					end AS val
				  from groups
				  cross join
				  (
					select 'UONE' AS col union 
					select 'UTWO' union 
					select 'UTHREE' union 
					select 'UFOUR'
				  ) s
				) s
				where val = ?");
    $sql->bind_param("s", $email);
    $sql->execute();
	$sql->bind_result($col);
	$sql->fetch();
    $sql->close();
	
	if($col !== null) {
	
		$opengrp = null;
		$grpid = null;
		$size = null;
		$sql = $conn->prepare("SELECT GROUPID, OPENGROUP, SIZE FROM GROUPS WHERE $col = ?");
		$sql->bind_param("s", $email);
		$sql->execute();
		$sql->bind_result($grpid, $opengrp, $size);
		$sql->fetch();
		$sql->close();
		
		if($opengrp === 1) $opengrp--;

		$sql = $conn->prepare("UPDATE GROUPS SET $col = NULL, SIZE = SIZE - 1, OPENGROUP = ? WHERE GROUPID = ?");
		$sql->bind_param('ii', $opengrp, $grpid);
		$sql->execute();
		$sql->close();
		
		$sql = $conn->prepare("UPDATE USERS SET GROUP_ID = NULL WHERE EMAIL = ?");
		$sql->bind_param("s", $email);
		$sql->execute();
		$sql->close();
		
		$member = null;
		$shift = null;
		if($col === "UONE") {
			$sql = $conn->prepare("SELECT UTWO FROM GROUPS WHERE GROUPID = ?");
			$sql->bind_param("i", $grpid);
			$sql->execute();
			$sql->bind_result($member);
			$sql->fetch();
			$sql->close();
			
			if($member !== null) {
				$shift = "UONE";
			} else {
				$sql = $conn->prepare("DELETE FROM GROUPS WHERE GROUPID = ?");
				$sql->bind_param("i", $grpid);
				$sql->execute();
				$sql->close();	
			}
		} else if($col === "UTWO") {
			$sql = $conn->prepare("SELECT UTHREE FROM GROUPS WHERE GROUPID = ?");
			$sql->bind_param("i", $grpid);
			$sql->execute();
			$sql->bind_result($member);
			$sql->fetch();
			$sql->close();
			
			if($member !== null) $shift = "UTWO";
		} else if($col === "UTHREE"){
			$sql = $conn->prepare("SELECT UFOUR FROM GROUPS WHERE GROUPID = ?");
			$sql->bind_param("i", $grpid);
			$sql->execute();
			$sql->bind_result($member);
			$sql->fetch();
			$sql->close();
			
			if($member !== null) $shift = "UTHREE";
		} 
		
		if($shift === "UONE") {
			$sql = $conn->prepare("UPDATE GROUPS SET UONE = UTWO WHERE GROUPID = ?");
			$sql->bind_param("i", $grpid);
			$sql->execute();
			$sql->close();
			
			$sql = $conn->prepare("UPDATE GROUPS SET LEADER = UONE WHERE GROUPID = ?");
			$sql->bind_param("i", $grpid);
			$sql->execute();
			$sql->close();
			
			$sql = $conn->prepare("UPDATE GROUPS SET UTWO = UTHREE WHERE GROUPID = ?");
			$sql->bind_param("i", $grpid);
			$sql->execute();
			$sql->close();
			
			$sql = $conn->prepare("UPDATE GROUPS SET UTHREE = UFOUR WHERE GROUPID = ?");
			$sql->bind_param("i", $grpid);
			$sql->execute();
			$sql->close();
		} else if($shift === "UTWO") {
			$sql = $conn->prepare("UPDATE GROUPS SET UTWO = UTHREE WHERE GROUPID = ?");
			$sql->bind_param("i", $grpid);
			$sql->execute();
			$sql->close();
			
			$sql = $conn->prepare("UPDATE GROUPS SET UTHREE = UFOUR WHERE GROUPID = ?");
			$sql->bind_param("i", $grpid);
			$sql->execute();
			$sql->close();
		} else if($shift === "UTHREE") {
			$sql = $conn->prepare("UPDATE GROUPS SET UTHREE = UFOUR WHERE GROUPID = ?");
			$sql->bind_param("i", $grpid);
			$sql->execute();
			$sql->close();
		}
	}
	
	header("Content-Type: application/json");
	print json_encode($report);
?>