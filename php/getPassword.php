<?php
	include 'db_connect.php';
	
	session_start();
	
	$fail = false;
	
	if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["current"])) {
		$current = $_POST["current"];	
		
		//Check correct password.
		$sql = $conn->prepare("select PASSWORD from USERS where EMAIL=?");
		$sql->bind_param("s", $_SESSION["email"]);
		$sql->execute();
	   	$res = $sql->bind_result($hashPass);
		$sql->fetch();
		$correctPass = password_verify($current, $hashPass);		
		$sql->free_result();
		$sql->close();		
		array_push($report, $correctPass);		

	}
	
	//Return message to ajax as array encoded to json
	$msg = array("error" => $report);	


	header("Content-Type: application/json");
	echo json_encode($msg);
?>