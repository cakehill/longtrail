<?php
//start the session
session_start();

//include everything that is needed for this page.
require ('includes/config.inc.php'); 

//check the users permission to see if they have admin abilities
checkPermission('admin');

//set the title
$title = 'Alerts';

//get the header info
require ('includes/admin-header.inc.php');

//get the admin navigation
require ('includes/admin-menu.inc.php'); 
echo '<p class="subnav"><a href="admin-addalert.php" class="more">Send Alert</a> | <a href="includes/csv.php">Export Alerts table as CSV</a></p>';
echo '<h1>Alerts</h1>';
// Need the database connection:
require (MYSQL);
// Make the query:
$q = "SELECT alerts.message, alerts.alert_date, alerts.subject, users.first_name, users.last_name FROM alerts INNER JOIN users ON alerts.to_id = users.user_id OR alerts.to_all = '1' ORDER BY alert_date DESC";
$r = @mysqli_query ($dbc, $q); // Run the query.

// Count the number of returned rows:
$num = mysqli_num_rows($r);

if ($num > 0) { // If it ran OK, display the records.
	echo '<table cellpadding="0" cellspacing="0" border="0" class="display" id="alertsTable" width="100%">';
	echo '<thead><tr><th>Date of Alert</th><th>Subject</th><th>Message</th><th>To</th></tr></thead><tbody>';
	while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
		echo '<tr><td>'.$row['alert_date'].'</td><td>'.$row['subject'].'</td><td>'.nl2br($row['message']).'</td><td>'.$row['first_name'].' '.$row['last_name'].'</td></tr>';
	}
	echo '</tbody></table>';
} else { // If no records were returned.
	echo '<p>There are no alerts listed yet. <a href="admin-addalert.php">Add an alert.</a></p>';
}

mysqli_close($dbc);
require ('includes/admin-footer.inc.php');
?>
