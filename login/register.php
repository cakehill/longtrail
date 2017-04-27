<?php # Script 18.8 - login.php
// This is the register page for the site.

session_start();

require ('../includes/config.inc.php'); 
$page_title = 'Login';
include ('../includes/header.inc.php'); 
?>
<body>
<div data-role="page" data-theme="c">
	<div data-role="header">
		<h1>Register</h1>
	</div>
	<div data-role="content" data-theme="c">	

<?

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

			// Create the activation code:
			//$a = md5(uniqid(rand(), true));

			// Add the user to the database:
			$q = "INSERT INTO users (email, pass, first_name, last_name, registration_date) VALUES ('$e', SHA1('$p'), '$fn', '$ln', NOW() )";
			$r = mysqli_query ($dbc, $q) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));

			if (mysqli_affected_rows($dbc) == 1) { // If it ran OK.
				$insertId = mysqli_insert_id($dbc);
				//create some default text for an initial/welcome alert
				$alertText = "Thanks for registering with the site. Please review the guide section on the homepage for more information on how to use the site.";
				$alertSubject = "Welcome " .$fn;
			
				// Add the user to the database:
				$q1 = "INSERT INTO alerts (subject, message, from_id, to_id) VALUES ('$alertSubject', '$alertText', '2', '$insertId')";
				$r1 = mysqli_query ($dbc, $q1) or trigger_error("Query: $q1\n<br />MySQL Error: " . mysqli_error($dbc));
				
				// Send the email:
				$body = "A new user has registered:\n\n" . $trimmed['email'];
				mail('johnpcahill@gmail.com', 'New Registration', $body, 'From: admin@johnpcahill.com');
				
				// Finish the page:
				echo 'Thank you for registering! An administrator will review the account and you will receive an email when it is active.</div></div>';
				include ('../includes/footer.inc.php'); // Include the HTML footer.
				exit(); // Stop the page.
				
			} else { // If it did not run OK.
				echo '<p class="error">You could not be registered due to a system error. We apologize for any inconvenience.</p>';
			}
		} else { // The email address is not available.
			echo '<p class="error">That email address has already been registered. If you have forgotten your password, use the link at right to have your password sent to you.</p>';
		}
		
	} else { // If one of the data tests failed.
		echo '<p class="error">Please try again.</p>';
	}

	mysqli_close($dbc);

} // End of the main Submit conditional.
?>
<form action="register.php" method="post" data-ajax="false" id="registerform">
		<div data-role="fieldcontain">
	         <!--<label for="first_name">First Name:</label>-->
	         <input placeholder="First Name (required)" type="text" name="first_name" maxlength="20" class="required" value="<?php if (isset($trimmed['first_name'])) echo $trimmed['first_name']; ?>" />
		</div>
		<div data-role="fieldcontain">
	         <!--<label for="last_name">Last Name:</label>-->
	         <input placeholder="Last Name (required)" type="text" name="last_name" maxlength="40" class="required" value="<?php if (isset($trimmed['last_name'])) echo $trimmed['last_name']; ?>" />
		</div>
		<div data-role="fieldcontain">
	         <!--<label for="email">Email:</label>-->
	         <input placeholder="Email Address (required)" type="email" name="email" maxlength="80" id="email" class="required email" value="<?php if (isset($trimmed['email'])) echo $trimmed['email']; ?>" />
		</div>
		<div data-role="fieldcontain">
	         <!--<label for="pass">Password:</label>-->
	         <input placeholder="Password (required)" type="password" name="password1" maxlength="20" class="required" value="<?php if (isset($trimmed['password1'])) echo $trimmed['password1']; ?>" />
		</div>
		<div data-role="fieldcontain">
	         <!--<label for="pass">Confirm Password:</label>-->
	         <input placeholder="Password again (required)" type="password" name="password2" maxlength="20" class="required" value="<?php if (isset($trimmed['password2'])) echo $trimmed['password2']; ?>" />
		</div>

		<button type="submit" data-theme="c" name="submit" value="Register">Register</button>
	</div>
</form>
</div>


<?php include ('../includes/footer.inc.php');  ?>