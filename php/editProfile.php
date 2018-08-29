<?php
	include 'db_connect.php';
	
	session_start();
	
	//Get user reservations to display.
	$sql = $conn->prepare("UPDATE USERS SET SMOKING = ?, DRINKING = ?, GAMBLING = ?, MUSIC = ? where EMAIL=?");
    $sql->bind_param("iiiis", $_POST["smoking"], $_POST["drinking"], $_POST["gambling"], $_POST["music"], $_SESSION["email"]);
    $sql->execute();
	$sql->close();
?>