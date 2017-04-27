<?php
//start the session
session_start();

//include everything that is needed for this page.
require ('includes/config.inc.php'); 

if (!isset($_SESSION['first_name'])) {
	// Redirect the user:
	$url = BASE_URL . 'login/index.php'; // Define the URL.
	header("Location: $url");
	exit(); // Quit the script.
}

//set the page title
$title = 'Welcome';

//include the header info
include('includes/header.inc.php');

// Need the database connection:
require (MYSQL);

$q = "SELECT COUNT(*) as tripCount FROM trips WHERE users_id = $_SESSION[user_id] AND submitted = '0' AND removed = '0'";		
$r = @mysqli_query ($dbc, $q); // Run the query.

// Count the number of returned rows:
$num = mysqli_num_rows($r);

if ($num > 0) { // If it ran OK, display the records.
	$row = mysqli_fetch_array($r, MYSQLI_ASSOC);
	$tripCount = $row['tripCount'];
} else {
	$tripCount = "0";
}	

$q1 = "SELECT COUNT(*) as alertCount FROM alerts WHERE to_id = $_SESSION[user_id] OR to_all = '1'";		
$r1 = @mysqli_query ($dbc, $q1); // Run the query.

// Count the number of returned rows:
$num1 = mysqli_num_rows($r1);

if ($num1 > 0) { // If it ran OK, display the records.
	$row1 = mysqli_fetch_array($r1, MYSQLI_ASSOC);
	$alertCount = $row1['alertCount'];
} else {
	$alertCount = "0";
}	

mysqli_close($dbc);

?>
<body> 
<div data-role="page" class="type-home">
	<div data-role="header">
		<h1>GMC</h1>
	</div>	
	<div data-role="content">
		<h2>Shelter & Overnight Site Maintenance Report</h2>
		<div class="content-secondary">
			<ul data-role="listview" data-inset="true" data-theme="c" data-dividertheme="f">
				<? 
				//if they are admin, give them the option
				if ($_SESSION['user_level'] == '1') {
				 echo '<li><a href="admin.php" rel="external">Admin Area</a></li>';
				} 
				?>
				<li><a href="tripinfo.php" data-rel="dialog" data-transition="flip">New Trip</a></li>
				<li><a href="trips.php">Edit Trips</a><span class="ui-li-count"><? echo $tripCount; ?></span></li>
				<li><a href="alerts.php">Alerts</a><span class="ui-li-count"><? echo $alertCount; ?></span></li>
				<li><a href="shelters.php">Shelter List</a></li>
				<li><a href="guide.php">Guide</a></li>
			</ul>
			<fieldset class="ui-grid-a">
				<div class="ui-block-a"><a href="/login/logout.php" data-role="button">Logout</a></div>
				<div class="ui-block-b"><a href="/login/change_password.php" rel="external" data-icon="gear" data-role="button">Options</a></div>	   
			</fieldset>
		</div>	
	</div>
</div>

<?php
include('includes/footer.inc.php'); 
?>