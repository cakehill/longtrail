<?php
//start the session
session_start();

//include everything that is needed for this page.
require ('includes/config.inc.php'); 

//check the users permission to see if they have admin abilities
checkPermission('admin');

//set the title
$title = 'Add Alert';

//add the header information
require ('includes/admin-header.inc.php');

$submitted = false;
$userId = "";

if (isset($_GET['id'])) {
	$userId = $_GET['id'];
} elseif (isset($_POST['id'])) {
	$userId = $_POST['id'];
}

// Need the database connection:
require (MYSQL);

if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Handle the form.

	// Assume invalid values:
	$as = $am = FALSE;
	
	// Trim all the incoming data:
	$trimmed = array_map('trim', $_POST);
	
	// Check for a subject:
	if (preg_match ('/^[A-Z \'.-]{2,25}$/i', $trimmed['alertSubject'])) {
		$as = strip_tags($trimmed['alertSubject']);
	} else {
		echo '<p class="error">Please enter a subject!</p>';
	}

	// Check for message:
	if (!empty($trimmed['alertMessage'])) {
		$am = strip_tags($trimmed['alertMessage']);
	} else {
		echo '<p class="error">Please enter a message!</p>';
	}
	
	$si = $_SESSION['user_id'];
	$toid = strip_tags($trimmed['alertTo']);
	
	//check to see if this is going to everyone
	if ($toid == '-1') {
		$toAll = '1';
	} else {
		$toAll = '0';
	}

  	if ($as && $am) { // If everything's OK...
  		$stmt = $dbc->prepare("INSERT INTO alerts (subject, message, from_id, to_id, to_all) VALUES (?, ?, ?, ?, ?)");
		$stmt->bind_param('ssiii', $as, $am, $si, $toid, $toAll);
		$stmt->execute();
		$stmt->close();
		$dbc->close();
 		$submitted = true;
	}
	mysqli_close($dbc);
}
?>
<h2>Add New Alert</h2></h1>

<? 
if ($submitted == false) { 
?>
	<div>
		<form action="admin-addalert.php" method="post" id="adminform">
			<p><label for="alertSubject">Subject:</label>
			<input type="text" maxlength="25" class="required" name="alertSubject" id="alertSubject" value="<?php if (isset($trimmed['alertSubject'])) echo $trimmed['alertSubject']; ?>" />
			</p>
			<?
			//fill the select box with all active users
			$q = "SELECT user_id, first_name, last_name, email FROM users WHERE active = '1' ORDER BY last_name ASC";		
			$r = @mysqli_query ($dbc, $q) or die("MySQL error: " . mysqli_error($dbc) . "<hr>\nQuery: $q"); // Run the query.

			// Count the number of returned rows:
			$num = mysqli_num_rows($r);

			if ($num > 0) { // If it ran OK, display the records.
				echo '<label for="alertTo" class="select">Send to: </label>';
				echo '<select name="alertTo" id="alertTo">';
				echo '<option value="-1">Send to all users</option>';
				while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
					echo '<option value="'.$row['user_id'].'"';
					if ($userId == $row['user_id']) {
						echo ' selected';
					}elseif (isset($trimmed['alertTo'])) {
						if ($trimmed['alertTo'] == $row['user_id']) {
							echo ' selected';
						}
					}				
					echo '>'.$row['first_name'].' '.$row['last_name'].' ('.$row['email'].')</option>';
				}
				echo '</select>';
			} else { // If no records were returned.
				echo '<p>There are no users setup.</p>';
			} 
?>
			<p><label for="alertMessage">Message:</label>
			<textarea cols="40" rows="8" class="required" name="alertMessage" id="alertMessage"><?php if (isset($trimmed['alertMessage'])) echo $trimmed['alertMessage']; ?></textarea><br />
			</p>
			<p><button type="submit" name="submit" value="submit-value">Save</button></p>
		</form>
	</div>
<? 
} else {
	echo '<p>Alert has been sent.</p>';
} 
?>
</body>
</html>