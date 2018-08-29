<?php
	//Get current session
	session_start();
	//unset session variables and destory session
	session_unset();
	session_destroy();
?>

<!-- Logout to home page -->
<script type="text/javascript">
         window.location.href = "../index.html";
</script>

