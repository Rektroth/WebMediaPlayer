<?php
	session_start();
	require_once "config.php";
	
	if ($isLoggedIn)
	{
		$userID = $_SESSION["user_id"];
		$query = "SELECT favorite_id FROM users_favorites WHERE user_id = ?";
		$result = $dbHandle->runQuery($query, "i", array($userID));
		$favoriteID = $_GET["id"];
		$favorited = false;
		
		if ($result->num_rows > 0)
		{
			$itemID = $result[0]["favorite_id"];
			
			if ($itemID == $favoriteID)
				{
					$favorited = true;
					break;
				}
			}
		}
		
		if ($_POST["action"] == "favorite")
		{
			if ($favorited)
			{
				$query = "DELETE FROM users_favorites WHERE user_id = ? AND favorite_id = ?";
			}
			else
			{
				$query = "INSERT INTO users_favorites (user_id, favorite_id) VALUES (?, ?)";
			}
			
			$dbHandle->runQuery($query, "ii", array($userID, $favoriteID));
		}
		else
		{
			$util->redirect("500.php");
		}
	}
	else
	{
		$util->redirect("500.php");
	}
?>