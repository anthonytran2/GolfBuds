<?php
	include 'db_connect.php';
	
	//Request user data from database
	$dataA = [];
	
	session_start();
	$email = $_SESSION["email"];
	
	//Get open groups
	$sql0 = $conn->prepare("SELECT COUNT(*) FROM GROUPS WHERE OPENGROUP=0");
	$sql0->execute();
	$openGroup = null;
	$sql0->bind_result($openGroup);
	$sql0->fetch();
	$sql0->close();
	//See if any groups exist
	$sql1 = $conn->prepare("SELECT COUNT(*) FROM GROUPS");
	$sql1->execute();
	$anyGroups = null;
	$sql1->bind_result($anyGroups);
	$sql1->fetch();
	$sql1->close();
	//
	$exists = $conn->prepare("SELECT COUNT(*) FROM GROUPS WHERE UONE = ? OR UTWO = ? OR UTHREE = ? OR UFOUR = ?");
	$exists->bind_param('ssss', $email, $email, $email, $email);
	$exists->execute();
	$existsCnt = null;
	$exists->bind_result($existsCnt);
	$exists->fetch();
	$exists->close();	
	
	//If no groups at all or no open groups then make a group
	if($anyGroups === 0 || $openGroup === 0) {
		//Get matched users based on same course, availability and score
		$sql2 = $conn->prepare("SELECT (u2.EMAIL) as matchEmail,
				+ (info.FNAME) as FNAME,
				+ (info.LNAME) as LNAME,
				+ (u1.SCORELVL) as SL,
				+ (u1.PARK_ID) as PRKID,
				+ (COUNT(*)) as COUNT
			FROM REQUEST u1 INNER JOIN REQUEST u2 ON u1.PARK_ID = u2.PARK_ID AND u2.AVAL = 0 AND u1.SCORELVL = u2.SCORELVL
			INNER JOIN USERS info ON u2.EMAIL = info.EMAIL
			WHERE u1.EMAIL = ? and u1.EMAIL <> u2.EMAIL");

		$sql2->bind_param('s', $email);
		$sql2->execute();	
		$sql2->bind_result($matchEmail, $fname, $lname, $sl, $prkid, $count);
		
		$numMatch = 0; //Number of users matched
		$max = 3; //Max users matched without the searcher
		$data = array_fill(0, 4, null);
		//Get matchable users
		while($sql2->fetch() && $numMatch < $max && $count > 0) {
			$data[$numMatch] = $matchEmail;
			$numMatch++;
		}
		$sql2->close();
		
		$disable = $conn->prepare("UPDATE REQUEST SET AVAL = 1 WHERE EMAIL = ?");
		$disable->bind_param('s', $email);
		$disable->execute();
		$disable->close();
		
		$u2 = null;
		$u3 = null;
		$u4 = null;
		$sl = null;
		$prkid = null;
		
		if($numMatch >= 1) {
			$disable1 = $conn->prepare("UPDATE REQUEST SET AVAL = 1 WHERE EMAIL = ?");
			$disable1->bind_param('s', $data[0]);
			$disable1->execute();
			$disable1->close();
			$u2 = $data[0];
		} 
		if($numMatch >= 2) {
			$disable2 = $conn->prepare("UPDATE REQUEST SET AVAL = 1 WHERE EMAIL = ?");
			$disable2->bind_param('s', $data[1]);
			$disable2->execute();
			$disable2->close();
			$u3 = $data[1];
		} 
		if($numMatch == 3) {	
			$disable3 = $conn->prepare("UPDATE REQUEST SET AVAL = 1 WHERE EMAIL = ?");
			$disable3->bind_param('s', $data[2]);
			$disable3->execute();
			$disable3->close();
			$u4 = $data[2];
		} 

		$size = $numMatch + 1; //Includes searcher
		//Set group open or closed if group is full
		if($size === 4) $open = 1;
		else $open = 0;
		
		if($count === 0) {
			$sql3 = $conn->prepare("SELECT SCORELVL, PARK_ID FROM REQUEST WHERE EMAIL = ?");
			$sql3->bind_param("s", $email);
			$sql3->execute();
			$sql3->bind_result($sl, $prkid);
			$sql3->fetch();
			$sql3->close();
			
			$sql3 = $conn->prepare("INSERT INTO GROUPS (LEADER, UONE, UTWO, UTHREE, UFOUR, OPENGROUP, SIZE, SCORELVL, COURSEID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
			$sql3->bind_param("sssssiiii", $email, $email, $u2, $u3, $u4, $open, $size, $sl, $prkid);
			$sql3->execute();
			$sql3->close();
		} else {
			$sql3 = $conn->prepare("INSERT INTO GROUPS (LEADER, UONE, UTWO, UTHREE, UFOUR, OPENGROUP, SIZE, SCORELVL, COURSEID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
			$sql3->bind_param("sssssiiii", $email, $email, $u2, $u3, $u4, $open, $size, $sl, $prkid);
			$sql3->execute();
			$sql3->close();
		}		
		
		$grpid = $conn->prepare("SELECT GROUPID FROM GROUPS WHERE LEADER = ?");
		$grpid->bind_param("s", $email);
		$grpid->execute();
		$grpid->bind_result($group_id);
		$grpid->fetch();
		$grpid->close();
		
		if($numMatch >= 0) {
			$update0 = $conn->prepare("UPDATE USERS SET GROUP_ID = ? WHERE EMAIL = ?");
			$update0->bind_param('is', $group_id, $email);
			$update0->execute();
			$update0->close();
		}	
		if($numMatch >= 1) {
			$update1 = $conn->prepare("UPDATE USERS SET GROUP_ID = ? WHERE EMAIL = ?");
			$update1->bind_param('is', $group_id, $data[0]);
			$update1->execute();
			$update1->close();
		}	
		if($numMatch >= 2) {
			$update2 = $conn->prepare("UPDATE USERS SET GROUP_ID = ? WHERE EMAIL = ?");
			$update2->bind_param('is', $group_id, $data[1]);
			$update2->execute();
			$update2->close();
		}
		if($numMatch == 3) {	
			$update3 = $conn->prepare("UPDATE USERS SET GROUP_ID = ? WHERE EMAIL = ?");
			$update3->bind_param('is', $group_id, $data[2]);
			$update3->execute();
			$update3->close();
		}
		
		$dataA = array("UONE"=>$email, "UTWO"=>$data[0], "UTHREE"=>$data[1], "UFOUR"=>$data[2]);
	} else if($openGroup > 0 && $existsCnt === 0){ //Groups are open
		//Look for open groups
		//within scorelevel  
		$sql4 = $conn->prepare("SELECT COUNT(*), GROUPID, SIZE FROM GROUPS grp INNER JOIN REQUEST rq ON rq.EMAIL = ? WHERE grp.SCORELVL = rq.SCORELVL AND grp.COURSEID = rq.PARK_ID AND OPENGROUP = 0");
		$sql4->bind_param('s', $email);
		$sql4->execute();
		$sql4->bind_result($cnt, $groupid, $size);
		$sql4->fetch();
		$sql4->close();
		
		if($cnt > 0) {
			if($size === 1) $userNum = 'UTWO';
			else if($size === 2) $userNum = 'UTHREE';
			else if($size === 3) $userNum = 'UFOUR';
			
			$size++;
			if($size == 4) $openGroup = 1;
			else $openGroup = 0;
			
			$sql5 = $conn->prepare("UPDATE GROUPS SET $userNum = ?, SIZE = ?, OPENGROUP = ? WHERE GROUPID = ?");
			$sql5->bind_param('siii', $email, $size, $openGroup, $groupid);
			$sql5->execute();
			$sql5->close();	
			
			$disable1 = $conn->prepare("UPDATE REQUEST SET AVAL = 1 WHERE EMAIL = ?");
			$disable1->bind_param('s', $email);
			$disable1->execute();
			$disable1->close();
			
			$update1 = $conn->prepare("UPDATE USERS SET GROUP_ID = ? WHERE EMAIL = ?");
			$update1->bind_param('is', $groupid, $email);
			$update1->execute();
			$update1->close();

			
			$u1 = null;
			$u2 = null;
			$u3 = null;
			$u4 = null;
			$getGroup = $conn->prepare("SELECT UONE, UTWO, UTHREE, UFOUR FROM GROUPS WHERE GROUPID = ?");
			$getGroup->bind_param('i', $groupid);
			$getGroup->execute();
			$getGroup->bind_result($u1, $u2, $u3, $u4);
			$getGroup->fetch();
			$getGroup->close();

			$dataA = array("UONE"=>$u1, "UTWO"=>$u2, "UTHREE"=>$u3, "UFOUR"=>$u4);
		} else {
			//Get matched users based on same course, availability and score
			$sql2 = $conn->prepare("SELECT (u2.EMAIL) as matchEmail,
					+ (info.FNAME) as FNAME,
					+ (info.LNAME) as LNAME,
					+ (u1.SCORELVL) as SL,
					+ (u1.PARK_ID) as PRKID, 
					+ (COUNT(*)) as COUNT
				FROM REQUEST u1 INNER JOIN REQUEST u2 ON u1.PARK_ID = u2.PARK_ID AND u2.AVAL = 0 AND u1.SCORELVL = u2.SCORELVL
				INNER JOIN USERS info ON u2.EMAIL = info.EMAIL
				WHERE u1.EMAIL = ? and u1.EMAIL <> u2.EMAIL");

			$sql2->bind_param('s', $email);
			$sql2->execute();	
			$sql2->bind_result($matchEmail, $fname, $lname, $sl, $prkid, $count);
			
			$numMatch = 0; //Number of users matched
			$max = 3; //Max users matched without the searcher
			$data = array_fill(0, 4, null);
			//Get matchable users
			while($sql2->fetch() && $numMatch < $max && $count > 0) {
				$data[$numMatch] = $matchEmail;
				$numMatch++;
			}
			$sql2->close();
			
			$disable = $conn->prepare("UPDATE REQUEST SET AVAL = 1 WHERE EMAIL = ?");
			$disable->bind_param('s', $email);
			$disable->execute();
			$disable->close();
			
			$u2 = null;
			$u3 = null;
			$u4 = null;
			$sl = null;
			$prkid = null;
			
			if($numMatch >= 1) {
				$disable1 = $conn->prepare("UPDATE REQUEST SET AVAL = 1 WHERE EMAIL = ?");
				$disable1->bind_param('s', $data[0]);
				$disable1->execute();
				$disable1->close();
				$u2 = $data[0];
			}	
			if($numMatch >= 2) {
				$disable2 = $conn->prepare("UPDATE REQUEST SET AVAL = 1 WHERE EMAIL = ?");
				$disable2->bind_param('s', $data[1]);
				$disable2->execute();
				$disable2->close();
				$u3 = $data[1];
			}
			if($numMatch == 3) {	
				$disable3 = $conn->prepare("UPDATE REQUEST SET AVAL = 1 WHERE EMAIL = ?");
				$disable3->bind_param('s', $data[2]);
				$disable3->execute();
				$disable3->close();
				$u4 = $data[2];
			}

			$size = $numMatch + 1; //Includes searcher
			//Set group open or closed if group is full
			if($size === 4) $open = 1;
			else $open = 0;
			
			if($count === 0) {
				$sql3 = $conn->prepare("SELECT SCORELVL, PARK_ID FROM REQUEST WHERE EMAIL = ?");
				$sql3->bind_param("s", $email);
				$sql3->execute();
				$sql3->bind_result($sl, $prkid);
				$sql3->fetch();
				$sql3->close();
				
				$sql3 = $conn->prepare("INSERT INTO GROUPS (LEADER, UONE, UTWO, UTHREE, UFOUR, OPENGROUP, SIZE, SCORELVL, COURSEID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
				$sql3->bind_param("sssssiiii", $email, $email, $u2, $u3, $u4, $open, $size, $sl, $prkid);
				$sql3->execute();
				$sql3->close();
			} else {
				$sql3 = $conn->prepare("INSERT INTO GROUPS (LEADER, UONE, UTWO, UTHREE, UFOUR, OPENGROUP, SIZE, SCORELVL, COURSEID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
				$sql3->bind_param("sssssiiii", $email, $email, $u2, $u3, $u4, $open, $size, $sl, $prkid);
				$sql3->execute();
				$sql3->close();
			}
			
			$grpid = $conn->prepare("SELECT GROUPID FROM GROUPS WHERE LEADER = ?");
			$grpid->bind_param("s", $email);
			$grpid->execute();
			$grpid->bind_result($group_id);
			$grpid->fetch();
			$grpid->close();
			
			if($numMatch >= 0) {
				$update0 = $conn->prepare("UPDATE USERS SET GROUP_ID = ? WHERE EMAIL = ?");
				$update0->bind_param('is', $group_id, $email);
				$update0->execute();
				$update0->close();
			}	
			if($numMatch >= 1) {
				$update1 = $conn->prepare("UPDATE USERS SET GROUP_ID = ? WHERE EMAIL = ?");
				$update1->bind_param('is', $group_id, $data[0]);
				$update1->execute();
				$update1->close();
			}	
			if($numMatch >= 2) {
				$update2 = $conn->prepare("UPDATE USERS SET GROUP_ID = ? WHERE EMAIL = ?");
				$update2->bind_param('is', $group_id, $data[1]);
				$update2->execute();
				$update2->close();
			}
			if($numMatch == 3) {	
				$update3 = $conn->prepare("UPDATE USERS SET GROUP_ID = ? WHERE EMAIL = ?");
				$update3->bind_param('is', $group_id, $data[2]);
				$update3->execute();
				$update3->close();
			}
			
			$dataA = array("UONE"=>$email, "UTWO"=>$data[0], "UTHREE"=>$data[1], "UFOUR"=>$data[2]);
		}			
	}

	header("Content-Type: application/json");
	echo json_encode($dataA);
?>