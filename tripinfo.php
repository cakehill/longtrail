<?php
//start the session
session_start();

//start the buffer
ob_start();

//include everything that is needed for this page.
require ('includes/config.inc.php'); 

//check to make sure they can access this area
checkPermission('user');

//set the page title
$title = 'Add Trip';

//include the header info
include('includes/header.inc.php');

// Need the database connection:
require (MYSQL);
	
?>

<body> 
<div data-role="page">
	<div data-role="header">
		<h1>Trip Info</h1>
	</div>
	<div data-role="content" data-theme="c">
<?
if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Handle the form.

	// Trim all the incoming data:
	$trimmed = array_map('trim', $_POST);
	
	// Assume invalid values:
	$tn = FALSE;
		
	// Check for a trip name:
	if (!isset($trimmed['trip_name'])) {
		echo '<p class="error">Please enter your trip name!</p>';
	} else {
		$tn = strip_tags($trimmed['trip_name']);
	}

	if ($tn) { // If everything's OK...
		$vd = strip_tags($trimmed['visit_date']);
		
		$stmt = $dbc->prepare("INSERT INTO trips (trip_name, users_id, visit_date, shelters_id) VALUES (?, ?, ?, ?)");
		$stmt->bind_param("sisi", $tn, $_SESSION['user_id'], $vd, $trimmed['sheltersId']);
		$stmt->execute();
		$stmt->close();

		// Redirect the user:
		$url = BASE_URL . 'details.php?id='.$dbc->insert_id; // Define the URL.
		$dbc->close();
		ob_end_clean(); // Delete the buffer.
		header("Location: $url");
		exit(); // Quit the script.
	}
	mysqli_close($dbc);
}
?>

<!-- 
date plugin: http://dev.jtsage.com/jQM-DateBox/demos/calendar/#main
 -->
		
<form action="tripinfo.php" method="post" data-ajax="false" id="tripInfoForm">
		<div data-role="fieldcontain">
	         <label for="trip_name">Trip Name:</label>
	         <input type="text" maxlength="40" name="trip_name" id="trip_name" class="required" value=""  />
		</div>
		<div data-role="fieldcontain">
			<label for="visit_date">Date of Visit:</label>
			<input name="visit_date" id="visit_date" type="date" class="required date" data-role="datebox" data-options='{"mode": "calbox"}'>
		</div>
<?		
		$q = "SELECT shelters_name, shelters_id FROM shelters ORDER BY shelters_name ASC";		
		$r = @mysqli_query ($dbc, $q); // Run the query.

		// Count the number of returned rows:
		$num = mysqli_num_rows($r);

		if ($num > 0) { // If it ran OK, display the records.
			echo '<div data-role="fieldcontain">';
			echo '<label for="sheltersId" class="select">Shelter</label>';
			echo '<select name="sheltersId" id="sheltersId">';
			while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
				echo '<option value="'.$row['shelters_id'].'">'.$row['shelters_name'].'</option>';
			}
		echo '</select>';
		echo '</div>';
		} else { // If no records were returned.
			echo '<p>There are no shelters to choose from.</p>';
	}
?>
		<button type="submit" data-theme="c" name="submit" value="submit-value">Save</button>
	</form>
	</div><!-- /content -->
</div>

<?php
include('includes/footer.inc.php'); 
?>