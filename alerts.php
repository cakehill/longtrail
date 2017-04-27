<?php
session_start();
require ('includes/config.inc.php'); 

if (!isset($_SESSION['first_name'])) {
	// Redirect the user
	redirect_user();
}
$title = 'List of Alerts';
include('includes/header.inc.php'); 
?>
<body>
<div data-role="page">
	<div data-role="header">
		<h1>Alerts</h1>
		<a href="/" class="ui-btn-right" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a> 
	</div>
	<div data-role="content" data-theme="c">
		
<?

$ui = $_SESSION['user_id'];

// Need the database connection:
require (MYSQL);
// Make the query:
$q = "SELECT message, alert_date, subject FROM alerts WHERE to_id = '$ui' OR to_all = '1' ORDER BY alert_date DESC";
$r = @mysqli_query ($dbc, $q); // Run the query.

// Count the number of returned rows:
$num = mysqli_num_rows($r);

if ($num > 0) { // If it ran OK, display the records.
	echo '<ul data-role="listview" data-inset="true" data-filter="true" data-theme="c" data-filter-placeholder="Search the alerts...">';
	$currentcategory = NULL;	
	$first = TRUE;

	while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {

		$myDate = date('m/d/y', strtotime($row['alert_date']));
		echo "<li><div data-role='collapsible' data-collapsed='true'>";
		echo "<h3>".$myDate." ".$row['subject']."</h3>";
		echo "<p><div>Date: ".$myDate."</div></p>";
		echo "<p><div>Subject: ".nl2br($row['subject'])."</div></p>";
		echo "<p style='margin-top:1.5em;'><div>Alert: ".nl2br($row['message'])."</div></p></div></li>";
	}
	echo '</ul>';
} else { // If no records were returned.
	echo '<p>You have not been sent any alerts.</p>';
}
?>
	</div><!-- /content -->
</div>
?>
</body>
</html>