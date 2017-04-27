<?php
//start the session
session_start();

//include everything that is needed for this page.
require ('includes/config.inc.php'); 

//check to make sure they can access this area
checkPermission('user');

//set the page title
$title = 'List of Shelters';

//include the header info
include('includes/header.inc.php');
?>
<div data-role="page">
	<div data-role="header">
		<h1>Shelter List</h1>
		<a href="/" class="ui-btn-right" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a> 
	</div>
	<div data-role="content" data-theme="c">
		
<?

$ui = $_SESSION['user_id'];

// Need the database connection:
require (MYSQL);
// Make the query:
$q = "SELECT shelters_id, shelters_name, latitude, longitude, comments FROM shelters ORDER BY shelters_name ASC";
$r = @mysqli_query ($dbc, $q); // Run the query.

// Count the number of returned rows:
$num = mysqli_num_rows($r);

if ($num > 0) { // If it ran OK, display the records.
	echo '<ul data-role="listview" data-inset="true" data-filter="true" data-theme="c" data-filter-placeholder="Search the shelters...">';
	while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
		$latitude = $row['latitude'];
		if (empty($latitude)) {
			$latitude = 'not set';
		}
		$longitude = $row['longitude'];
		if (empty($longitude)) {
			$longitude = 'not set';
		}
		echo "<li><div data-role='collapsible' data-collapsed='true'>";
		echo "<h3>".$row['shelters_name']."</h3>";
		echo "<p><div>Latitude: ".$latitude."</div></p>";
		echo "<p><div>Longitude: ".$longitude."</div></p>";
		echo "<p><div>Comment: ".nl2br($row['comments'])."</div></p>";
		if ((!empty($row['latitude']) && (!empty($row['longitude'])))) {
			echo "<p style='margin-top:1.5em;'><div><a href='/maps.php?id=".$row['shelters_id']."' data-role='button' data-inline='true' rel='external'>View map</a></div></p></div></li>";
		} else {
			echo '</div></li>';
		}
	}
	echo '</ul>';
} else { // If no records were returned.
	echo '<p>There are no shelters yet setup.</p>';
}

mysqli_close($dbc);

?>
	</div><!-- /content -->
</div>

<?php
include('includes/footer.inc.php'); 
?>  		