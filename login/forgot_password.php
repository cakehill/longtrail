<?php # Script 18.10 - forgot_password.php
// This page allows a user to reset their password, if forgotten.
session_start();
ob_start();

require ('../includes/config.inc.php'); 
$title = 'Forgot Password';
include ('../includes/header.inc.php'); 
?>
<body>
<form action="forgot_password.php" method="post" id="loginform">
<div data-role="page" data-theme="c">
	<div data-role="header">
		<h1>Reset</h1>
	</div>
	<div data-role="content" data-theme="c">	
<?
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	require (MYSQL);

	// Assume nothing:
	$uid = FALSE;

	// Validate the email address...
	if (!empty($_POST['email'])) {

		// Check for the existence of that email address...
		$q = 'SELECT user_id FROM users WHERE email="'.  mysqli_real_escape_string ($dbc, $_POST['email']) . '"';
		$r = mysqli_query ($dbc, $q) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));
		
		if (mysqli_num_rows($r) == 1) { // Retrieve the user ID:
			list($uid) = mysqli_fetch_array ($r, MYSQLI_NUM); 
		} else { // No database match made.
			echo '<p class="error">The submitted email address does not match those on file!</p>';
		}
		
	} else { // No email!
		echo '<p class="error">You forgot to enter your email address!</p>';
	} // End of empty($_POST['email']) IF.
	
	if ($uid) { // If everything is OK.

		// Create a new, random password - 10 characters long:
		$p = substr ( md5(uniqid(rand(), true)), 3, 10);

		// Update the table:
		$q = "UPDATE users SET pass=SHA1('$p') WHERE user_id=$uid LIMIT 1";
		$r = mysqli_query ($dbc, $q) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));

		if (mysqli_affected_rows($dbc) == 1) { // If it ran OK.
		
			// Send an email:
			$body = "Your password for http://longtrail.johnpcahill.com has been temporarily changed to '$p'. Please log in using this new password and this email address. Then you may change it to something more familiar.";
			mail ($_POST['email'], 'Your temporary password.', $body, 'From: johnpcahill@gmail.com');
			
			// Print a message and wrap up:
			echo '<p>Your password is now changed. You will receive your new password at the email address with which you registered. Once you have logged in, you may change it by clicking on the "options" button.</p>';
			echo '<a rel="external" href="/" data-role="button" data-icon="home">Back to login screen</a>';
			mysqli_close($dbc);
			include ('../includes/footer.inc.php');
			exit(); // Stop the script.
			
		} else { // If it did not run OK.
			echo '<p class="error">Your password could not be changed. We apologize for the inconvenience.</p>'; 
		}

	} else { // Failed the validation test.
		echo '<p class="error">Please try again.</p>';
	}

	mysqli_close($dbc);
}
?>

<h1>Reset Password</h1>
<p>Enter your email and we will send you a temporary password.</p> 
<form action="forgot_password.php" method="post">
	<div data-role="fieldcontain">
		<label for="email" class="ui-hidden-accessible">Email:</label>
		<input placeholder="Email address" type="email" name="email" id="email" class="required email" value="<?php if (isset($_POST['email'])) echo $_POST['email']; elseif (isset($_COOKIE['email'])) echo $_COOKIE['email']; ?>" />
		<button type="submit" data-theme="c" name="submit" value="submit-value">Submit</button>
	</div>
</form>

<?php include ('../includes/footer.inc.php');  ?>