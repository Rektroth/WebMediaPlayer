<?php
	session_start();
	require_once "config.php";
	
	if ($_SESSION["user_admin"])
	{
		$itemID = $_GET["id"];
		$query = "SELECT type FROM items WHERE id = ?";
		$result = $dbHandle->runQuery($query, "i", array($itemID));
		
		if ($result->num_rows > 0)
		{
			$itemType = $result[0]["type"];
			
			if ($itemType == $SHOW)
			{
				$premiere = 2000;
				$finale = 2001;
				
				if ($_SERVER["REQUEST_METHOD"] == "POST")
				{
					$titleErr = $posterErr = $descriptionErr = $ratingErr = $genreErr = $runtimeErr = $premiereErr = $finaleErr = "";
					
					if (empty(trim($_POST["title"])))
					{
						$titleErr = "Please enter title.";
					}
					else
					{
						$title = trim($_POST["title"]);
					}
					
					$posterDir = "img/poster/" . basename($_FILES["poster"]["name"]);
					$imageFileType = strtolower(pathinfo($poster_dir, PATHINFO_EXTENSION));
					$check = getimagesize($_FILES["poster"]["tmp_name"]);
					
					if (!empty(basename($_FILES["poster"]["name"])))
					{
						if (!$check)
						{
							if ($_FILES["poster"]["size"] <= $MAX_FILE_SIZE)
							{
								if ($imageFileType != "jpg" && $imageFileType != "jpeg")
								{
									$posterErr = "Poster/cover must be a jpeg.";
								}
							}
							else
							{
								$posterErr = "The poster/cover is too large.";
							}
						}
						else
						{
							$posterErr = "The file is not an image.";
						}
					}
					
					if (empty(trim($_POST["description"])))
					{
						$descriptionErr = "Please enter description.";
					}
					else
					{
						$description = trim($_POST["description"]);
					}
					
					$validRating = false;
					
					if (empty(trim($_POST["rating"])))
					{
						$ratingErr = "Please choose a rating.";
					}
					else
					{
						foreach ($RATING as $i => $r)
						{
							if ($r == trim($_POST["rating"]))
							{
								$validRating = true;
								break;
							}
						}

						if ($validRating)
						{
							$rating = trim($_POST["rating"]);
						}
						else
						{
							$ratingErr = "Please choose a valid rating.";
						}
					}

					$validGenre = false;
					$genre = $_POST["genre"];

					foreach ($genre as $i => $g1)
					{
						foreach ($genre as $j => $g2)
						{
							if ($i != $j)
							{
								if ($g1 == $g2)
								{
									$genreErr = "You cannot apply the same genre twice.";
								}
							}
						}
					}

					$genreValid = array();

					if (empty(trim($genreErr)))
					{
						foreach ($genre as $i => $g1)
						{
							foreach ($GENRE as $j => $g2)
							{
								if ($g1 == $g2)
								{
									$genreValid[$i] = true;
									break;
								}
							}
						}
					}

					foreach ($genreValid as $i => $g)
					{
						if (!$g)
						{
							$genreErr = "Please provide valid genres.";
							break;
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

					if (empty(trim($_POST["premiere"])))
					{
						$releaseErr = "Please enter a premiere year.";
					}
					else
					{
						$premiere = trim($_POST["premiere"]);
					}

					if (empty(trim($_POST["finale"])))
					{
						$finaleErr = "Please enter a finale year.";
					}
					else
					{
						if (trim($_POST["finale"]) >= $premiere)
						{
							$finale = trim($_POST["finale"]);
						}
						else
						{
							$finaleErr = "Finale must be after premiere.";
						}
					}
					
					if (empty($titleErr) && empty($posterErr) && empty($descriptionErr) && empty($ratingErr) && empty($genreErr) && empty($runtimeErr) && empty($premiereErr) && empty($finaleErr))
					{
						if (!empty(basename($_FILES["poster"]["name"])))
						{
							$posterDir = "img/poster/" . $itemID . ".jpg";
							move_uploaded_file($_FILES["poster"]["tmp_name"], $posterDir);
						}
						
						$query = "UPDATE shows SET title = ?, description = ?, rating = ?, runtime = ?, premiereyear = ?, finaleyear = ? WHERE id = ?";
						$success = $dbHandle->runQuery($query, "sssiiii", array($title, $description, $rating, $runtime, $premiere, $finale, $itemID));

						if ($success)
						{
							$query = "DELETE FROM shows_genres WHERE id = ?";
							$dbHandle->runQuery($query, "i", array($itemID));
							
							foreach ($genre as $i => $g)
							{
								$sql = $connection->prepare("INSERT INTO shows_genres (id, genre) VALUES (?, ?)");
								$dbHandle->runQuery($query, "is", array($itemID, $g));
							}
							
							$util->redirect("location: item.php?id=" . $itemID);
						}
						else
						{
							$titleErr = "An error occured in modifying the show.";
						}
					}
				}
				else
				{
					$query = "SELECT title, description, rating, runtime, premiereyear, finaleyear FROM shows WHERE id = ?";
					$result = $dbHandle->runQuery($query, "i", array($itemID));

					if ($result->num_rows > 0)
					{
						$item = $result[0];
						$title = $item["title"];
						$description = $item["description"];
						$rating = $item["rating"];
						$runtime = $item["runtime"];
						$premiere = $item["premiereyear"];
						$finale = $item["finaleyear"];
						$query = "SELECT genre FROM shows_genres WHERE id = ?";
						$result = $dbHandle->runQuery($query, "i", $itemID);
						$genre = array();

						if ($result->num_rows > 0)
						{
							for ($i = 0; $i < $result->num_rows; $i++)
							{
								$row = $result->fetch_assoc();
								$genre[$i] = $row["genre"];
							}
						}

						$genreSelects = "";

						foreach ($genre as $i => $g1)
						{
							if (!empty(trim($g1)))
							{
								$genreSelects .= "<div><select name=\"genre[]\" class=\"form-control\">";

								foreach ($GENRE as $j => $g2)
								{
									$genreSelects .= "<option value=\"" . $g2 . "\"";

									if ($g1 == $g2)
									{
										$genreSelects .= " selected";
									}

									$genreSelects .= ">" . $g2 . "</option>";
								}

								$genreSelects .= "</select><a href=\"#\" class=\"remove-field\"><span class=\"material-icons\">clear</span></a></div>";
							}
						}
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
		}
		else
		{
			$util->redirect("404.php");
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
		<title>Pirate Library - Edit Show</title>
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
			<h2>Edit Show</h2>
			<p>Change the info appropriate.</p>
			<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?id=" . $itemID; ?>" method="post" enctype="multipart/form-data">
				<div class="form-group <?php echo (!empty($titleErr)) ? "has-error" : ""; ?>">
					<label>Title</label>
					<input type="text" name="title" class="form-control" size="255" value="<?php echo $title; ?>" />
					<span class="help-block"><?php echo $titleErr; ?></span>
				</div>
				<div class="form-group <?php echo (!empty($titleErr)) ? "has-error" : ""; ?>">
					<label>Poster/Cover</label>
					<input type="file" name="poster" class="form-control" value="<?php echo $poster; ?>" />
					<span class="help-block"><?php echo $posterErr; ?></span>
				</div>
				<div class="form-group <?php echo (!empty($descriptionErr)) ? "has-error" : ""; ?>">
					<label>Description</label>
					<textarea name="description" class="form-control text" cols="64" rows="256"><?php echo $description; ?></textarea>
					<span class="help-block"><?php echo $descriptionErr; ?></span>
				</div>
				<div class="form-group <?php echo (!empty($ratingErr)) ? "has-error" : ""; ?>">
					<label>Rating</label>
					<div>
						<select name="rating" class="form-control">
							<option value="NR"<?php if ($rating == "NR") echo " selected"; ?>>NR</option>
							<option value="G"<?php if ($rating == "G") echo " selected"; ?>>G</option>
							<option value="PG"<?php if ($rating == "PG") echo " selected"; ?>>PG</option>
							<option value="PG-13"<?php if ($rating == "PG-13") echo " selected"; ?>>PG-13</option>
							<option value="R"<?php if ($rating == "R") echo " selected"; ?>>R</option>
							<option value="NC-17"<?php if ($rating == "NC-17") echo " selected"; ?>>NC-17</option>
							<option value="TV-Y"<?php if ($rating == "TV-Y") echo " selected"; ?>>TV-Y</option>
							<option value="TV-Y7"<?php if ($rating == "TV-Y7") echo " selected"; ?>>TV-Y7</option>
							<option value="TV-G"<?php if ($rating == "TV-G") echo " selected"; ?>>TV-G</option>
							<option value="TV-PG"<?php if ($rating == "TV-PG") echo " selected"; ?>>TV-PG</option>
							<option value="TV-14"<?php if ($rating == "TV-14") echo " selected"; ?>>TV-14</option>
							<option value="TV-MA"<?php if ($rating == "TV-MA") echo " selected"; ?>>TV-MA</option>
						</select>
					</div>
					<span class="help-block"><?php echo $ratingErr; ?></span>
				</div>
				<div class="form-group <?php echo (!empty($genreErr)) ? "has-error" : ""; ?>">
					<label>Genres</label>
					<div id="genre-wrapper">
						<?php echo $genreSelects; ?>
					</div>
					<input type="button" id="add-genre-button" class="btn" value="Add Genre" />
					<span class="help-block"><?php echo $genreErr; ?></span>
				</div>
				<div class="form-group <?php echo (!empty($runtimeErr)) ? "has-error" : ""; ?>">
					<label>Runtime (minutes)</label>
					<input type="number" name="runtime" class="form-control" min="1" max="999" value="<?php echo $runtime; ?>" />
					<span class="help-block"><?php echo $runtimeErr; ?></span>
				</div>
				<div class="form-group <?php echo (!empty($premiereErr)) ? "has-error" : ""; ?>">
					<label>Premiere year</label>
					<input type="number" name="premiere" class="form-control" min="1850" max="9999" value="<?php echo $premiere; ?>" />
					<span class="help-block"><?php echo $premiereErr; ?></span>
				</div>
				<div class="form-group <?php echo (!empty($finaleErr)) ? "has-error" : ""; ?>">
					<label>Finale year</label>
					<input type="number" name="finale" class="form-control" min="1850" max="9999" value="<?php echo $finale; ?>" />
					<span class="help-block"><?php echo $finaleErr; ?></span>
				</div>
				<div class="form-group">
					<input type="submit" class="btn btn-primary" value="Submit Changes" />
				</div>
			</form>
			<a href="<?php echo "deleteitem.php?id=" . $itemID; ?>" class="btn btn-primary btn-delete">Delete Show</a>
			<footer>
				<p>&copy; Rexroth Computing, 2019</p>
			</footer>
		</div>
	</body>
</html>