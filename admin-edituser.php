<?php
//start the session
session_start();

//include everything that is needed for this page.
require ('includes/config.inc.php'); 

//check the users permission to see if they have admin abilities
checkPermission('admin');

if (isset($_GET['id'])) {
	$userId = $_GET['id'];
} elseif (isset($_POST['id'])) {
	$userId = $_POST['id'];
}

//set the title
$title = 'Edit User';

//get the header info
require ('includes/admin-header.inc.php');

// Need the database connection:
require (MYSQL);
$done = false;
$submitted = false;

$stmt = $dbc->stmt_init();

if (isset($userId) && !$_POST) {
	// prepare SQL query
	$sql = 'SELECT first_name, last_name, email, active, pass, user_level, comments FROM users WHERE user_id = ?';
	if ($stmt->prepare($sql)) {
		// bind the query parameter
		$stmt->bind_param('i', $userId);
		// bind the results to variables
		$stmt->bind_result($firstName, $lastName, $email, $active, $password, $userLevel, $comments);
		// execute the query, and fetch the result
		$OK = $stmt->execute();
		$stmt->fetch();
	}
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Handle the form.
  	
  	//send the user a notification that account is active
  	if (!empty($_POST['notify'])) {
		$body = "Your Long Trail Shelter Maintenance account has been activated:\n\nPlease visit http://longtrail.johnpcahill.com/ to login.";
		mail($_POST['email'], 'Long Trail Account is now active', $body, 'From: admin@johnpcahill.com');
		$notificationText = "Notification has been sent!  Thanks.";
		$submitted = true;
	}
  	
  	if (!empty($_POST['submit'])) {  		  		
  		if (!empty($_POST['password'])) {
  			$p = sha1(mysqli_real_escape_string($dbc, $_POST['password']));
  		} else {
			$sql = 'SELECT pass FROM users WHERE user_id = ?';
			if ($stmt->prepare($sql)) {
				// bind the query parameter
				$stmt->bind_param('i', $userId);
				// bind the results to variables
				$stmt->bind_result($p);
				// execute the query, and fetch the result
				$OK = $stmt->execute();
				$stmt->fetch();
			}
  		}

		// Trim all the incoming data:
		$trimmed = array_map('trim', $_POST);

		// Assume invalid values at first
		$firstName = $lastName = $email = FALSE;
		
		//set up variables
		$firstName = strip_tags($trimmed['firstName']);
		$lastName = strip_tags($trimmed['lastName']);
		$comments = strip_tags($trimmed['comments']);
		$active = mysqli_real_escape_string ($dbc, $_POST['active']);
		$userLevel = mysqli_real_escape_string ($dbc, $_POST['userLevel']);
		
		//do a quick check to see if fields are empty
	  	if (empty($firstName)) {
  			echo '<p class="error">Please enter a first name!</p>';
  		}

	  	if (empty($lastName)) {
  			echo '<p class="error">Please enter a last name!</p>';
  		}

	  	// Check for an email address:
		if (!filter_var($trimmed['email'], FILTER_VALIDATE_EMAIL)) {			
			echo '<p class="error">Please enter a valid email address!</p>';
		} else {
			$email = mysqli_real_escape_string ($dbc, $trimmed['email']);
		}

  		// If everything's OK...
  		if ($firstName && $lastName && $email) {
  			// prepare update query
  			$sql = 'UPDATE users SET first_name = ?,
  			last_name = ?,
  			email = ?,
  			active = ?,
  			pass = ?, 
  			user_level = ?,
  			comments = ?
			WHERE user_id = ?';
  				if ($stmt->prepare($sql)) {
					$stmt->bind_param('sssisisi', $firstName, $lastName, $email, $active, $p, $userLevel, $comments, $userId);
					$done = $stmt->execute();
					$submitted = true;	
				}
			}		
		}
	mysqli_close($dbc);		
}

?>
<h2>Edit User Info</h2></h1>

<?
//the first time through this page
if ($submitted == false) { ?>
<div>
	<form action="admin-edituser.php" method="post" id="adminform">
		<input type="hidden" name="id" value="<? echo $userId ?>" />
		<label for="firstName">First Name:</label>
	    <input type="text" name="firstName" id="firstName" class="required" maxlength="20" value="<? if (isset($firstName)) echo $firstName; ?>"  /><br />

		<label for="lastName">Last Name:</label>
		<input type="text" name="lastName" id="lastName" class="required" maxlength="40" value="<? if (isset($lastName)) echo $lastName; ?>"  /><br />

		<label for="email">Email:</label>
		<input type="text" name="email" id="email" class="required email" maxlength="80" value="<? if (isset($email)) echo $email; ?>"  /><br />

		<label for="password">Password:</label>
		<input type="password" name="password" id="password" /><br />
	         	         
		<label for="active">Active</label>
		<select name="active" id="active">
		<option value="0" <? if (!$active) echo 'selected="selected"'; ?>>no</option>
		<option value="1" <? if ($active) echo 'selected="selected"'; ?>>yes</option>
		</select> <br />

		<label for="userLevel">User Level</label>
		<select name="userLevel" id="userLevel">
		<option value="0" <? if (!$userLevel) echo 'selected="selected"'; ?>>non-admin</option>
		<option value="1" <? if ($userLevel) echo 'selected="selected"'; ?>>admin</option>
		</select> <br />

		<label for="comments">Comments:</label>
		<textarea cols="45" rows="8" name="comments" id="comments"><? if (isset($comments)) echo $comments; ?></textarea><br />
		<button type="submit" name="submit" value="submit-value">Save</button>
	</form>
</div>
<? } else {?>

<p>Your changes have been made.</p>
<form action="admin-edituser.php" method="post">
	<input type="hidden" name="notify" value="yes" />	
	<input type="hidden" name="email" value="<? echo $_POST['email']; ?>" />	
	<button type="submit" name="submit-notify" value="submit-value">Send notification to user</button>
</form>
<p><? if (!empty($notificationText)) echo $notificationText; ?></p>
<p>Click outside box to close.</p>
<? } ?>
</body>
</html>