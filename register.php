<?php
	session_start();
	require_once "authcookiesessionvalidate.php";
	
	$dbHandle = new DBController();
	
	if ($isLoggedIn)
	{
		$util->redirect("account.php");
	}
	
	$username = $password = $confirmPassword = "";
	$usernameErr = $passwordErr = $confirmPasswordErr = "";
	
	if ($_SERVER["REQUEST_METHOD"] == "POST")
	{
		if (empty(trim($_POST["username"])))
		{
			$usernameErr = "Please enter a username.";
		}
		else
		{
			$query = "SELECT id FROM users WHERE username = ?";
			$result = $dbHandle->runQuery($query, "s", trim($_POST["username"]));
			
			if ($result->num_rows > 0)
			{
				$usernameErr = "This username is already taken.";
			}
			else
			{
				$username = trim($_POST["username"]);
			}
		}
		
		if (empty(trim($_POST["password"])))
		{
			$passwordErr = "Please enter a password.";
		}
		elseif (strlen(trim($_POST["password"])) < 8)
		{
			$passwordErr = "Password must have atleast 8 characters.";
		}
		else
		{
			$password = trim($_POST["password"]);
		}
		
		if (empty(trim($_POST["confirm_password"])))
		{
			$confirmPasswordErr = "Please confirm password.";     
		}
		else
		{
			$confirmPassword = trim($_POST["confirm_password"]);
			
			if (empty($passwordErr) && ($password != $confirmPassword))
			{
				$confirmPasswordErr = "Password did not match.";
			}
		}
		
		if (empty($usernameErr) && empty($passwordErr) && empty($confirmPasswordErr))
		{
			$salt = randomString();
			$query = "INSERT INTO users (username, password, salt) VALUES (?, ?, ?)";
			$result = $dbHandle->runQuery($query, "sss", array($username, password_hash($password + $salt, PASSWORD_DEFAULT), $salt));
			
			if ($result->num_rows > 0)
			{
				$util->redirect("login.php");
			}
			else
			{
				$usernameErr = "An error occured when creating the account.";
			}
		}
	}
	
	function randomString($length = 16)
	{
		$characters = "0123456789qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM";
		$charactersLength = strlen($characters);
		$randomString = "";
		
		for ($i = 0; $i < $length; $i++)
		{
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		
		return $randomString;
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
		<title>Pirate Library - Sign Up</title>
	</head>
	<body>
		<header>
			<div class="top-nav">
				<a href="login.php">Login</a>
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
			<h2>Sign Up</h2>
			<p>Please fill this form to create an account.</p>
			<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
				<div class="form-group <?php echo (!empty($usernameErr)) ? 'has-error' : ''; ?>">
					<label>Username</label>
					<input type="text" name="username" class="form-control" value="<?php echo $username; ?>" />
					<span class="help-block"><?php echo $usernameErr; ?></span>
				</div>    
				<div class="form-group <?php echo (!empty($passwordErr)) ? 'has-error' : ''; ?>">
					<label>Password</label>
					<input type="password" name="password" class="form-control" value="<?php echo $password; ?>" />
					<span class="help-block"><?php echo $passwordErr; ?></span>
				</div>
				<div class="form-group <?php echo (!empty($confirmPasswordErr)) ? 'has-error' : ''; ?>">
					<label>Confirm Password</label>
					<input type="password" name="confirmPassword" class="form-control" value="<?php echo $confirm_password; ?>" />
					<span class="help-block"><?php echo $confirmPasswordErr; ?></span>
				</div>
				<div class="form-group">
					<input type="submit" class="btn btn-primary" value="Submit" />
					<input type="reset" class="btn btn-default" value="Reset" />
				</div>
				<p>Already have an account? <a href="login.php">Login here</a>.</p>
			</form>
			<footer>
				<p>&copy; Rexroth Computing, 2019</p>
			</footer>
		</div>
	</body>
</html>
