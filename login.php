<?php
	session_start();
	require_once "authcookiesessionvalidate.php";
	
	if ($isLoggedIn)
	{
		$util->redirect("account.php");
	}
	
	if (!empty($_POST["username"]))
	{
		$username = $_POST["username"];
		$password = $_POST["password"];
		$user = $auth->getUserByUsername($username);
		
		if (password_verify($password + $user[0]["salt"], $user[0]["password"]))
		{
			$_SESSION["user_id"] = $user[0]["id"];
			
			if ($user[0]["admin"] === 1)
			{
				$_SESSION["user_admin"] = true;
			}
			
			if (!empty($_POST["remember"]))
			{
				setcookie("username", $username, $cookieExpirationTime);
				$randomPassword = $util->getToken(16);
				setcookie("random_password", $randomPassword, $cookieExpirationTime);
				$randomSelector = $util->getToken(32);
				setcookie("random_selector", $randomSelector, $cookieExpirationTime);
				$randomPasswordHash = password_hash($randomPassword, PASSWORD_DEFAULT);
				$randomSelectorHash = password_hash($randomSelector, PASSWORD_DEFAULT);
				$expiryDate = date("Y-m-d H:i:s", $cookieExpirationTime);
				$userToken = $auth->getTokenByUsername($username, 0);
				
				if (!empty($userToken[0]["id"]))
				{
					$auth->markAsExpired($userToken[0]["id"]);
				}
				
				$auth->insertToken($username, $randomPasswordHash, $randomSelectorHash, $expiryDate);
			}
			else
			{
				$util->clearAuthCookie();
			}
			
			$util->redirect("account.php");
		}
		else
		{
			$loginErr = "Username or password is incorrect.";
		}
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
		<title>Pirate Library - Login</title>
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
			<h2>Login</h2>
			<p>Please fill in your credentials to login.</p>
			<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
				<div class="form-group">
					<label>Username</label>
					<input type="text" name="username" class="form-control" value="<?php echo $username; ?>" />
				</div>    
				<div class="form-group <?php echo (!empty($loginErr)) ? 'has-error' : ''; ?>">
					<label>Password</label>
					<input type="password" name="password" class="form-control" />
					<span class="help-block"><?php echo $loginErr; ?></span>
				</div>
				<div class="form-group">
					<input type="checkbox" name="remember" value="remember" />
					<span>Remember me</span>
				</div>
				<div class="form-group">
					<input type="submit" class="btn btn-primary" value="Login" />
				</div>
				<p>Don't have an account? <a href="register.php">Sign up now</a>.</p>
			</form>
			<footer>
				<p>&copy; Rexroth Computing, 2019</p>
			</footer>
		</div>
	</body>
</html>