<?php
	include 'db_connect.php';
	
	session_start();
	
	$fail = false;
	
	if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["password"]) && isset($_POST["confirm"])) {
		$user_pass = $_POST["password"];
		$user_pass = password_hash($user_pass, PASSWORD_BCRYPT);
		$sql = $conn->prepare("UPDATE USERS SET PASSWORD = ? where EMAIL=?");
		$sql->bind_param("ss",  $user_pass, $_SESSION["email"]);
		$sql->execute();
		$sql->close();
	}
	
	//Return message to ajax as array encoded to json
	if($fail === true) 
		$msg = array("error" => $report);	
	else
		$msg = array("success" => "success");	

	header("Content-Type: application/json");
	echo json_encode($msg);
?>