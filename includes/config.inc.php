<?php 

// ********************************** //
// ************ SETTINGS ************ //

// Flag variable for site status:
define('LIVE', FALSE);

// Admin contact address:
define('EMAIL', '');

// Site URL (base for all redirections):
define ('BASE_URL', '');

// Location of the MySQL connection script:
define ('MYSQL', '');

// Adjust the time zone for PHP 5.1 and greater:
date_default_timezone_set ('US/Eastern');

// ************ SETTINGS ************ //
// ********************************** //


// ****************************************** //
// ************ ERROR MANAGEMENT ************ //

// Create the error handler:
function my_error_handler ($e_number, $e_message, $e_file, $e_line, $e_vars) {

	// Build the error message:
	$message = "An error occurred in script '$e_file' on line $e_line: $e_message\n";
	
	// Add the date and time:
	$message .= "Date/Time: " . date('n-j-Y H:i:s') . "\n";
	
	if (!LIVE) { // Development (print the error).

		// Show the error message:
		echo '<div class="error">' . nl2br($message);
	
		// Add the variables and a backtrace:
		echo '<pre>' . print_r ($e_vars, 1) . "\n";
		debug_print_backtrace();
		echo '</pre></div>';
		
	} else { // Don't show the error:

		// Send an email to the admin:
		$body = $message . "\n" . print_r ($e_vars, 1);
		mail(EMAIL, 'Site Error!', $body, 'From: email@example.com');
	
		// Only print an error message if the error isn't a notice:
		if ($e_number != E_NOTICE) {
			echo '<div class="error">A system error occurred. We apologize for the inconvenience.</div><br />';
		}
	} // End of !LIVE IF.

} // End of my_error_handler() definition.

// Use my error handler:
set_error_handler ('my_error_handler');

// ************ ERROR MANAGEMENT ************ //
// ****************************************** //

//determine which status to show in the display table
function checkTripStatus($passedLevel) {
	if (!$passedLevel) {
		$passedLevel = 'In Progress';
	}else {
		$passedLevel = 'Submitted';
	}
	return $passedLevel;
}

//decide what to print out on the table
function checkActive($passedActive) {
	if (!$passedActive) {
		$passedActive = 'no';
	}else {
		$passedActive = 'yes';
	}
	return $passedActive;
}

//decide what to print out on the table
function checkLevel($passedLevel) {
	if (!$passedLevel) {
		$passedLevel = 'non-admin';
	}else {
		$passedLevel = 'admin';
	}
	return $passedLevel;
}

//decide whether to include an alert link
function sendAlert ($sendTheAlert, $theId) {
	if (!$sendTheAlert) {
		$sendTheAlert = '';
	}else {
		$sendTheAlert = ', <a href="admin-addalert.php?id='.$theId.'" class="more">send alert</a>';
	}
	return $sendTheAlert;
}

//dashboard function - finds the recent trips that were submitted
function getSubmitted ($num, $r) {
	if ($num > 0) { // If it ran OK, display the records.
		echo '<table id="sheltersTableHome" cellpadding="0" cellspacing="0" border="0" class="display" width="500">';
		echo '<thead><tr><th>Trip Name</th><th>Trip Date</th><th>User</th><th>Shelter</th></tr></thead><tbody>';

		while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
			echo "<tr><td><a href='details.php?id=".$row['trips_id']."'>".$row['trip_name']."</a> (<a target='_blank' href='includes/pdf.php?id=".$row['trips_id']."'>pdf</a>)</td><td>".$row['visit_date']."</td><td>".$row['first_name']." ".$row['last_name']."</td><td><a href='admin-trips.php?shelterid=".$row['shelters_id']."'>".$row['shelters_name']."</td></tr>";
		}
		echo '</tbody></table>';
	} else { // If no records were returned.
		echo '<p>No trips have been submitted yet.</p>';
	}
}

//dashboard function - finds the newest users
function getNewUsers ($num2, $r2) {
	if ($num2 > 0) { // If it ran OK, display the records.
		echo '<table cellpadding="0" cellspacing="0" border="0" class="display" id="usersTableHome" width="400">';
		echo '<thead><tr><th>User Name</th><th>Registration Date</th></tr></thead><tbody>';

		while ($row = mysqli_fetch_array($r2, MYSQLI_ASSOC)) {
			echo "<tr><td><a href='admin-edituser.php?id=".$row['user_id']."' class='more'>".$row['first_name']." ".$row['last_name']."</td><td>".$row['registration_date']."</td></tr>";
		}
		echo '</tbody></table>';
	} else { // If no records were returned.
		echo '<p>No users are marked as inactive.</p>';
	}
}

//dashboard function - finds the items marked as urgent
function getUrgentItems ($num1, $r1) {
	if ($num1 > 0) { // If it ran OK, display the records.
		echo '<table cellpadding="0" cellspacing="0" border="0" class="display" id="urgentTableHome" width="100%">';
		echo '<thead><tr><th>Trip Name</th><th>Trip Date</th><th>User</th><th>Shelter</th><th>Action Item</th></tr></thead><tbody>';

		while ($row = mysqli_fetch_array($r1, MYSQLI_ASSOC)) {
			echo "<tr><td><a href='details.php?id=".$row['trips_id']."'>".$row['trip_name']."</a> (<a target='_blank' href='includes/pdf.php?id=".$row['trips_id']."'>pdf</a>)</td><td>".$row['visit_date']."</td><td>".$row['first_name']." ".$row['last_name']."</td><td><a href='admin-trips.php?shelterid=".$row['shelters_id']."'>".$row['shelters_name']."</a></td><td>".nl2br($row['action_explain'])."</td></tr>";
		}
		echo '</tbody></table>';
	} else { // If no records were returned.
		echo '<p>No items are marked as urgent.</p>';
	}
}

//redirect the user
function redirect_user ($page = 'index.php') {
	// Start defining the URL...
	// URL is http:// plus the host name plus the current directory:
	$url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
	
	// Remove any trailing slashes:
	$url = rtrim($url, '/\\');
	
	// Add the page:
	$url .= '/' . $page;
	
	// Redirect the user:
	header("Location: $url");
	exit(); // Quit the script.

} // End of redirect_user() function.

function checkPermission($area) {
//check for admin status
if ($area == 'admin') {	
	//check to see if they have logged in and if they are able to access this area
	if (isset($_SESSION['user_level'])){
		if ($_SESSION['user_level'] != '1') {
		// Redirect the user
		redirect_user();
		}
	} else {
		// Redirect the user
		redirect_user();
	}
//or check for logged in session variable
} else {
	if (!isset($_SESSION['first_name'])) {
	// Redirect the user
	redirect_user();
	}
}
}