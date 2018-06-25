<?php
	include "db_connect.php";

	$first_name = $last_name = $user_email;
	$fail = false; //Error reporting

	//Check if input is from post method and check if null.
	if(($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["firstname"]) && isset($_POST["lastname"]) 
			 && isset($_POST["em"]) && isset($_POST["age"]) && isset($_POST["handicap"]) && isset($_POST["question1"])
			 && isset($_POST["question2"]) && isset($_POST["question3"]) && isset($_POST["question4"])&& isset($_POST["course"]) 
			 && isset($_POST["reservationDate"]) && isset($_POST["time"]) && isset($_POST["golfType"]))
			 && (strcmp($_POST["firstname"], "") !== 0 && strcmp($_POST["lastname"], "") !== 0 
			 && strcmp($_POST["em"], "") !== 0 && strcmp($_POST["age"], "") !== 0 && strcmp($_POST[""], "handicap") !== 0 
			 && strcmp($_POST["question1"], "") !== 0 && strcmp($_POST["question2"], "") !== 0 && strcmp($_POST["question3"], "") !== 0 
			 && strcmp($_POST["question4"], "") !== 0 && strcmp($_POST["course"], "") !== 0 && strcmp($_POST["reservationDate"], "") !== 0
			 && strcmp($_POST["time"], "") !== 0 && strcmp($_POST["golfType"], "") !== 0)){
		
		//Get values and trim spaces, strip input of html and php tags, escape input 	
		$first_name = mysqli_real_escape_string($conn , strip_tags(trim($_POST["firstname"])));
		$last_name = mysqli_real_escape_string($conn, strip_tags(trim($_POST["lastname"])));
		//Also sanitize illegal inputs for email
		$user_email = mysqli_real_escape_string($conn, filter_var(strip_tags(trim($_POST["em"])), FILTER_SANITIZE_EMAIL));	
		$age = $_POST["age"];
		$handicap = $_POST["handicap"];
		$q1 = $_POST["question1"];
		$q2 = $_POST["question2"];
		$q3 = $_POST["question3"];
		$q4 = $_POST["question4"];
		$course = $_POST["course"];
		$requestdate = $_POST["reservationDate"];
		$requesttime = $_POST["time"];
		$golfType = $_POST["golfType"];
	} else {
		$fail = true; 
		array_push($report, "All Fields Must Be Filled.");
	}

	if($fail === false) {
		//check if string contains any spaces in the actually trimmed string
		//Exclude passwords
		if(strpos($first_name, " ") !== false || strpos($last_name, " ") !== false || strpos($user_email, " ") !== false ) { 
			$fail = true; 
			array_push($report, "First Name, Last Name And Email Can Not Contain Spaces.");
		}

		//Only letters in names
		if(preg_match("/^[A-Za-z]+$/", $first_name) === 0 && preg_match("/^[A-Za-z]+$/", $last_name) === 0 ) { 
			$fail = true;
			array_push($report, "First And Last Name Must Only Contain Letters.");
		}

		//Validate email format
		if(!filter_var($user_email, FILTER_VALIDATE_EMAIL)) { 
			$fail = true; 
			array_push($report, "Invalid Email Format");
		}

		//If no errors then try sql else echo error
		if($fail === false){
			//Prepare statements
			$sql = $conn->prepare("INSERT INTO USERS(FNAME, LNAME, EMAIL, SMOKING, GAMBLING, DRINKING, MUSIC, HANDICAP, AGE, GOLF_TYPE) VALUES(?, ?, ?, ? ,? ,? ,? ,? ,? ,?)");
			$sql->bind_param("sssiiiidii", $first_name, $last_name, $user_email, $q1, $q2, $q3, $q4, $handicap, $age, $golfType);
			if(!($sql->execute())) {
				$fail = true;
				if(strpos($sql->error, "user_email"))
					array_push($report, "Email Already Exists.");
			} else {
				$sql = $conn->prepare("INSERT INTO REQUEST (EMAIL, PARK_ID, SMOKING, GAMBLING, DRINKING, MUSIC, HANDICAP, AGE, GOLF_TYPE, REQUESTTIME, REQUESTDATE) VALUES(?, ?, ?, ?, ?, ?, ? ,? ,? ,? ,?)");
				$sql->bind_param("siiiiidiiss", $user_email, $course, $q1, $q2, $q3, $q4, $handicap, $age, $golfType, $requesttime, $requestdate);
				 
				if(!($sql->execute())){ 
					$fail = true;
					array_push($report, "Request a group failed.");
				}
				//session_start();
				//$_SESSION["EMAIL"] = $user_email;
			}
			
			//Clear contents from variable
			$sql->free_result();
			$sql->close();
		} 
	}


	if($fail == true){
		$msg = array("error" => $report);
	} else {
		$msg = array("success" => "success");
	}
	header("Content-Type: application/json");
	echo json_encode($msg);

?>

