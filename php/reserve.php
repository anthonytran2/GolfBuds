<?php
	include 'db_connect.php';

	$course = $res_date = $res_time = "";
	$fail = false;

	if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["course"]) && isset($_POST["handicap"]) && isset($_POST["age"]) && isset($_POST["golfType"]) ) {

		//Get values and trim spaces, strip input of html and php tags, escape input
		$course = mysqli_real_escape_string($conn, strip_tags(trim($_POST["course"])));
		$handicap = $_POST["handicap"];
		$age = $_POST["age"];
		$golfType = $_POST["golfType"];
	} else {
		$fail = true;
		array_push($report, "All Fields Required.");
	}

	if($fail === false && (strcmp($course, "") === 0)){
		$fail = true;
		array_push($report, "All Fields Required.");
	} 
	
	if($fail === false) {
		session_start();
		$email = $_SESSION["email"];
		$sql = $conn->prepare("SELECT COUNT(*) FROM REQUEST WHERE EMAIL=?");
		$sql->bind_param("s", $email);
		
		$sql->execute();
		$cnt = 0;
		$sql->bind_result($cnt);
		$sql->fetch();

		if($cnt > 0) {
			$fail = true;
			array_push($report, "Only one reservation is allowed at a time.");
		}	
		
		$sql->free_result();
		$sql->close();
		
		if($fail === false) {
			$email = $_SESSION["email"];
			$smoking = $_SESSION["smoking"];
			$gambling = $_SESSION["gambling"];
			$drinking  = $_SESSION["drinking"];
			$music = $_SESSION["music"];
			$aval = 0;
			
			$scorelvl = (50*$smoking/2) + (10*$gambling/2) + (10*$drinking/2) + (10*$music/2) + (10*($golfType/2)) + (50*(round($handicap,-1,PHP_ROUND_HALF_UP)/10)) + (10*$age);
			if($scorelvl > 0 && $scorelvl <= 60) $scorelvl = 1;
			else if($scorelvl > 60 && $scorelvl <= 120) $scorelvl = 1;
			else if($scorelvl > 120 && $scorelvl <= 180) $scorelvl = 2;
			else if($scorelvl > 180 && $scorelvl <= 240) $scorelvl = 2;
			else if($scorelvl > 240 && $scorelvl <= 300) $scorelvl = 3;
			else if($scorelvl > 300 && $scorelvl <= 360) $scorelvl = 3;

			$sql = $conn->prepare("INSERT INTO REQUEST(EMAIL, PARK_ID, SMOKING, GAMBLING, DRINKING, MUSIC, HANDICAP, AGE, GOLF_TYPE, AVAL, SCORELVL) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
			$sql->bind_param('siiiiidiiii', $email, $course, $smoking, $gambling, $drinking, $music, $handicap, $age, $golfType, $aval, $scorelvl);
			if(!$sql->execute()) {
				$fail = true;
			    array_push($report, "sql error");
			}
            $sql->close();
			
			$sql1 = $conn->prepare("UPDATE USERS SET HANDICAP=?, AGE=?, GOLF_TYPE=? WHERE EMAIL=?");
			$sql1->bind_param('diis', $handicap, $age, $golfType, $email);

			$sql1->execute();
			//Clear contents from variable
			$sql1->close();
		}
	}

	mysqli_close($conn);

	if($fail === true) 
		$msg = array("error" => $report);	
	else
		$msg = array("success" => "success");	

	header("Content-Type: application/json");
	echo json_encode($msg);
?>
