<?php
//start the session
session_start();

//include everything that is needed for this page.
require ('includes/config.inc.php'); 

//check the users permission to see if they have admin abilities
checkPermission('admin');

//set the title 
$title = 'Add User';

//add the header information
require ('includes/admin-header.inc.php');

$submitted = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Handle the form.

	// Need the database connection:
	require (MYSQL);
	
	// Trim all the incoming data:
	$trimmed = array_map('trim', $_POST);

	// Assume invalid values:
	$fn = $ln = $e = $p = FALSE;
	
	// Check for a first name:
	if (preg_match ('/^[A-Z \'.-]{2,20}$/i', $trimmed['first_name'])) {
		$fn = mysqli_real_escape_string ($dbc, $trimmed['first_name']);
	} else {
		echo '<p class="error">Please enter your first name!</p>';
	}

	// Check for a last name:
	if (preg_match ('/^[A-Z \'.-]{2,40}$/i', $trimmed['last_name'])) {
		$ln = mysqli_real_escape_string ($dbc, $trimmed['last_name']);
	} else {
		echo '<p class="error">Please enter your last name!</p>';
	}
	
	// Check for an email address:
	if (filter_var($trimmed['email'], FILTER_VALIDATE_EMAIL)) {
		$e = mysqli_real_escape_string ($dbc, $trimmed['email']);
	} else {
		echo '<p class="error">Please enter a valid email address!</p>';
	}

	// Check for a password and match against the confirmed password:
	if (preg_match ('/^\w{4,20}$/', $trimmed['password1']) ) {
		if ($trimmed['password1'] == $trimmed['password2']) {
			$p = mysqli_real_escape_string ($dbc, $trimmed['password1']);
		} else {
			echo '<p class="error">Your password did not match the confirmed password!</p>';
		}
	} else {
		echo '<p class="error">Please enter a valid password!</p>';
	}
	
	if ($fn && $ln && $e && $p) { // If everything's OK...

		// Make sure the email address is available:
		$q = "SELECT user_id FROM users WHERE email='$e'";
		$r = mysqli_query ($dbc, $q) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));
		
		if (mysqli_num_rows($r) == 0) { // Available.

			// Add the user to the database:
			$q = "INSERT INTO users (email, pass, first_name, last_name, registration_date) VALUES ('$e', SHA1('$p'), '$fn', '$ln', NOW() )";
			$r = mysqli_query ($dbc, $q) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));
			$submitted = true;
			
		} else { // The email address is not available.
			echo '<p class="error">That email address has already been registered. If you have forgotten your password, use the link at right to have your password sent to you.</p>';
		}
		
	} else { // If one of the data tests failed.
		echo '<p class="error">Please try again.</p>';
	}

	mysqli_close($dbc);

}
?>
<h2>Add New User</h2></h1>

<? 
if ($submitted == false) { ?>
	<div>
	<form action="admin-adduser.php" method="post" id="adminform">
		<p><label for="first_name">First Name:</label>
	    <input placeholder="(required)" type="text" name="first_name" maxlength="20" class="required" value="<?php if (isset($trimmed['first_name'])) echo $trimmed['first_name']; ?>" /></p>
	    <p><label for="last_name">Last Name:</label>
	    <input placeholder="(required)" type="text" name="last_name" maxlength="40" class="required" value="<?php if (isset($trimmed['last_name'])) echo $trimmed['last_name']; ?>" /></p>
	    <p><label for="email">Email:</label>
	    <input placeholder="(required)" type="email" name="email" maxlength="80" id="email" class="required email" value="<?php if (isset($trimmed['email'])) echo $trimmed['email']; ?>" /></p>
	    <p><label for="pass">Password:</label>
	    <input placeholder="(required)" type="password" name="password1" maxlength="20" class="required" value="<?php if (isset($trimmed['password1'])) echo $trimmed['password1']; ?>" /></p>
	    <p><label for="pass">Confirm Password:</label>
	    <input placeholder="(required)" type="password" name="password2" maxlength="20" class="required" value="<?php if (isset($trimmed['password2'])) echo $trimmed['password2']; ?>" /></p>
		<p><button type="submit" name="submit" value="Save">Save</button></p>
	</form>
	</div>
<? 
} else {
	echo '<p>'.$e.' has been added.</p>';
} 
?>
</body>
</html>