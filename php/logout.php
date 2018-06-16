<?php
	//Get current session
	session_start();
	//unset session variables and destory session
	session_unset();
	session_destroy();
	//Destory cookie
	unset($_COOKIE["online"]);
	unset($_COOKIE["fname"]);
	unset($_COOKIE["lname"]);
	setcookie("online", null, time()-3600);
	setcookie("fname", null, time()-3600);
	setcookie("lname", null, time()-3600);
?>

<!-- Logout to home page -->
<script type="text/javascript">
         window.location.href = "../index.html";
</script>

