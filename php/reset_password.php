<?php
	include "db_connect.php";

	$fail = false;
	$now = time();
	$expireIndi = false;

	session_start(); 

	//Check if session exist
	if(isset($_SESSION["email"]) && isset($_SESSION["expire"])){
		$email = $_SESSION["email"];
		$expire = $_SESSION["expire"];
		
		//Check if session expired.
		if($now > $expire) {
			$fail = true;
			$expireIndi = true;
		}
	} else {
		$fail = true;
		$expireIndi = true;
	}

	//Check if input correctly set
	if( ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["passW"]) && isset($_POST["confirmPassW"]) )
		&& (strcmp($_POST["passW"], "") !== 0 && strcmp($_POST["confirmPassW"], "") !== 0) ) {
	
		$password = mysqli_real_escape_string($conn, strip_tags(trim($_POST["passW"])));
		$password_confirm = mysqli_real_escape_string($conn, strip_tags(trim($_POST["confirmPassW"])));
	} else {
		$fail = true;
		array_push($report, "All Fields Are Required.");
	}
	
	//If all fields filled then check inputs
	if($fail === false) {
		//Check to see if password and password confirm matches
		if(strcmp($password, $password_confirm) !== 0) {
			array_push($report, "Passwords Do Not Match.");
			$fail = true;
		}
		if($fail === false) {
			//Encrypt
			$password = password_hash($password, PASSWORD_BCRYPT);
			//Prepare statments for security
			$sql = $conn->prepare("select * from USER where EMAIL=?");
			$sql->bind_param("s", $email);
			$sql->execute();
			$res = $sql->get_result();
			$sql->close();

			//Login is correct then change password
			if($res->num_rows === 1) {
				$sql = $conn->prepare("UPDATE USER SET PASSWORD=? WHERE EMAIL=?");
				$sql->bind_param("ss", $password, $email);
				$sql->execute();

				//Clear results from variable
				$sql->free_result();
				$sql->close();
			} else {
				$fail = true;
				array_push($report, "Email Not Found.");
		   }
		}
	}


	//Return message to ajax as array encoded to json
	if($fail === true) {
		//If expired then destory session
		if($expireIndi === true) {
			$msg = array("expire" => "true");
			//Expire session
			//unset session variables and destory session
			session_unset();
			session_destroy();

		} else {  	
			$msg = array("error" => $report);
		}
	} else {
		$msg = array("success" => "success");
		//Expire session
		//unset session variables and destory session
		session_unset();
		session_destroy();
	}
	header("Content-Type: application/json");
	echo json_encode($msg);

?>
