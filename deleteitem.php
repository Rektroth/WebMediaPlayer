<?php
	session_start();
	require_once "config.php";
	
	if ($_SESSION["user_admin"])
	{
		$itemID = $_GET["id"];
		
		if ($_SERVER["REQUEST_METHOD"] == "POST")
		{
			$query = "DELETE FROM items WHERE id = ?";
			$dbHandle->runQuery($query, "i", array($itemID));
			$query = "DELETE FROM movies WHERE id = ?";
			$dbHandle->runQuery($query, "i", array($itemID));
			$query = "DELETE FROM shows WHERE id = ?";
			$dbHandle->runQuery($query, "i", array($itemID));
			$query = "DELETE FROM movies_genres WHERE id = ?";
			$dbHandle->runQuery($query, "i", array($itemID));
			$query = "DELETE FROM shows_genres WHERE id = ?";
			$dbHandle->runQuery($query, "i", array($itemID));
			$query = "DELETE FROM shows_episodes WHERE id = ?";
			$dbHandle->runQuery($query, "i", array($itemID));
			$query = "DELETE FROM users_favorites WHERE favoriteid = ?";
			$dbHandle->runQuery($query, "i", array($itemID));
			$util->redirect("movies.php");
		}
		else
		{
			$query = "SELECT title FROM movies WHERE id = ?";
			$result = $dbHandle->runQuery($query, "i", array($itemID));
			
			if ($result->num_rows > 0)
			{
				$item = $result[0];
				$title = $item["title"];
				$itemHTML = "<div class=\"movie-item\"><a href=\"item.php?id=" . $itemID . "\"><img src=\"img/poster/" . $itemID . ".jpg\" alt=\"" . $title . "\" /><span>" . $title . "</span></a></div>";
			}
			else
			{
				$util->redirect("404.php");
			}
		}
	}
	else
	{
		$util->redirect("500.php");
	}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8" />
		<meta name="robots" content="noindex" />
		<link rel="icon" href="img/icon.ico" />
		<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />
		<link rel="stylesheet" href="css/main.css" />
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		<script src="js/main.js"></script>
		<title>Pirate Library - Edit Item</title>
	</head>
	<body>
		<header>
			<div class="top-nav">
				<?php echo "<a href=\"account.php\">" . $_COOKIE["username"] . "</a>"; ?>
			</div>
			<ul>
				<li>
					<a href="movies.php">
						<span class="material-icons">movie</span>
					</a>
				</li>
				<li>
					<a href="shows.php">
						<span class="material-icons">tv</span>
					</a>
				</li>
				<li>
					<a href="albums.php">
						<span class="material-icons">music_note</span>
					</a>
				</li>
				<li>
					<a href="books.php">
						<span class="material-icons">book</span>
					</a>
				</li>
			</ul>
			<img class="logo" alt="PIRATE LIBRARY" src="img/logo.png" />
			<input type="text" id="searchInput" placeholder="Search for title..." title="Search for title" />
		</header>
		<div class="item-wrapper">
			<h2>Delete Item</h2>
			<p>Are you sure you want to delete this item?</p>
			<?php echo $itemHTML ?>
			<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?id=" . $itemID; ?>" method="post">
				<input type="submit" class="btn btn-primary btn-delete" value="Delete Item" />
			</form>
			<footer>
				<p>&copy; Rexroth Computing, 2019</p>
			</footer>
		</div>
	</body>
</html>