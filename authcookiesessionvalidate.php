<?php 
	require_once "auth.php";
	require_once "util.php";

	$auth = new Auth();
	$dbHandle = new DBController();
	$util = new Util();
	$currentTime = time();
	$currentDate = date("Y-m-d H:i:s", $currentTime);
	$cookieExpirationTime = $currentTime + (30 * 24 * 60 * 60);
	$isLoggedIn = false;
	$isAdmin = false;
	
	if (!empty($_SESSION["user_id"]))
	{
		$isLoggedIn = true;
		
		if ($_SESSION["user_admin"])
		{
			$isAdmin = true;
		}
	}
	else if (!empty($_COOKIE["username"]) && !empty($_COOKIE["random_password"]) && !empty($_COOKIE["random_selector"]))
	{
		$isPasswordVerified = false;
		$isSelectorVerified = false;
		$isExpiryDateVerified = false;
		$userToken = $auth->getTokenByUsername($_COOKIE["username"], 0);
		
		if (password_verify($_COOKIE["random_password"], $userToken[0]["password"]))
		{
			$isPasswordVerified = true;
		}
		
		if (password_verify($_COOKIE["random_selector"], $userToken[0]["selector"]))
		{
			$isSelectorVerified = true;
		}
		
		if ($userToken[0]["expiry_date"] >= $currentDate)
		{
			$isExpiryDareVerified = true;
		}
		
		if (!empty($userToken[0]["id"]) && $isPasswordVerified && $isSelectorVerified && $isExpiryDareVerified)
		{
			$isLoggedIn = true;
			$query = "SELECT id, admin FROM users WHERE username = ?";
			$user = $dbHandle->runQuery($query, "s", $_COOKIE["username"]);
			$_SESSION["user_id"] = $user[0]["id"];
			
			if ($user[0]["admin"] === 1)
			{
				$_SESSION["user_admin"] = true;
			}
		}
		else
		{
			if (!empty($userToken[0]["id"]))
			{
				$auth->markAsExpired($userToken[0]["id"]);
			}
			
			$util->clearAuthCookie();
		}
	}
?>