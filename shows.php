<?php
	session_start();
	require_once "config.php";
	
	if ($_GET["c"] <= 28 && $_GET["c"] >= 1)
	{
		$category = $GENRE[$_GET["c"] - 1];
		$sql = $connection->prepare("SELECT title, id FROM shows WHERE id IN (SELECT id FROM shows_genres WHERE genre = ?) ORDER BY RAND() LIMIT 64");
		$sql->bind_param("s", $category);
	}
	else
	{
		$category = "";
		$sql = $connection->prepare("SELECT title, id FROM shows ORDER BY RAND() LIMIT 64");
	}
	
	$sql->execute();
	$result = $sql->get_result();
	$sql->close();
	
	if ($result->num_rows > 0)
	{
		$titles = "";
		
		while ($row = $result->fetch_assoc())
		{
			$id = $row["id"];
			$title = $row["title"];
			$titles = $titles . "<div class=\"show-item\"><a href=\"item.php?id=" . $id . "\"><img src=\"img/poster/" . $id . ".jpg\" alt=\"" . $title . "\" /><span>" . $title . "</span></a></div>";
		}
		
		if ($_SESSION["admin"] == true)
		{
			$titles = $titles . "<div class=\"add-item\"><a href=\"addshow.php\"><img src=\"img/add-item.png\" /></a></div>";
		}
	}
	else
	{
		$titles = "<p>No shows.</p><p><a href=\"addshows.php\">Add a show.</a></p>";
	}
	
	$connection->close();
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
		<title>Pirate Library - <?php if ($category != "") echo $category . " "; ?>Shows</title>
	</head>
	<body>
		<header>
			<div class="top-nav">
				<a id="categories-button">&#60; Categories</a>
				<?php echo $login; ?>
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
		<div class="categories-panel">
			<ul>
				<li>
					<a class="<?php if ($_GET["c"] == 1) echo "selected"; ?>" href="shows.php?c=1">Action</a>
				</li>
				<li>
					<a class="<?php if ($_GET["c"] == 2) echo "selected"; ?>" href="shows.php?c=2">Adult</a>
				</li>
				<li>
					<a class="<?php if ($_GET["c"] == 3) echo "selected"; ?>" href="shows.php?c=3">Adventure</a>
				</li>
				<li>
					<a class="<?php if ($_GET["c"] == 4) echo "selected"; ?>" href="shows.php?c=4">Animation</a>
				</li>
				<li>
					<a class="<?php if ($_GET["c"] == 5) echo "selected"; ?>" href="shows.php?c=5">Biography</a>
				</li>
				<li>
					<a class="<?php if ($_GET["c"] == 6) echo "selected"; ?>" href="shows.php?c=6">Comedy</a>
				</li>
				<li>
					<a class="<?php if ($_GET["c"] == 7) echo "selected"; ?>" href="shows.php?c=7">Crime</a>
				</li>
				<li>
					<a class="<?php if ($_GET["c"] == 8) echo "selected"; ?>" href="shows.php?c=8">Documentary</a>
				</li>
				<li>
					<a class="<?php if ($_GET["c"] == 9) echo "selected"; ?>" href="shows.php?c=9">Drama</a>
				</li>
				<li>
					<a class="<?php if ($_GET["c"] == 10) echo "selected"; ?>" href="shows.php?c=10">Family</a>
				</li>
				<li>
					<a class="<?php if ($_GET["c"] == 11) echo "selected"; ?>" href="shows.php?c=11">Fantasy</a>
				</li>
				<li>
					<a class="<?php if ($_GET["c"] == 12) echo "selected"; ?>" href="shows.php?c=12">Film Noir</a>
				</li>
				<li>
					<a class="<?php if ($_GET["c"] == 13) echo "selected"; ?>" href="shows.php?c=13">Game-Show</a>
				</li>
				<li>
					<a class="<?php if ($_GET["c"] == 14) echo "selected"; ?>" href="shows.php?c=14">History</a>
				</li>
				<li>
					<a class="<?php if ($_GET["c"] == 15) echo "selected"; ?>" href="shows.php?c=15">Horror</a>
				</li>
				<li>
					<a class="<?php if ($_GET["c"] == 16) echo "selected"; ?>" href="shows.php?c=16">Musical</a>
				</li>
				<li>
					<a class="<?php if ($_GET["c"] == 17) echo "selected"; ?>" href="shows.php?c=17">Music</a>
				</li>
				<li>
					<a class="<?php if ($_GET["c"] == 18) echo "selected"; ?>" href="shows.php?c=18">Mystery</a>
				</li>
				<li>
					<a class="<?php if ($_GET["c"] == 19) echo "selected"; ?>" href="shows.php?c=19">News</a>
				</li>
				<li>
					<a class="<?php if ($_GET["c"] == 20) echo "selected"; ?>" href="shows.php?c=20">Reality-TV</a>
				</li>
				<li>
					<a class="<?php if ($_GET["c"] == 21) echo "selected"; ?>" href="shows.php?c=21">Romance</a>
				</li>
				<li>
					<a class="<?php if ($_GET["c"] == 22) echo "selected"; ?>" href="shows.php?c=22">Sci-Fi</a>
				</li>
				<li>
					<a class="<?php if ($_GET["c"] == 23) echo "selected"; ?>" href="shows.php?c=23">Short</a>
				</li>
				<li>
					<a class="<?php if ($_GET["c"] == 24) echo "selected"; ?>" href="shows.php?c=24">Sport</a>
				</li>
				<li>
					<a class="<?php if ($_GET["c"] == 25) echo "selected"; ?>" href="shows.php?c=25">Talk-Show</a>
				</li>
				<li>
					<a class="<?php if ($_GET["c"] == 26) echo "selected"; ?>" href="shows.php?c=26">Thriller</a>
				</li>
				<li>
					<a class="<?php if ($_GET["c"] == 27) echo "selected"; ?>" href="shows.php?c=27">War</a>
				</li>
				<li>
					<a class="<?php if ($_GET["c"] == 28) echo "selected"; ?>" href="shows.php?c=28">Western</a>
				</li>
			</ul>
		</div>
		<div class="browse-wrapper">
			<?php echo $titles; ?>
			<footer>
				<p>&copy; Rexroth Computing, 2019</p>
			</footer>
		</div>
	</body>
</html>