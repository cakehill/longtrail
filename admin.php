<?php
//start the session
session_start();

//include everything that is needed for this page.
require ('includes/config.inc.php'); 

//check the users permission to see if they have admin abilities
checkPermission('admin');

//set the title
$title = 'Home';

//get the header info
require ('includes/admin-header.inc.php');

require(MYSQL);

//initiate query for getSubmitted()
$q = "SELECT trips.trips_id, trips.trip_name, trips.visit_date, users.first_name, users.last_name, shelters.shelters_name, shelters.shelters_id FROM trips INNER JOIN users ON users.user_id = trips.users_id INNER JOIN shelters ON trips.shelters_id = shelters.shelters_id WHERE trips.submitted = '1' AND trips.removed = '0' ORDER BY trips.visit_date desc LIMIT 10";
$r = @mysqli_query ($dbc, $q); // Run the query.
$num = mysqli_num_rows($r);

//initiate query for getUrgentItems()
$q1 = "SELECT trips.trips_id, trips.action_explain, trips.trip_name, trips.visit_date, users.first_name, users.last_name, shelters.shelters_name, shelters.shelters_id FROM trips INNER JOIN users ON users.user_id = trips.users_id INNER JOIN shelters ON trips.shelters_id = shelters.shelters_id WHERE trips.submitted = '1' AND trips.action_need_help = '1' AND trips.removed = '0' ORDER BY trips.visit_date desc LIMIT 10";
$r1 = @mysqli_query ($dbc, $q1); // Run the query.
$num1 = mysqli_num_rows($r1);

//initiate query for getNewUsers()
$q2 = "SELECT first_name, last_name, registration_date, user_id FROM users WHERE active = '0' LIMIT 10";
$r2 = @mysqli_query ($dbc, $q2); // Run the query.
$num2 = mysqli_num_rows($r2);

require ('includes/admin-menu.inc.php'); 
?>

<table class="dashboard" width="100%">
	<tr>
		<td>
			<h2>Last 10 items submitted</h2>
			<p>The latest items.</p>
			<? getSubmitted($num, $r); ?>
		</td>
		<td>
			<h2>Last 10 users registered</h2>
			<p>Users who are not yet active. Click name to activate them.</p>
			<? getNewUsers($num2, $r2); ?>
		</td>
	</tr>
	<tr>
		<td colspan="2"> 
			<h2>Urgent Items</h2>
			<p>User has marked an item as urgent in their trip report.</p>
			<? getUrgentItems($num1, $r1); ?>
		</td>
		<!--<td>
			<h2>One More</h2>
			<p>have to choose one more option</p>
		</td>-->
	</tr>
</table>
<?
require ('includes/admin-footer.inc.php');
?>