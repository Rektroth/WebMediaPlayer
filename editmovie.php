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
			
			if ($itemType == $MOVIE)
			{
				$release = "2000-01-01";

				if ($_SERVER["REQUEST_METHOD"] == "POST")
				{
					$titleErr = $posterErr = $descriptionErr = $ratingErr = $genreErr = $runtimeErr = $releaseErr = $viewDIErr = "";

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

					if (empty(trim($genre_err)))
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

					if (empty(trim($_POST["release"])))
					{
						$releaseErr = "Please enter a release date.";
					}
					else
					{
						$d = DateTime::createFromFormat("Y-m-d", $_POST["release"]);

						if ($d && $d->format("Y-m-d") === $_POST["release"])
						{
							$release = trim($_POST["release"]);
						}
						else
						{
							$releaseErr = "Please enter a valid date.";
						}
					}

					if (empty(trim($_POST["view_id"])))
					{
						$viewIDErr = "Please enter view ID.";
					}
					else
					{
						$viewID = trim($_POST["view_id"]);
					}

					if (empty($titleErr) && empty($posterErr) && empty($descriptionErr) && empty($ratingErr) && empty($genreErr) && empty($runtimeErr) && empty($releaseErr) && empty($viewidErr))
					{
						if (!empty(basename($_FILES["poster"]["name"])))
						{
							$posterDir = "img/poster/" . $itemID . ".jpg";
							move_uploaded_file($_FILES["poster"]["tmp_name"], $posterDir);
						}
						
						$query = "UPDATE movies SET title = ?, description = ?, rating = ?, runtime = ?, release_date = ?, view_id = ? WHERE id = ?";
						$success = $dbHandle->runQuery($query, "sssssss", array($title, $description, $rating, $runtime, $release, $viewID, $itemID));

						if ($success)
						{
							$query = "DELETE FROM movies_genres WHERE id = ?";
							$dbHandle->runQuery($query, "s", array($itemID));
							
							foreach ($genre as $i => $g)
							{
								$query = "INSERT INTO movies_genres (id, genre) VALUES (?, ?)";
								$dbHandle->runQuery($query, "is", array($itemID, $g));
							}
							
							$util->redirect("item.php?id=" . $itemID);
						}
						else
						{
							$titleErr = "An error occured in modifying the movie.";
						}
					}
				}
				else
				{
					$query = "SELECT title, description, rating, runtime, release_date, view_id FROM movies WHERE id = ?";
					$result = $dbHandle->runQuery($query, "i", array($itemID));

					if ($result->num_rows > 0)
					{
						$item = $result[0];
						$title = $item["title"];
						$description = $item["description"];
						$rating = $item["rating"];
						$runtime = $item["runtime"];
						$release = $item["releasedate"];
						$viewID = $item["view_id"];
						$query = "SELECT genre FROM movies_genres WHERE id = ?";
						$result = $dbHandle->runQuery($query, "i", array($itemID));
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
	}
	else
	{
		$util->redirecT("500.php");
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
		<title>Pirate Library - Edit Movie</title>
	</head>
	<body>
		<header>
			<div class="top-nav">
				<?php echo "<a href=\"account.php\">" . $_COOKIE["username"] . "</a>"; ?>
			</div>
			<ul>
				<li class="selected">
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
			<h2>Edit Movie</h2>
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
				<div class="form-group <?php echo (!empty($releaseErr)) ? "has-error" : ""; ?>">
					<label>Release date</label>
					<input type="date" name="release" class="form-control" min="1850-01-01" value="<?php echo $release; ?>" />
					<span class="help-block"><?php echo $releaseErr; ?></span>
				</div>
				<div class="form-group <?php echo (!empty($viewIDErr)) ? "has-error" : ""; ?>">
					<label>View ID</label>
					<input type="text" name="viewid" class="form-control" size="33" value="<?php echo $viewID; ?>" />
					<span class="help-block"><?php echo $viewIDErr; ?></span>
				</div> 
				<div class="form-group">
					<input type="submit" class="btn btn-primary" value="Submit Changes" />
				</div>
			</form>
			<a href="<?php echo "deleteitem.php?id=" . $itemID; ?>" class="btn btn-primary btn-delete">Delete Item</a>
			<footer>
				<p>&copy; Rexroth Computing, 2019</p>
			</footer>
		</div>
	</body>
</html>