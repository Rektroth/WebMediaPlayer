<?php
	session_start();
	require "util.php";
	
	$util = new Util();
	$_SESSION["member_id"] = "";
	session_destroy();
	$util->clearAuthCookie();
	$util->redirect("login.php");
?>