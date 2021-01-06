<?php
	require "dbcontroller.php";
	
	class Auth
	{
		function getUserByUsername($username)
		{
			$dbHandle = new DBController();
			$query = "SELECT * FROM users WHERE username = ?";
			$result = $dbHandle->runQuery($query, "s", array($username));
			return $result;
		}
		
		function getTokenByUsername($username, $expired)
		{
			$dbHandle = new DBController();
			$query = "SELECT * FROM token_auth WHERE username = ? AND expired = ?";
			$result = $dbHandle->runQuery($query, "si", array($username, $expired));
			return $result;
		}
		
		function markAsExpired($tokenId)
		{
			$dbHandle = new DBController();
			$query = "UPDATE token_auth SET expired = ? WHERE id = ?";
			$expired = 1;
			$result = $dbHandle->update($query, "ii", array($expired, $tokenId));
			return $result;
		}
		
		function insertToken($username, $randomPassword, $randomSelector, $expiryDate)
		{
			$dbHandle = new DBController();
			$query = "INSERT INTO token_auth (username, password, selector, expiry_date) VALUES (?, ?, ?, ?)";
			$result = $dbHandle->insert($query, "ssss", array($username, $randomPassword, $randomSelector, $expiryDate));
			return $result;
		}
		
		function update($query)
		{
			mysqli_query($this->conn, $query);
		}
	}
?>