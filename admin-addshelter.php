<?php
//start the session
session_start();

//include everything that is needed for this page.
require ('includes/config.inc.php'); 

//check the users permission to see if they have admin abilities
checkPermission('admin');

//set the title
$title = 'Add Shelter';

//add the header information
require ('includes/admin-header.inc.php');
?>
<body>

<?
$submitted = false;
	
if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Handle the form.

	// Need the database connection:
	require (MYSQL);

	// Assume invalid values:
	$sn = FALSE;
	
	// Trim all the incoming data:
	$trimmed = array_map('trim', $_POST);
	
	// Check for a shelter name:
	if (!empty($trimmed['shelterName'])) {
		$sn = strip_tags($trimmed['shelterName']);
	} else {
		echo '<p class="error">Please enter a sheltername!</p>';
	}
	/*
	// Check for latitude:
	if (preg_match ('/^[A-Z \'.-]{2,20}$/i', $trimmed['latitude'])) {
		$lt = mysqli_real_escape_string ($dbc, $trimmed['latitude']);
	} else {
		echo '<p class="error">Please enter latitude!</p>';
	}

	// Check for longitude:
	if (preg_match ('/^[A-Z \'.-]{2,20}$/i', $trimmed['longitude'])) {
		$ll = mysqli_real_escape_string ($dbc, $trimmed['longitude']);
	} else {
		echo '<p class="error">Please enter longitude!</p>';
	}*/

	$ll = strip_tags($trimmed['longitude']);
	$lt = strip_tags($trimmed['latitude']);
	$c = strip_tags($trimmed['comments']);

  	if ($sn) { // If everything's OK...

		$stmt = $dbc->prepare("INSERT INTO shelters (shelters_name, latitude, longitude, comments) VALUES (?, ?, ?, ?)");
		$stmt->bind_param('ssss', $sn, $lt, $ll, $c);
		$stmt->execute();
		$stmt->close();
		$dbc->close();
		

  		// prepare update query
		//$q = "INSERT INTO shelters (shelters_name, latitude, longitude, comments) VALUES ('$sn', '$lt', '$ll', '$c')";
		//$r = mysqli_query ($dbc, $q) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));
		//
		$submitted = true;
	}
	mysqli_close($dbc);

}
?>
<h2>Add New Shelter</h2></h1>

<? 
if ($submitted == false) { ?>
	<div>
	<form action="admin-addshelter.php" method="post" id="adminform">
		<label for="shelterName">Shelter Name:</label>
		<input type="text" name="shelterName" class="required" maxlength="50" id="shelterName" value="<?php if (isset($trimmed['shelterName'])) echo $trimmed['shelterName']; ?>" />
		<br />
		<label for="latitude">Latitude:</label>
		<input type="text" name="latitude" maxlength="50" id="latitude" value="<?php if (isset($trimmed['latitude'])) echo $trimmed['latitude']; ?>" />
		<br />
		<label for="longitude">Longitude:</label>
		<input type="text" name="longitude" maxlength="50" id="longitude" value="<?php if (isset($trimmed['longitude'])) echo $trimmed['longitude']; ?>" />
		<br />
		<label for="comments">Shelter comments:</label>
		<textarea cols="45" rows="8" name="comments" id="comments"><?php if (isset($trimmed['comments'])) echo $trimmed['comments']; ?></textarea><br />
		<button type="submit" name="submit" value="submit-value">Save</button>
	</form>
	</div>
<? 
} else {
	echo '<p>'.$sn.' has been added.</p>';
} 
?>
</body>
</html>