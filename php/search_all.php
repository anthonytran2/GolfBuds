<?php

$sql = "SELECT fName, lName, userName, email, pwd, userID FROM TABLENAME";
$result = $conn->query($sql);

if($result->num_rows > 0){
	while($row = $result->fetch_assoc()){
		echo " Fname: " . $row["fName"]. "Lname: " .$row["lName"]. "Uname: " .$row["userName"]. "Email: " .$row["email"]. "Pass: " .$row["pwd"]. "UserID: " .$row["userID"]. "<br>";
	}
}else{
	echo "0 results";	
}


?>