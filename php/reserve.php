<?php
	include 'db_connect.php';

	$course = $res_date = $res_time = "";
	$fail = false;

	if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["course"]) && isset($_POST["res_date"]) && isset($_POST["res_time"])) {

		//Get values and trim spaces, strip input of html and php tags, escape input
		$course = mysqli_real_escape_string($conn, strip_tags(trim($_POST["course"])));
		$res_date = $_POST["res_date"];//mysqli_real_escape_string($conn, strip_tags(trim($_POST["res_date"])));
		$res_time = $_POST["res_time"]; //mysqli_real_escape_string($conn, strip_tags(trim($_POST["res_time"])));
		$handicap = $_POST["handicap"];
		$age = $_POST["age"];
		$golfType = $_POST["golfType"];

	} else {
		$fail = true;
	}

	if(strcmp($course, "") === 0 || strcmp($res_date, "") === 0 || strcmp($res_time, "") === 0){
		$fail = true;
	}

	if($fail === false){
		session_start();
		$email = $_SESSION["email"];
		$smoking = $_SESSION["smoking"];
		$gambling = $_SESSION["gambling"];
		$drinking  = $_SESSION["drinking"];
		$music = $_SESSION["music"];

		$sql = $conn->prepare("INSERT INTO REQUEST(EMAIL, PARK_ID, SMOKING, GAMBLING,DRINKING,
					 MUSIC, HANDICAP, AGE, GOLF_TYPE, REQUESTDATE, REQUESTTIME) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$sql->bind_param("siiiiidiiss", $email, $course, $smoking, $gambling, $drinking, $music, $handicap, $age, $golfType, $res_date, $res_time);

		if(!($sql->execute())) echo "<br>" . $sql->error;

		$sql = $conn->prepare("UPDATE USER SET HANDICAP=?, AGE=?, GOLF_TYPE=? WHERE EMAIL=?");
		$sql->bind_param("diis", $handicap, $age, $golfType, $email);

		if(!($sql->execute())) echo "<br>" . $sql->error;
		//Clear contents from variable
		$sql->free_result();
		$sql->close();
	} else if($fail === true){
		echo "<br>" . "ERROR: CHECK INPUT";
	}

	mysqli_close($conn);

	header("Content-Type: application/json");
	echo json_encode("ok");
?>
