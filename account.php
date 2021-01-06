<?php
	session_start();
	require_once "config.php";
	
	if (!$isLoggedIn)
	{
		$util->redirect("login.php");
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
		<title>Pirate Library - <?php echo htmlspecialchars($_COOKIE["username"]); ?></title>
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
			<h2><?php echo htmlspecialchars($_COOKIE["username"]); ?></h2>
			<p>
				<a href="logout.php">Logout</a>
			</p>
			<h3>Favorites</h3>
			<?php
				$userID = $_SESSION["user_id"];
				$query = "SELECT favorite_id FROM users_favorites WHERE user_id = ? ORDER BY RAND()";
				$userFavs = $dbHandle->runQuery($query, "i", array($userID));
				
				if ($userFavs->num_rows > 0)
				{
					$html = "<div class=\"browse-wrapper\">";
					
					while ($fav = $userFavs->fetch_assoc())
					{
						$favID = $fav["favorite_id"];
						$query = "SELECT type FROM items WHERE id = ?";
						$result = $dbHandle->runQuery($query, "i", array($favID));
						
						if ($result->num_rows > 0)
						{
							$itemType = $result[0]["type"];
							
							switch ($favoriteItemType)
							{
								case $MOVIE:
									$query = "SELECT title FROM movies WHERE id = ?";
									break;
								case $SHOW:
									$query = "SELECT title FROM shows WHERE id = ?";
									break;
								case $ALBUM:
									// coming soon!
									break;
								case $BOOK:
								default:
									// coming soon!
									break;
							}
							
							$result = $dbHandle->runQuery($query, "i", array($favID));
							
							if ($result->num_rows > 0)
							{
								$itemTitle = $result[0]["title"];
								$html .= "<div class=\"" . $itemType . "-item\">";
								$html .= "<a href=\"item.php?id=" . $favID . "\">";
								$html .= "<img src=\"img/poster/" . $favID . ".jpg\" alt=\"" . $itemTitle . "\" />";
								$html .= "<span>" . $itemTitle . "</span></a></div>";
							}
						}
					}
					
					$html .= "</div>";
					echo $html
				}
				else
				{
					echo "<p>Looks like you have no favorites!</p>";
				}
			?>
			<footer>
				<p>&copy; Rexroth Computing, 2019</p>
			</footer>
		</div>
	</body>
</html>