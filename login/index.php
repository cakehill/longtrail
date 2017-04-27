<?php # Script 18.8 - login.php
// This is the login page for the site.

session_start();
ob_start();

require ('../includes/config.inc.php'); 
$page_title = 'Login';
include ('../includes/header.inc.php'); 
?>
<body>
<form action="index.php" method="post" data-ajax="false" id="loginform">
<div data-role="page" data-theme="c">
	<div data-role="header">
		<h1>Login</h1>
	</div>
	<div data-role="content" data-theme="c">	
<?
require (MYSQL);
$stmt = $dbc->stmt_init();

//check to see if there is a cookie stored so we can log them back in and set the proper session variables
if (isset($_COOKIE['token'])) {
	$token = mysqli_real_escape_string ($dbc, $_COOKIE['token']);
	$q1 = "SELECT user_id, first_name, user_level FROM users WHERE token = '$token' AND active IS TRUE";
	$r1 = mysqli_query ($dbc, $q1) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));
	
	if (@mysqli_num_rows($r1) == 1) { // A match was made.
		// Register the values:
		$_SESSION = mysqli_fetch_array ($r1, MYSQLI_ASSOC); 

		//regenerate a new token to keep things secure
		$newToken = md5(uniqid(mt_rand(), true));

		//update the new token id
		$sql = 'UPDATE users SET token = ? WHERE user_id = ?';
  		if ($stmt->prepare($sql)) {
			$stmt->bind_param('si', $newToken, $_SESSION['user_id']);
			$done = $stmt->execute();
		}			

		//close up the connection
		mysqli_free_result($r1);
		mysqli_close($dbc);
 
		//set a cookie with the new token
		setcookie("token", $newToken, mktime()+(86400*30), "/");

		// Delete the buffer.
		ob_end_clean();
		
		//redirect the user
		$url = BASE_URL . '../index.php';
		header("Location: $url");
		
		// Quit the script.
		exit();
	}
}

//if user has submitted the form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	// Validate the email address:
	if (!empty($_POST['email'])) {
		$e = mysqli_real_escape_string ($dbc, $_POST['email']);
	} else {
		$e = FALSE;
		echo '<p class="error">You forgot to enter your email address!</p>';
	}
	
	// Validate the password:
	if (!empty($_POST['pass'])) {
		$p = mysqli_real_escape_string ($dbc, $_POST['pass']);
	} else {
		$p = FALSE;
		echo '<p class="error">You forgot to enter your password!</p>';
	}
	
	// If everything's is ok
	if ($e && $p) { 
		// Query the database:
		$q = "SELECT user_id, first_name, user_level FROM users WHERE (email='$e' AND pass=SHA1('$p')) AND active IS TRUE";		
		$r = mysqli_query ($dbc, $q) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));
		
		// A match was made.
		if (@mysqli_num_rows($r) == 1) {

			//set a token so we can log them in next time
			$newToken = null;
			if ($_POST['checkbox-1'] == 'on') {
				$newToken = md5(uniqid(mt_rand(), true));
				setcookie("token", $newToken, mktime()+(86400*30), "/");
			}

			//update some user info
			$sql = 'UPDATE users SET last_login = ?, token = ?
			WHERE email = ?';
			$ll = date("D M j, g:i a");
  			if ($stmt->prepare($sql)) {
				$stmt->bind_param('sss', $ll, $newToken, $e);
				$done = $stmt->execute();
			}			
			// Register the values:
			$_SESSION = mysqli_fetch_array ($r, MYSQLI_ASSOC); 
			
			//close up the connection
			mysqli_free_result($r);
			mysqli_close($dbc);
			
			//set a cookie to fillin the login form next time
			setcookie("email", $e, mktime()+(86400*30), "/");
			
			//determine their access and send them off to their area
			//if ($_SESSION['user_level'] == '1') {
				// Redirect the user:
			//	$url = BASE_URL . '../admin.php';
			//}else {
				$url = BASE_URL . '../index.php';
			//}
			
			// Delete the buffer.
			ob_end_clean();
			header("Location: $url");
			
			// Quit the script.
			exit();
				
		} else { // No match was made.
			echo '<p class="error">Either the email address and password entered do not match those on file or your account has not been activated.</p>';
		}
		
	} else { // If everything wasn't OK.
		echo '<p class="error">Please try again.</p>';
	}
	
	mysqli_close($dbc);

} // End.
?>
		<div data-role="fieldcontain">
	         <label for="email" class="ui-hidden-accessible">Email:</label>
	         <input placeholder="Email address" type="email" maxlength="80" name="email" id="email" class="required email" value="<?php if (isset($_POST['email'])) echo $_POST['email']; elseif (isset($_COOKIE['email'])) echo $_COOKIE['email']; ?>" />
		</div>
		<div data-role="fieldcontain">
	         <label for="pass" class="ui-hidden-accessible">Password:</label>
	         <input placeholder="Password" maxlength="40" type="password" name="pass" id="pass" class="required" maxlength="20" value="<?php if (isset($_POST['pass'])) echo $_POST['pass']; ?>" />
		</div>
		<div data-role="fieldcontain">
			<input type="checkbox" name="checkbox-1" id="checkbox-1" />
			<label for="checkbox-1">Remember Me</label>
		</div>

		<button type="submit" data-theme="c" name="submit" value="submit-value">Login</button>		  
		<fieldset class="ui-grid-a">
			<div class="ui-block-a"><a href="/login/register.php" data-role="button" data-theme="c" data-icon="gear" rel="external">Register</a> </div>
			<div class="ui-block-b"><a href="/login/forgot_password.php" data-role="button" data-theme="c" data-icon="gear" rel="external">Forget?</a></div>	   
		</fieldset>
	</div>
</div>
</form>
<?php include ('../includes/footer.inc.php');  ?>