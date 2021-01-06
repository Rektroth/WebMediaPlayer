<?php
	session_start();
	require_once "config.php";
	
	$itemID = $_GET["id"];
	$query = "SELECT * FROM items WHERE id = ?";
	$result = $dbHandle->runQuery($query, "i", array($itemID));
	
	if ($result->num_rows > 0)
	{
		$itemType = $result[0]["type"];
		
		switch ($itemType)
		{
			case $MOVIE:
				$query = "SELECT * FROM movies WHERE id = ?";
				break;
			case $SHOW:
				$query = "SELECT * FROM shows WHERE id = ?";
				break;
			case $ALBUM:
				// coming soon
				break;
			case $BOOK:
			default:
				// coming soon
				break;
		}
		
		$result = $dbHandle->runQuery($query, "i", array($itemID));
		
		if ($result->num_rows > 0)
		{
			$item = $result[0];
			
			if ($type == $SHOW)
			{
				$release = $result[0]["premiere_year"] . "-" . $result[0]["finale_year"];
			}
			else
			{
				$release = $result[0]["release_date"];
			}
		}
		else
		{
			$util->redirect("404.php");
		}
	}
	else
	{
		$util->redirect("404.php");
	}
	
	switch ($itemType)
	{
		case $MOVIE:
			$query = "SELECT genre FROM movies_genres WHERE id = ?";
			break;
		case $SHOW:
			$query = "SELECT title, season, episode, runtime, view_id FROM shows_episodes WHERE id = ? ORDER BY season, episode";
			$result = $dbHandle->runQuery($query, "i", array($itemID));
			
			if ($result->num_rows > 0)
			{
				$season = -1;
				
				while ($episodeItem = $result->fetch_assoc())
				{
					if ($episodeItem["season"] != $season)
					{
						if ($season == -1)
						{
							$episodeList = "<ul class=\"season-list\" id=\"season-" . $episodeItem["season"] . "-list\" style=\"visibility: visible; height: auto;\">";
						}
						else
						{
							$episodeList .= "</ul><ul class=\"season-list\" id=\"season-" . $episodeItem["season"] . "-list\">";
						}
						
						$season = $episodeItem["season"];
						$seasonList .= "<a href=\"#\" class=\"view-season-button\" id=\"view-season_" . $season . "\">Season " . $season . "</a>";
					}
					
					$episodeList .= "<li><a href=\"#\" class=\"watch-episode\" id=\"" . $episodeItem["view_id"] . "\">" . $episodeItem["episode"] . ". " . $episodeItem["title"] . "<span>" . $episodeItem["runtime"] . "m</span></a>";
					
					if ($_SESSION["user_admin"])
					{
						$episodeList .= "<span class=\"edit-episode\"><a href=\"editepisode.php?id=" . $itemID . "&s=" . $episodeItem["season"] . "&e=" . $episodeItem["episode"] . "\" class=\"material-icons\">edit</a></span>";
					}
					
					$episodeList .= "</li>";
				}
				
				if ($_SESSION["user_admin"])
				{
					$episodeListWrapper .= "<a href=\"addepisode.php?id=" . $itemID . "\" class=\"add-episode-button\">+</a>";
				}
			}
			
			$query = "SELECT genre FROM shows_genres WHERE id = ?";
			break;
		case $ALBUM:
			// coming soon
			break;
		case $BOOK:
		default:
			// coming soon
			break;
	}
	
	$result = $dbHandle->runQuery($query, "i", array($itemID));
	
	if ($result->num_rows > 0)
	{
		$genreItem = $result->fetch_assoc();
		$genres = $genreItem["genre"];
		
		while ($genreItem = $result->fetch_assoc())
		{
			$genres .= ", " . $genreItem["genre"];
		}
	}
	else
	{
		$genres = "404";
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
		<title>Pirate Library - <?php echo $row["title"]; ?></title>
	</head>
	<body>
		<header>
			<div class="top-nav">
				<?php echo $isLoggedIn ? "<a href=\"account.php\">" . $_COOKIE["username"] . "</a>" : "<a href=\"login.php\">Login</a>"; ?>
			</div>
			<ul>
				<li class="<?php if ($type == $MOVIE) echo "selected"; ?>">
					<a href="movies.php">
						<span class="material-icons">movie</span>
					</a>
				</li>
				<li class="<?php if ($type == $SHOW) echo "selected"; ?>">
					<a href="shows.php">
						<span class="material-icons">tv</span>
					</a>
				</li>
				<li class="<?php if ($type == $ALBUM) echo "selected"; ?>">
					<a href="albums.php">
						<span class="material-icons">music_note</span>
					</a>
				</li>
				<li class="<?php if ($type == $BOOK) echo "selected"; ?>">
					<a href="books.php">
						<span class="material-icons">book</span>
					</a>
				</li>
			</ul>
			<img class="logo" alt="PIRATE LIBRARY" src="img/logo.png" />
			<input type="text" id="searchInput" placeholder="Search for title..." title="Search for title" />
		</header>
		<div class="item-wrapper">
			<?php echo "<img class=\"poster\" alt=\"poster\" src=\"img/poster/" . $itemID . ".jpg\" />"; ?>
			<div class="meta-wrapper">
				<h1><?php
					echo $title;
					
					if($isLoggedIn)
					{
						if ($_COOKIE["user_admin"])
						{
							switch ($itemType)
							{
								case $MOVIE:
									echo " <a href=\"editmovie.php?id=" . $itemID . "\"><span class=\"material-icons\">edit</span></a>";
									break;
								case $SHOW:
									echo " <a href=\"editshow.php?id=" . $itemID . "\"><span class=\"material-icons\">edit</span></a>";
									break;
								case $ALBUM:
									// coming soon
									break;
								case $BOOK:
								default:
									// coming soon
									break;
							}
						}
					}
				?></h1>
				<p><?php echo $item["description"]; ?></p>
				<p>
					<span class="meta"><span class="material-icons">supervisor_account</span><?php echo $item["rating"]; ?></span>
					<span class="meta"><span class="material-icons">timer</span><?php echo $item["runtime"] . "m"; ?></span>
					<span class="meta"><span class="material-icons">category</span><?php echo $genres; ?></span>
					<span class="meta"><span class="material-icons">calendar_today</span><?php echo $release; ?></span>
				</p><?php
					if ($isLoggedIn)
					{
						$userID = $_SESSION["user_id"];
						$query = "SELECT favorite_id FROM users_favorites WHERE user_id = ?";
						$result = $dbHandle->runQuery($query, "i", array($userID));
						$favorited = false;
						
						if ($result->num_rows > 0)
						{
							while ($favItem = $result->fetch_assoc())
							{
								if ($favItem["favorite_id"] == $itemID)
								{
									$favorited = true;
									break;
								}
							}
						}
						
						if ($favorited)
						{
							echo "<p id=\"" . $itemID . "\" class=\"meta\"><a href=\"#\" id=\"unfavorite-button\"><span class=\"material-icons\">favorite</span> Favorited</a></p>";
						}
						else
						{
							echo "<p id=\"" . $itemID . "\" class=\"meta\"><a href=\"#\" id=\"favorite-button\"><span class=\"material-icons\">favorite_border</span> Add to Favorites</a></p>";
						}
					}
				?>
			</div>
			<?php if ($type == $MOVIE) echo "<a href=\"#\" id=\"watch\"><span class=\"material-icons\">play_circle_outline</span> Play Movie</a>"; ?>
			<?php if ($type == $SHOW) echo "<div class=\"episode-wrapper\">" . $episodeList . "</ul></div>"; ?>
			<footer>
				<p>&copy; Rexroth Computing, 2019</p>
			</footer>
		</div>
		<div id="player-wrapper">
			<a href="#">Close</a>
			<iframe id="player" src="https://drive.google.com/file/d/<?php echo $viewID; ?>/preview" width="853" height="480" allowfullscreen></iframe>
		</div>
	</body>
</html>