<?php
	session_start();
	require_once "config.php";
	
	if ($_SESSION["user_admin"])
	{
		$itemID = $_GET["id"];
		$itemSeason = $_GET["s"];
		$itemEpisode = $_GET["e"];
		$query = "SELECT type FROM items WHERE id = ?";
		$result = $dbHandle->runQuery($query, "i", array($itemID));
		
		if ($result->num_rows > 0)
		{
			$itemType = $result[0]["type"];
			
			if ($itemType == $SHOW)
			{
				$query = "SELECT title, runtime, viewid FROM shows_episodes WHERE id = ? AND season = ? AND episode = ?";
				$result = $dbHandle->runQuery($query, "iii", array($itemID, $itemSeason, $itemEpisode));
				
				if ($result->num_rows > 0)
				{
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
						
						if (empty($seasonErr) && empty($episodeErr) && ($season != $itemSeason || $episode != $itemEpisode))
						{
							$query = "SELECT * FROM shows_episodes WHERE id = ? AND season = ? AND episode = ?";
							$item = $dbHandle->runQuery($query, "iii", array($itemID, $itemSeason, $itemEpisode));
							
							if ($item->num_rows > 0)
							{
								$seasonErr = $episodeErr = "Another item with the new season and episode already exists.";
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
							$query = "UPDATE shows_episodes SET title = ?, season = ?, episode = ?, runtime = ?, viewid = ? WHERE id = ? AND season = ? AND episode = ?";
							$success = $dbHandle->runQuery($query, "siiisiii", array($title, $season, $episode, $runtime, $viewID, $id, $itemSeason, $itemEpisode));
							
							if ($success)
							{
								$util->redirect("item.php?id=" . $id);
							}
							else
							{
								$titleErr = "An error occured in adding the new episode.";
							}
						}
					}
					else
					{
						$item = $result[0];
						$title = $item["title"];
						$season = $itemSeason;
						$episode = $itemEpisode;
						$runtime = $item["runtime"];
						$viewID = $item["view_id"];
						$query = "SELECT title FROM shows WHERE id = ?";
						$showTitle = $dbHandle->runQuery($query, "i", array($itemID))[0]["title"];
					}
				}
				else
				{
					$util->redirect("404.php");
				}
			}
			else
			{
				$util->redirect("500.php");
			}
		}
		else
		{
			$util->redirect("404.php");
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
		<title>Pirate Library - Edit Episode</title>
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
			<h2>Edit Episode of <?php echo $showTitle; ?></h2>
			<p>Enter the info for the episode you are editing.</p>
			<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?id=" . $itemID . "&s=" . $itemSeason . "&e=" . $itemEpisode; ?>" method="post" enctype="multipart/form-data">
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
				<div class="form-group <?php echo (!empty($episodeErr)) ? "has-error" : ""; ?>">
					<label>Episode</label>
					<input type="number" name="episode" class="form-control" min="1" max="999" value="<?php echo $episode; ?>" />
					<span class="help-block"><?php echo $episodeErr; ?></span>
				</div>
				<div class="form-group <?php echo (!empty($runtimeErr)) ? "has-error" : ""; ?>">
					<label>Runtime (minutes)</label>
					<input type="number" name="runtime" class="form-control" min="1" max="999" value="<?php echo $runtime; ?>" />
					<span class="help-block"><?php echo $runtimeErr; ?></span>
				</div>
				<div class="form-group <?php echo (!empty($viewIDErr)) ? "has-error" : ""; ?>">
					<label>View ID</label>
					<input type="text" name="view_id" class="form-control" size="33" value="<?php echo $viewID; ?>" />
					<span class="help-block"><?php echo $viewIDErr; ?></span>
				</div> 
				<div class="form-group">
					<input type="submit" class="btn btn-primary" value="Submit Changes" />
				</div>
			</form>
			<a href="<?php echo "deleteepisode.php?id=" . $itemID . "&s=" . $itemSeason . "&e=" . $itemEpisode; ?>" class="btn btn-primary btn-delete">Delete Episode</a>
			<footer>
				<p>&copy; Rexroth Computing, 2019</p>
			</footer>
		</div>
	</body>
</html>