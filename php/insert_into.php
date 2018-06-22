<?php
include "db_connect.php";
include "secstuff.php";

$first_name = $last_name = $user_email = $user_pass = $user_pass_confirm = "";
$fail = false;
$report = [];

//Check if input is from post method and check if null.
if(($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["firstname"]) && isset($_POST["lastname"]) 
		 && isset($_POST["em"]) && isset($_POST["passW"]) && isset($_POST["passWConfirm"]) 
		 && isset($_POST["question1"]) && isset($_POST["question2"]) && isset($_POST["question3"]) && isset($_POST["question4"])) 
		 && (strcmp($_POST["firstname"], "") !== 0 && strcmp($_POST["lastname"], "") !== 0 && strcmp($_POST["em"], "") !== 0 
		 && strcmp($_POST["passW"], "") !== 0  && strcmp($_POST["passWConfirm"], "") !== 0 
		 && strcmp($_POST["question1"], "") !== 0 && strcmp($_POST["question2"], "") !== 0 
		 && strcmp($_POST["question3"], "") !== 0 && strcmp($_POST["question4"], "") !== 0) )  {
	
	//Get values and trim spaces, strip input of html and php tags, escape input 	
	$first_name = mysqli_real_escape_string($conn , strip_tags(trim($_POST["firstname"])));
	$last_name = mysqli_real_escape_string($conn, strip_tags(trim($_POST["lastname"])));
	//Also sanitize illegal inputs for email
	$user_email = mysqli_real_escape_string($conn, filter_var(strip_tags(trim($_POST["em"])), FILTER_SANITIZE_EMAIL));
	$user_pass = mysqli_real_escape_string($conn, strip_tags(trim($_POST["passW"])));
	$user_pass_confirm = mysqli_real_escape_string($conn, strip_tags(trim($_POST["passWConfirm"])));
	$q1 = $_POST["question1"];
	$q2= $_POST["question2"];
	$q3 = $_POST["question3"];
	$q4 = $_POST["question4"];
	
} else {
	array_push($report, "All Fields Must Be Filled.");
	$fail = true;
}

if($fail === false) {

	//check if string contains any spaces in the actually trimmed string
	//Exclude passwords
	if(strpos($first_name, " ") !== false || strpos($last_name, " ") !== false 
			|| strpos($user_email, " ") !== false ) { 

		array_push($report,"First Name, Last Name And Email Can Not Contain Spaces.");
		$fail = true; 
	}
	//Only letters in names. Username can contain numbers
	if((preg_match("/^[A-Za-z]+$/", $first_name) === 0) || (preg_match("/^[A-Za-z]+$/", $last_name) === 0)) { 
		array_push($report, "First Name And Last Name Can Only Contain Letters.");
		$fail = true; 
	} 
	//Validate email format
	if(!filter_var($user_email, FILTER_VALIDATE_EMAIL)) { 
		array_push($report, "Invalid Email Format.");
		$fail = true; 
	}
	//Check to see if password and password confirm matches
	if(strcmp($user_pass, $user_pass_confirm) !== 0) { 
		array_push($report, "Passwords Do Not Match.");
		$fail = true; 
	}

	//If no errors then try sql else echo error
	if($fail === false){
		//Encrypt
		$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length("$user_pass"));
		$user_pass = openssl_encrypt($user_pass, "aes-256-cbc", $key, $options=0, $iv);

		//Prepare statements
	    $sql = $conn->prepare("INSERT INTO USERS(FNAME, LNAME, EMAIL, PASSWORD, SMOKING, GAMBLING, DRINKING, MUSIC) VALUES(?, ?, ?, ?, ?, ?, ?, ?)");
		$sql->bind_param("ssssiiii", $first_name, $last_name, $user_email, $user_pass, $q1, $q2, $q3, $q4);
		if(!($sql->execute())) {
			$fail = true;
			if(strpos($sql->error, "user_email"))  
				array_push($report, "Email Already Exists.");
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

