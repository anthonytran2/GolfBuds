
<?php

	/*
						$to = "team14csc190@gmail.com";
						$subject = "My subject";
						$txt = "Hello world!";
						$headers = "From: sample@example.com" . "\r\n" .
									"CC: nishaantsingh@csus.edu";

						mail($to,$subject,$txt,$headers);
					*/
ini_set('max_execution_time', 300); //300 seconds = 5 minutes
include 'db_connect.php';
session_start();

//$_SESSION["EMAIL"] = "team14csc190@gmail.com";//This is where the result of a successful login should go
//$_SESSION["PARK_ID"] = "1";
//  $_SESSION["REQUESTTIME"] = '04:54:00';//This is where the time input goes
//	$_SESSION["REQUESTDATE"] = date("Y-m-d",strtotime('2017/11/17'));

$_SESSION["EMAIL"] = $_SESSION["email"];
$_SESSION["ID"] = $_SESSION["id"];
$query = mysqli_query($conn,"SELECT * FROM REQUEST WHERE EMAIL = '{$_SESSION['EMAIL']}'");
date_default_timezone_set("America/Los_Angeles");
$RegDate = date("Y-m-d");//This is where the date input goes
$_SESSION["Date"] = date("Y-m-d",strtotime('+0 days',strtotime($RegDate))); 


while($row = mysqli_fetch_array($query))
	{
//		$_SESSION["FNAME"] = $row['FNAME'];
//		$_SESSION["LNAME"] = $row['LNAME'];
//		$_SESSION["PASSWORD"] = $row['PASSWORD'];

		$_SESSION["PARK_ID"] = $row['PARK_ID'];
		$_SESSION["SMOKING"] = $row['SMOKING'];
		$_SESSION["GAMBLING"] = $row['GAMBLING'];
		$_SESSION["DRINKING"] = $row['DRINKING'];
		$_SESSION["MUSIC"] = $row['MUSIC'];
		$_SESSION["HANDICAP"] = $row['HANDICAP'];
		$_SESSION["AGE"] = $row['AGE'];
		$_SESSION["GOLF_TYPE"] = $row['GOLF_TYPE'];
		$_SESSION["REQUESTTIME"] = $row['REQUESTTIME'];
		$_SESSION["REQUESTDATE"] = $row['REQUESTDATE'];
	}
?>

 <?php
 /*
	$query = mysqli_query($conn,"INSERT INTO request(EMAIL,PARK_ID,SMOKING,
																GAMBLING,DRINKING,MUSIC,HANDICAP,AGE,GOLF_TYPE,REQUESTTIME,REQUESTDATE) 
																	VALUES('{$_SESSION['EMAIL']}','{$_SESSION['PARK_ID']}','{$_SESSION['SMOKING']}','{$_SESSION['GAMBLING']}',
														'{$_SESSION['DRINKING']}','{$_SESSION['MUSIC']}','{$_SESSION['HANDICAP']}', '{$_SESSION['AGE']}', '{$_SESSION['GOLF_TYPE']}','{$_SESSION['REQUESTTIME']}','{$_SESSION['REQUESTDATE']}')");
															
												
	$result = mysqli_query($conn,"SELECT * FROM request");
	$num_rows = mysqli_num_rows($result);
*/
	?>

<?php
		$groupReq = mysqli_query($conn,"SELECT*FROM PAIRING");
		$num_rows = mysqli_num_rows($groupReq);
		
		if($num_rows == 0){
				
				$makeGroup = mysqli_query($conn, "INSERT INTO PAIRING(USER_ID1, TIME, PARK_ID,DATE)
																VALUES('{$_SESSION['ID']}','{$_SESSION['REQUESTTIME']}','{$_SESSION['PARK_ID']}','{$_SESSION['REQUESTDATE']}')");
				$_SESSION["grid"] = mysqli_insert_id($conn);
				
				$del = mysqli_query($conn, "DELETE FROM REQUEST WHERE EMAIL = '{$_SESSION['EMAIL']}' AND PARK_ID = '{$_SESSION['PARK_ID']}' AND REQUESTTIME = '{$_SESSION['REQUESTTIME']}'");
				sleep(30);
		
				$group = mysqli_query($conn,"SELECT*FROM PAIRING WHERE GROUP_ID = '{$_SESSION['grid']}'");
				$num = mysqli_num_rows($group);
			
				
				if($num == 0){
					
						session_close();
						exit(1);
				}else{
					
					$groupCheck = mysqli_query($conn,"SELECT USER_ID4 FROM PAIRING WHERE GROUP_ID = '{$_SESSION['grid']}'");
					$row =mysqli_fetch_array($groupCheck);
				
					if($row[0] == 0){
						
						$groupEmail = mysqli_query($conn,"SELECT USER_ID1, USER_ID2, USER_ID3, USER_ID4 FROM PAIRING WHERE GROUP_ID = '{$_SESSION['grid']}' ");
						$rowEmail = mysqli_fetch_array($groupEmail);
						$user1Email = mysqli_query($conn, "SELECT EMAIL FROM USER WHERE ID = '{$rowEmail[0]}'");
						$user1Row = mysqli_fetch_array($user1Email);
						$user2Email;
						$user2Row;
						$user3Email;
						$user3Row;
						$user4Email;
						$user4Row;
						if($rowEmail[1] != 0){
							$user2Email = mysqli_query($conn, "SELECT EMAIL FROM USER WHERE ID = '{$rowEmail[1]}'");
							$user2Row = mysqli_fetch_array($user2Email);
						}
						if($rowEmail[2] != 0){
							$user3Email = mysqli_query($conn, "SELECT EMAIL FROM USER WHERE ID = '{$rowEmail[2]}'");
							$user3Row = mysqli_fetch_array($user3Email);
		
						}
						if($rowEmail[3] != 0){
							$user4Email = mysqli_query($conn, "SELECT EMAIL FROM USER WHERE ID = '{$rowEmail[3]}'");
							$user4Row = mysqli_fetch_array($user4Email);
							
						}
						
						$subject = "1st"; $text = "1st join"; $headers = "From: team14csc190@gmail.com" . "\r\n";
						//Email first user using this command
						mail($user1Row[0],$subject,$text,$headers);
						
						//For user 2,3,4 use following style of code
						if(isset($user2Row)){
							
							
							$subject = "2nd"; $text = "2nd join"; $headers = "From: team14csc190@gmail.com" . "\r\n";
							mail($user2Row[0],$subject,$text,$headers);
						}
						if(isset($user3Row)){
						
							$subject = "3rdd"; $text = "3rd join"; $headers = "From: team14csc190@gmail.com" . "\r\n";
							mail($user3Row[0],$subject,$text,$headers);
						}
						if(isset($user4Row)){
						
							$subject = "4th"; $text = "4th join"; $headers = "From: team14csc190@gmail.com" . "\r\n";
							mail($user4Row[0],$subject,$text,$headers);
						}
						
					
						$groupDel = mysqli_query($conn,"DELETE FROM PAIRING WHERE GROUP_ID = '{$_SESSION['grid']}'");
						mysqli_close($conn);
						exit(1);
					}else{
						
						$groupEmail = mysqli_query($conn,"SELECT USER_ID1, USER_ID2, USER_ID3, USER_ID4 FROM PAIRING WHERE GROUP_ID = '{$_SESSION['grid']}' ");
						$rowEmail = mysqli_fetch_array($groupEmail);
						$user1Email = mysqli_query($conn, "SELECT EMAIL FROM USER WHERE ID = '{$rowEmail[0]}'");
						$user1Row = mysqli_fetch_array($user1Email);
						$user2Email = mysqli_query($conn, "SELECT EMAIL FROM USER WHERE ID = '{$rowEmail[1]}'");
						$user2Row = mysqli_fetch_array($user2Email);
						$user3Email = mysqli_query($conn, "SELECT EMAIL FROM USER WHERE ID = '{$rowEmail[2]}'");
						$user3Row = mysqli_fetch_array($user3Email);
						$user4Email = mysqli_query($conn, "SELECT EMAIL FROM USER WHERE ID = '{$rowEmail[3]}'");
						$user4Row = mysqli_fetch_array($user4Email);
						$groupDel = mysqli_query($conn,"DELETE FROM PAIRING WHERE GROUP_ID = '{$_SESSION['grid']}'");
						mysqli_close($conn);
						exit(1);
					}
				}
				
		}else{
			
			$groupCheck2 = mysqli_query($conn,"SELECT * FROM PAIRING");
			$num2 = mysqli_num_rows($groupCheck2);
			while($row = mysqli_fetch_array($groupCheck2)){
				
				$_SESSION["baseID"] = $row['USER_ID1'];
				
				$userTraits = mysqli_query($conn, "SELECT * FROM USER WHERE ID = '{$_SESSION['baseID']}'");
				$traits = mysqli_fetch_array($userTraits);
				
				
				if($traits[5] != $_SESSION["SMOKING"]){
					
					continue;
					
				
				}else if($traits[6] != $_SESSION["GAMBLING"]){

					continue;
					
					
				}else if($traits[7] != $_SESSION["DRINKING"]){

					continue;
					
				}else if($traits[8] != $_SESSION["MUSIC"]){

					continue;
					
				}else if(abs($traits[9] - $_SESSION["HANDICAP"])> 5){

					continue;
			
			
				}else if(abs($traits[10] - $_SESSION["AGE"])>10){

					continue;
					
					
				}else if($traits[11] != $_SESSION["GOLF_TYPE"]){
					
					
					continue;
				}else{
					
					
					
					$_SESSION["groupID"] = $row['GROUP_ID'];
					$posCheck = mysqli_query($conn,"SELECT USER_ID2, USER_ID3, USER_ID4 FROM PAIRING WHERE GROUP_ID = '{$_SESSION['groupID']}'");
					$rowA =mysqli_fetch_array($posCheck);
					if($rowA[2] != 0){
							
								continue;
						
					}else if($rowA[0] == 0){
						
						$addUser = mysqli_query($conn, "UPDATE PAIRING SET USER_ID2 = '{$_SESSION['ID']}' WHERE GROUP_ID = '{$_SESSION['groupID']}'");
						$delTHISGUY = mysqli_query($conn, "DELETE FROM REQUEST WHERE EMAIL = '{$_SESSION['EMAIL']}' AND PARK_ID = '{$_SESSION['PARK_ID']}' AND REQUESTTIME = '{$_SESSION['REQUESTTIME']}'");
					
					
						mysqli_close($conn);
						exit(1);
						

					
					}else if($rowA[1] == 0){
						$addUser = mysqli_query($conn, "UPDATE PAIRING SET USER_ID3 = '{$_SESSION['ID']}' WHERE GROUP_ID = '{$_SESSION['groupID']}'");
						$delTHISGUY = mysqli_query($conn, "DELETE FROM REQUEST WHERE EMAIL = '{$_SESSION['EMAIL']}' AND PARK_ID = '{$_SESSION['PARK_ID']}' AND REQUESTTIME = '{$_SESSION['REQUESTTIME']}'");
						
						
						mysqli_close($conn);
						exit(1);
					}else{
						
						$addUser = mysqli_query($conn, "UPDATE PAIRING SET USER_ID4 = '{$_SESSION['ID']}' WHERE GROUP_ID = '{$_SESSION['groupID']}'");
						$delTHISGUY = mysqli_query($conn, "DELETE FROM REQUEST WHERE EMAIL = '{$_SESSION['EMAIL']}' AND PARK_ID = '{$_SESSION['PARK_ID']}' AND REQUESTTIME = '{$_SESSION['REQUESTTIME']}'");
					
						
						mysqli_close($conn);
						exit(1);
					}
						
				}
				
				
			}
			
				$makeGroup = mysqli_query($conn, "INSERT INTO PAIRING(USER_ID1, TIME, PARK_ID,DATE)
																VALUES('{$_SESSION['ID']}','{$_SESSION['REQUESTTIME']}','{$_SESSION['PARK_ID']}','{$_SESSION['REQUESTDATE']}')");
				$_SESSION["grid2"] = mysqli_insert_id($conn);
				
				$del = mysqli_query($conn, "DELETE FROM REQUEST WHERE EMAIL = '{$_SESSION['EMAIL']}' AND PARK_ID = '{$_SESSION['PARK_ID']}' AND REQUESTTIME = '{$_SESSION['REQUESTTIME']}'");
				sleep(30);
		
				$group = mysqli_query($conn,"SELECT*FROM PAIRING WHERE GROUP_ID = '{$_SESSION['grid2']}'");
				$num = mysqli_num_rows($group);
			
				
				if($num == 0){

					
						exit(1);
				}else{
					
					$groupCheck = mysqli_query($conn,"SELECT USER_ID4 FROM PAIRING WHERE GROUP_ID = '{$_SESSION['grid2']}'");
					$row =mysqli_fetch_array($groupCheck);
				
					if($row[0] == 0){
						
						$groupEmail = mysqli_query($conn,"SELECT USER_ID1, USER_ID2, USER_ID3, USER_ID4 FROM PAIRING WHERE GROUP_ID = '{$_SESSION['grid2']}' ");
						$rowEmail = mysqli_fetch_array($groupEmail);
						$user1Email = mysqli_query($conn, "SELECT EMAIL FROM USER WHERE ID = '{$rowEmail[0]}'");
						$user1Row = mysqli_fetch_array($user1Email);
						$user2Email;
						$user2Row;
						$user3Email;
						$user3Row;
						$user4Email;
						$user4Row;
						if($rowEmail[1] != 0){
							$user2Email = mysqli_query($conn, "SELECT EMAIL FROM USER WHERE ID = '{$rowEmail[1]}'");
							$user2Row = mysqli_fetch_array($user2Email);
						}
						if($rowEmail[2] != 0){
							$user3Email = mysqli_query($conn, "SELECT EMAIL FROM USER WHERE ID = '{$rowEmail[2]}'");
						$user3Row = mysqli_fetch_array($user3Email);
		
						}
						if($rowEmail[3] != 0){
							$user4Email = mysqli_query($conn, "SELECT EMAIL FROM USER WHERE ID = '{$rowEmail[3]}'");
							$user4Row = mysqli_fetch_array($user4Email);
							
						}
					
					
						//Email first user using this command
						$subject = "1st 2"; $text = "1 join 2"; $headers = "From: team14csc190@gmail.com" . "\r\n";
						mail($user1Row[0],$subject,$text,$headers);
						
						//For user 2,3,4 use following style of code
						if(isset($user2Row)){
							
							$subject = "2nd "; $text = "2 join 2"; $headers = "From: team14csc190@gmail.com" . "\r\n";
							mail($user2Row[0],$subject,$text,$headers);
						}
						if(isset($user3Row)){
							
							$subject = "3rd"; $text = "3 join 2"; $headers = "From: team14csc190@gmail.com" . "\r\n";
							mail($user3Row[0],$subject,$text,$headers);
						}
						if(isset($user4Row)){
							
							$subject = "4th"; $text = "4th join 2"; $headers = "From: team14csc190@gmail.com" . "\r\n";
							mail($user4Row[0],$subject,$text,$headers);
						}
						
					
						$groupDel = mysqli_query($conn,"DELETE FROM PAIRING WHERE GROUP_ID = '{$_SESSION['grid2']}'");
						mysqli_close($conn);
						exit(1);
					}else{
						
						$groupEmail = mysqli_query($conn,"SELECT USER_ID1, USER_ID2, USER_ID3, USER_ID4 FROM PAIRING WHERE GROUP_ID = '{$_SESSION['grid2']}' ");
						$rowEmail = mysqli_fetch_array($groupEmail);
						$user1Email = mysqli_query($conn, "SELECT EMAIL FROM USER WHERE ID = '{$rowEmail[0]}'");
						$user1Row = mysqli_fetch_array($user1Email);
						$user2Email = mysqli_query($conn, "SELECT EMAIL FROM USER WHERE ID = '{$rowEmail[1]}'");
						$user2Row = mysqli_fetch_array($user2Email);
						$user3Email = mysqli_query($conn, "SELECT EMAIL FROM USER WHERE ID = '{$rowEmail[2]}'");
						$user3Row = mysqli_fetch_array($user3Email);
						$user4Email = mysqli_query($conn, "SELECT EMAIL FROM USER WHERE ID = '{$rowEmail[3]}'");
						$user4Row = mysqli_fetch_array($user4Email);
				
						$groupDel = mysqli_query($conn,"DELETE FROM PAIRING WHERE GROUP_ID = '{$_SESSION['grid2']}'");
						//Email for group found
						$subject = "Found"; $text = "Group found"; $headers = "From: team14csc190@gmail.com" . "\r\n";
						mail($user1Row[0],$subject,$text,$headers); //Repeat for users 2 ,3 ,4
						mail($user1Row[1],$subject,$text,$headers); //Repeat for users 2 ,3 ,4
						mail($user1Row[2],$subject,$text,$headers); //Repeat for users 2 ,3 ,4
						mail($user1Row[3],$subject,$text,$headers); //Repeat for users 2 ,3 ,4

						mysqli_close($conn);
						exit(1);
					}
				}
			
			
		}
		
?>
