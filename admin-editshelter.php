<?php
//start the session
session_start();

//include everything that is needed for this page.
require ('includes/config.inc.php'); 

//check the users permission to see if they have admin abilities
checkPermission('admin');

if (isset($_GET['id'])) {
	$shelterId = $_GET['id'];
} elseif (isset($_POST['id'])) {
	$shelterId = $_POST['id'];
}

//set the title
$title = 'Edit Shelter';

//get the header info
require ('includes/admin-header.inc.php');

// Need the database connection:
require (MYSQL);
$done = false;
$submitted = false;

$stmt = $dbc->stmt_init();

if (isset($shelterId) && !$_POST) {
  // prepare SQL query
  $sql = 'SELECT shelters_name, latitude, longitude, comments
   FROM shelters WHERE shelters_id = ?';
  if ($stmt->prepare($sql)) {
	// bind the query parameter
	$stmt->bind_param('i', $shelterId);
	// bind the results to variables
	$stmt->bind_result($shelterName, $latitude, $longitude, $comments);
	// execute the query, and fetch the result
	$OK = $stmt->execute();
	$stmt->fetch();
	}
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Handle the form.
  	
  	// Assume invalid values:
	$shelterName = FALSE;

	// Trim all the incoming data:
	$trimmed = array_map('trim', $_POST);
	
	$id = mysqli_real_escape_string($dbc, $_POST['id']);
	$shelterName = strip_tags($trimmed['shelterName']);
  	$latitude = strip_tags($trimmed['latitude']);
  	$longitude = strip_tags($trimmed['longitude']);
  	$comments = strip_tags($trimmed['comments']);
  	
  	if (empty($shelterName)) {
  		echo '<p class="error">Please enter a shelter name!</p>';
  	}
  	
  	if ($shelterName) { // If everything's OK...
  		// prepare update query
  		$sql = 'UPDATE shelters SET shelters_name = ?,
  		latitude = ?,
  		longitude = ?,
  		comments = ?
		WHERE shelters_id = ?';
  		if ($stmt->prepare($sql)) {
			$stmt->bind_param('ssssi', $shelterName, $latitude, $longitude, $comments, $id);
			$done = $stmt->execute();
		}
		// Redirect the user:
		$submitted = true;
	}
	mysqli_close($dbc);
}
?>
<h2>Edit Shelter Info</h2>
<? 
if ($submitted == false) { ?>
<div>
	<form action="admin-editshelter.php" method="post" id="adminform">
	<input type="hidden" name="id" value="<? echo $shelterId ?>" />	
	<label for="shelterName">Shelter Name:</label>
	<input type="text" name="shelterName" id="shelterName" class="required" maxlength="50" value="<? if (isset($shelterName)) echo $shelterName; ?>"  /><br />
    <label for="latitude">Latitude:</label>
    <input type="text" name="latitude" id="latitude" maxlength="50" value="<? if (isset($latitude)) echo $latitude; ?>"  /><br />
    <label for="longitude">Longitude:</label>
    <input type="text" name="longitude" id="longitude" maxlength="50" value="<? if (isset($longitude)) echo $longitude; ?>"  /><br />
    <label for="comments">Shelter comments:</label>
	<textarea cols="45" rows="8" name="comments" id="comments"><? if (isset($comments)) echo $comments; ?></textarea><br />
	<button type="submit" name="submit" value="submit-value">Save</button>
</form>
</div>
<? } else {
echo '<p>Your changes have been made.</p>';
} ?>
</body>
</html>