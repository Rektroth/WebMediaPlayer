<?php
	session_start();
	require_once "config.php";
	
	if ($_SESSION["user_admin"])
	{
		$itemID = $_GET["id"];
		$itemSeason = $_GET["s"];
		$itemEpisode = $_GET["e"];
		
		if ($_SERVER["REQUEST_METHOD"] == "POST")
		{
			$query = "DELETE FROM shows_episode WHERE id = ? AND season = ? AND episode = ?";
			$dbHandle->runQuery($query, "iii", array($itemID, $season, $episode);
			$util->redirect("item.php?id=" . $itemID);
		}
		else
		{
			$query = "SELECT title FROM shows_episodes WHERE id = ? AND season = ? AND episode = ?";
			$result = $dbHandle->runQuery($query, "iii", array($itemID, $itemSeason, $itemEpisode));
			
			if ($result->num_rows > 0)
			{
				$title = $result[0]["title"];
				$itemHTML = "<h1>" . $episode . ". " . $title . "</h1>";
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
				<li class="selected">
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
			<?php echo $itemHTML; ?>
			<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?id=" . $itemID . "&s=" . $itemSeason . "&e=" . $itemEpisode; ?>" method="post">
				<input type="submit" class="btn btn-primary btn-delete" value="Delete Item" />
			</form>
			<footer>
				<p>&copy; Rexroth Computing, 2019</p>
			</footer>
		</div>
	</body>
</html>