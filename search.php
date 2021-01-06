<?php
	session_start();
	require_once "config.php";
	
	$titles = "";
	$s = preg_split('/\s+/', $_GET["s"]);
	
	if ($_GET["s"] == "brew coffee with a teapot")
	{
		header("location: 418.php");
		exit;
	}
	
	if (count($s) > 0)
	{
		$paramTypes = "ss";
		$stmt1 = "SELECT id, title FROM movies WHERE (title LIKE CONCAT('%', ?, '%'))";
		$stmt2 = "SELECT id, title FROM shows WHERE (title LIKE CONCAT('%', ?, '%'))";
		
		foreach ($s as $i => $t)
		{
			if ($i != 0)
			{
				$stmt1 = $stmt1 . " AND (title LIKE CONCAT('%', ?, '%'))";
				$stmt2 = $stmt2 . " AND (title LIKE CONCAT('%', ?, '%'))";
				$paramTypes = $paramTypes . "ss";
			}
		}
		
		$sql = $connection->prepare($stmt1 . " UNION " . $stmt2 . " ORDER BY RAND() LIMIT 64");
		$inputArray[] = &$paramTypes;
		
		for ($i = 0; $i < count($s); $i++)
		{
			$inputArray[] = &$s[$i];
		}
		
		for ($i = 0; $i < count($s); $i++)
		{
			$inputArray[] = &$s[$i];
		}
		
		call_user_func_array(array($sql, 'bind_param'), $inputArray);
		$sql->execute();
		$result = $sql->get_result();
		
		if ($result->num_rows > 0)
		{
			while ($row = $result->fetch_assoc())
			{
				$id = $row["id"];
				$title = $row["title"];
				$titles = $titles . "<div class=\"movie-item\"><a href=\"item.php?id=" . $id . "\"><img src=\"img/poster/" . $id . ".jpg\" alt=\"" . $title . "\" /><span>" . $title . "</span></a></div>";
			}
		}
		
		$sql->close();
	}
	else
	{
		$titles = "<p>No results found.</p>";
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
		<title>Pirate Library - Search "<?php echo $_GET["s"]; ?>"</title>
	</head>
	<body>
		<header>
			<div class="top-nav">
				<?php echo $login; ?>
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
		<div class="browse-wrapper">
			<?php echo $titles; ?>
			<footer>
				<p>&copy; Rexroth Computing, 2019</p>
			</footer>
		</div>
	</body>
</html>