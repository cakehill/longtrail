<?php
//start the session
session_start();

//include everything that is needed for this page.
require ('includes/config.inc.php'); 

//check the users permission to see if they have admin abilities
checkPermission('admin');

//set the title
$title = 'Shelters';

//get the header info
require ('includes/admin-header.inc.php');

//get the admin menu
require ('includes/admin-menu.inc.php'); 
echo '<p class="subnav"><a href="admin-addshelter.php" class="more">Add shelter</a> | <a href="includes/csv.php">Export Shelter table as CSV</a></p>';
echo '<h1>Shelters</h1>';
// Need the database connection:
require (MYSQL);
// Make the query to list all the shelters
$q = "SELECT shelters_id, shelters_name, latitude, longitude FROM shelters ORDER BY shelters_name ASC";		
$r = @mysqli_query ($dbc, $q); // Run the query.

// Count the number of returned rows:
$num = mysqli_num_rows($r);

if ($num > 0) { // If it ran OK, display the records.
	echo '<table cellpadding="0" cellspacing="0" border="0" class="display" id="sheltersTable" width="100%">';
	echo '<thead><tr><th>Shelter Name</th><th>Longitude</th><th>Latitude</th><th>Action</th></tr></thead><tbody>';
	while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
		echo '<tr><td>'.$row['shelters_name'].'</td><td>' . $row['longitude'].'</td><td>'.$row['latitude'].'</td><td><a href="admin-editshelter.php?id='.$row['shelters_id'].'" class="more">edit info</a>, <a href="http://maps.google.com/maps?q=loc:'.$row['latitude'].','.$row['longitude'].'&z=11&t=p&output=embed" class="map">map</a>, <a href="admin-trips.php?shelterid='.$row['shelters_id'].'">all trips</a></td></tr>';
	}
	echo '</tbody></table>';
} else { // If no records were returned.
	echo '<p>There are no shelters listed. <a href="admin-addshelter.php">Add shelter</a></p>';
}

mysqli_close($dbc);

require ('includes/admin-footer.inc.php');
?>