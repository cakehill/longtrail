<?php
//start the session
session_start();

//include everything that is needed for this page.
require ('../includes/config.inc.php'); 

//check to make sure they can access this area
checkPermission('user');

//set page title
$title = 'Change Password';

//include header info
include ('../includes/header.inc.php'); 
?>
<body>
<form action="change_password.php" method="post" id="loginform">
<div data-role="page" data-theme="c">
	<div data-role="header">
		<h1>Change Password</h1>
		<a href="/" class="ui-btn-right" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a> 
	</div>
	<div data-role="content" data-theme="c">
<?
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	require (MYSQL);
			
	// Check for a new password and match against the confirmed password:
	$p = FALSE;
	if (preg_match ('/^(\w){4,20}$/', $_POST['password1']) ) {
		if ($_POST['password1'] == $_POST['password2']) {
			$p = mysqli_real_escape_string ($dbc, $_POST['password1']);
		} else {
			echo '<p class="error">Your password did not match the confirmed password!</p>';
		}
	} else {
		echo '<p class="error">Please enter a valid password!</p>';
	}
	
	if ($p) { // If everything's OK.

		// Make the query:
		$q = "UPDATE users SET pass=SHA1('$p') WHERE user_id={$_SESSION['user_id']} LIMIT 1";	
		$r = mysqli_query ($dbc, $q) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));
		if (mysqli_affected_rows($dbc) == 1) { // If it ran OK.

			// Send an email, if desired.
			echo '<p>Thanks, your password has now been changed.</p>';
			echo '<a href="/" data-role="button" data-icon="home">Back to home screen</a>';
			mysqli_close($dbc); // Close the database connection.
			include ('../includes/footer.inc.php');
			exit();
			
		} else { // If it did not run OK.
		
			echo '<p class="error">Your password has not been changed. Make sure it is different than the current password.</p>'; 

		}

	} else { // Failed the validation test.
		echo '<p class="error">Please try again.</p>';		
	}
	
	mysqli_close($dbc); // Close the database connection.

} // End of the main Submit conditional.
?>
		<p>Please enter your new password and also confirm it. Must be between 4 and 20 characters long.</p>
		<div data-role="fieldcontain">
	         <label for="pass" class="ui-hidden-accessible">New Password:</label>
	         <input placeholder="New Password" type="password" name="password1" id="pass" maxlength="20" class="required" value="<?php if (isset($_POST['pass'])) echo $_POST['pass']; ?>" />
		</div>
		<div data-role="fieldcontain">
	         <label for="pass" class="ui-hidden-accessible">Confirm Password:</label>
	         <input placeholder="Confirm Password" type="password" name="password2" id="pass2" maxlength="20" class="required" value="<?php if (isset($_POST['pass'])) echo $_POST['pass']; ?>" />
		</div>
		<button type="submit" data-theme="c" name="submit" value="submit-value">Submit</button>
	</div>
</div>
</form>

<?php include ('../includes/footer.inc.php');  ?>