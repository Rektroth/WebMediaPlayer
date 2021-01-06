<?php
	session_start();
	require_once "config.php";
	
	if ($isAdmin)
	{
		$itemID = $_GET["id"];
		$query = "SELECT type FROM items WHERE id = ?");
		$itemType = $dbHandle->runQuery($query, "s", array($itemID))[0]["type"];
		
		if ($itemType == $SHOW)
		{
			$query = "SELECT title FROM shows WHERE id = ?";
			$showTitle = $dbHandle->runQuery($query, "i", array($itemID))[0]["title"];
			
			if ($_SERVER["REQUEST_METHOD"] == "POST")
			{
				$titleErr = $seasonErr = $episodeErr = $runtimeErr = $viewIDErr = "";
				
				if (empty(trim($_POST["title"])))
				{
					$titleErr = "Please enter title.";
				}
				else
				{
					$title = trim($_POST["title"]);
				}
				
				if (empty(trim($_POST["season"])))
				{
					$seasonErr = "Please enter season.";
				}
				else
				{
					$season = trim($_POST["season"]);
				}
				
				if (empty(trim($_POST["episode"])))
				{
					$episodeErr = "Please enter episode.";
				}
				else
				{
					$episode = trim($_POST["episode"]);
				}
				
				if (empty($seasonErr) && empty($episodeErr))
				{
					$query = "SELECT * FROM shows_episodes WHERE id = ? AND season = ? AND episode = ?";
					$episodeTemp = $dbHandle->runQuery($query, "iii", array($itemID, $season, $episode));
					
					if ($result->num_rows > 0)
					{
						$seasonErr = $episodeErr = "Item with identified season and episode already exists.";
					}
				}
				
				if (empty(trim($_POST["runtime"])))
				{
					$runtimeErr = "Please enter runtime.";
				}
				else
				{
					$runtime = trim($_POST["runtime"]);
				}
				
				if (empty(trim($_POST["view_id"])))
				{
					$viewIDErr = "Please enter view ID.";
				}
				else
				{
					$viewID = trim($_POST["view_id"]);
				}
				
				if (empty($titleErr) && empty($seasonErr) && empty($episodeErr) && empty($runtimeErr) && empty($viewIDErr))
				{
					$query = "INSERT INTO shows_episodes (id, title, season, episode, runtime, view_id) VALUES (?, ?, ?, ?, ?, ?)";
					$success = $dbHandle->runQuery($query, "isiiis", array($itemID, $title, $season, $episode, $runtime, $viewID));
					
					if ($success)
					{
						$util->redirect("item.php?id=" . $itemID);
					}
					else
					{
						$util->redirect("500.php");
					}
				}
			}
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
		<title>Pirate Library - Add Episode</title>
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
			<h2>Add Episode of <?php echo $show_title; ?></h2>
			<p>Enter the info for the episode you are adding.</p>
			<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?id=" . $itemID; ?>" method="post" enctype="multipart/form-data">
				<div class="form-group <?php echo (!empty($titleErr)) ? "has-error" : ""; ?>">
					<label>Title</label>
					<input type="text" name="title" class="form-control" size="255" value="<?php echo $title; ?>" />
					<span class="help-block"><?php echo $titleErr; ?></span>
				</div>
				<div class="form-group <?php echo (!empty($seasonErr)) ? "has-error" : ""; ?>">
					<label>Season</label>
					<input type="number" name="season" class="form-control" min="1" max="999" value="<?php echo $season; ?>" />
					<span class="help-block"><?php echo $seasonErr; ?></span>
				</div>
				<div class="form-group <?php echo (!empty($episode_err)) ? "has-error" : ""; ?>">
					<label>Episode</label>
					<input type="number" name="episode" class="form-control" min="1" max="999" value="<?php echo $episode; ?>" />
					<span class="help-block"><?php echo $episodeErr; ?></span>
				</div>
				<div class="form-group <?php echo (!empty($runtimeErr)) ? "has-error" : ""; ?>">
					<label>Runtime (minutes)</label>
					<input type="number" name="runtime" class="form-control" min="1" max="999" value="<?php echo $runtime; ?>" />
					<span class="help-block"><?php echo $runtimeErr; ?></span>
				</div>
				<div class="form-group <?php echo (!empty($viewidErr)) ? "has-error" : ""; ?>">
					<label>View ID</label>
					<input type="text" name="view_id" class="form-control" size="33" value="<?php echo $viewID; ?>" />
					<span class="help-block"><?php echo $viewidErr; ?></span>
				</div> 
				<div class="form-group">
					<input type="submit" class="btn btn-primary" value="Add" />
				</div>
			</form>
			<footer>
				<p>&copy; Rexroth Computing, 2019</p>
			</footer>
		</div>
	</body>
</html>