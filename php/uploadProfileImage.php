<?php
	include 'db_connect.php';
	
	session_start();
	
	$email = $_SESSION["email"];
	$dataA = [];
	$msg = "Uploaded";
	$error = 0;
	$imageFileType = "";
	$id = null;
	$res = false;

	$sql0 = $conn->prepare("SELECT ID FROM USERS WHERE EMAIL = ?");
	$sql0->bind_param('s', $email);
	$sql0->execute();
	$sql0->bind_result($id);
	$sql0->fetch();
	$sql0->close();
	
	if(isset($_FILES["file"]["type"])) {
		$sourcePath = $_FILES['file']['tmp_name'];	   // Storing source path of the file in a variable
		$targetPath = "../userImages/".$_FILES['file']['name']; // Target path where file is to be stored
		$imageFileType = strtolower(pathinfo($targetPath,PATHINFO_EXTENSION));
		$targetPath = "../userImages/".$id.".".$imageFileType;
		$size = $_FILES['file']['size'];

		//Limit size 8000kb.
		if ($size > 8000000) {
			$msg = "8000kb size limit.";
			$error = 1;
		} 
		//Empty.
		if ($size == 0 && $error == 0) {
			$msg = "No file selected.";
			$error = 1;
		} 
		// Allow certain file formats.
		if($imageFileType != "jpg" && $imageFileType != "jpeg" && $error == 0) {
			$msg = "Sorry, only JPG & JPEG files are allowed.";
			$error = 1;
		} 
		
		$dimW = 260;
		$dimH = 260;
		if($error == 0) {
			list($origW, $origH) = getimagesize($sourcePath);
			$img = imagecreatefromjpeg($sourcePath);
			$new = imagecreatetruecolor($dimW, $dimH);			
			imagecopyresized($new, $img, 0, 0, 0, 0, $dimW, $dimH, $origW, $origH);
			if($imageFileType == "jpg" || $imageFileType == "jpeg") {
				$res = imagejpeg($new, $targetPath, 100);
			}
			
			if($res === true){
				$sql0 = $conn->prepare("UPDATE USERS SET IMAGELINK = ? WHERE EMAIL = ?");
				$sql0->bind_param('ss', $targetPath, $email);
				$sql0->execute();
				$sql0->close();	
			} else {
				$msg = "Image conversion failed.";
				$error = 1;
			}
		}
	} else {
		$msg = "Upload Error.";
	}
//$msg = $error;
	if($error == 0) {
		$dataA = array("MSG"=>$msg, "LINK"=>$targetPath, "ERROR"=>$error);
	} else {
		$dataA = array("MSG"=>$msg, "LINK"=>"", "ERROR"=>$error);
	}
	header("Content-Type: application/json");
	echo json_encode($dataA);
?>