<?php
//start the session
session_start();

//include everything that is needed for this page.
require ('includes/config.inc.php'); 

//check the users permission to see if they have admin abilities
checkPermission('admin');

//set the title
$title = 'Trips';

//get the header info
require ('includes/admin-header.inc.php');

//get the admin menu
require ('includes/admin-menu.inc.php'); 

// Need the database connection:
require (MYSQL);

if (isset($_GET['id'])) {
	$userId = mysqli_real_escape_string($dbc, $_GET['id']);
} elseif (isset($_POST['id'])) {
	$userId = mysqli_real_escape_string($dbc, $_POST['id']);
}

//generate the where clause depending on how user got to the page.
//will either display trips for a certain shelter or certain user
$where = "";
if (isset($userId) && (!isset($_GET['shelterid']))) {
	$where = " WHERE trips.users_id = $userId AND trips.removed = '0'";
	$pageHeader = "<p>These trips are grouped by the selected user.</p>";
}elseif (isset($_GET['shelterid']) && (!isset($userId))) {
	$shelterId = mysqli_real_escape_string($dbc, $_GET['shelterid']);
	$where = "WHERE shelters.shelters_id = $shelterId AND trips.removed = '0'";
	$pageHeader = "<p>These trips are grouped by the selected shelter.</p>";
}elseif (isset($_GET['removed'])){
	$where = "WHERE trips.removed = '1'";
	$pageHeader = "<p>These trips have been deleted by the user. Click edit to reactivate the trip.  To view all trips by user or shelter, please click into section above.</p>";
}else {
	$where = "WHERE trips.removed = '0' AND trips.submitted = '1'";
	$pageHeader = "<p>These are all the trips theat have been submitted by users.</p>";

}

// Make the query to grab all the trip info joining users and shelters tables
$q = "SELECT users.first_name, users.last_name, trips.trips_id, trips.trip_name, trips.visit_date, trips.submitted, shelters.shelters_name FROM trips INNER JOIN users ON trips.users_id = users.user_id INNER JOIN shelters ON trips.shelters_id = shelters.shelters_id $where ORDER BY trips.visit_date DESC";	
$r = @mysqli_query ($dbc, $q); // Run the query.

// Count the number of returned rows:
$num = mysqli_num_rows($r);

if ($num > 0) { // If it ran OK, display the records.
	echo '<p class="subnav"><a href="admin-trips.php?removed">All deleted trips</a></p>';
	echo '<h1>Trips</h1>';
	if (isset($pageHeader)) echo $pageHeader;
	echo '<table cellpadding="0" cellspacing="0" border="0" class="display" id="tripsTable" width="100%">';
	echo '<thead><tr><th>User</th><th>Shelter</th><th>Trip Name</th><th>Date</th><th>Trip Status</th><th>Action</th></tr></thead><tbody>';
	while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
		echo '<tr><td>'.$row['first_name'].' '.$row['last_name'].'</td><td>'.$row['shelters_name'].'</td><td>'.$row['trip_name'].'</td><td>' . $row['visit_date'].'</td><td>' . checkTripStatus($row['submitted']).'</td><td><a href="details.php?id='.$row['trips_id'].'">edit</a>, <a target="_blank" href="includes/pdf.php?id='.$row['trips_id'].'">view pdf</a></td></tr>';
	}
	echo '</tbody></table>';
} else { // If no records were returned.
	echo '<p>There are not yet any trips entered.</p>';
}

mysqli_close($dbc);

require ('includes/admin-footer.inc.php');
?>