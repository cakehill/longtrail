<?php
//start the session
session_start();

//include everything that is needed for this page.
require ('includes/config.inc.php'); 

//check the users permission to see if they have admin abilities
checkPermission('admin');

//set the title
$title = 'Users';

//get the header info
require ('includes/admin-header.inc.php');

//get the admin menu
require ('includes/admin-menu.inc.php');
echo '<p class="subnav"><a href="admin-adduser.php" class="more">Add User</a> | <a href="includes/csv.php">Export User table as CSV</a></p>'; 
echo '<h1>Users</h1>';

// Need the database connection:
require (MYSQL);

// Make the query to grab all users
$q = "SELECT first_name, last_name, email, user_id, active, user_level, last_login FROM users ORDER BY last_name ASC";		
$r = @mysqli_query ($dbc, $q); // Run the query.

// Count the number of returned rows:
$num = mysqli_num_rows($r);

if ($num > 0) { // If it ran OK, display the records.
	echo '<table cellpadding="0" cellspacing="0" border="0" class="display" id="usersTable" width="100%">';
	echo '<thead><tr><th>First</th><th>Last</th><th>Email</th><th>Account Active</th><th>User Level</th><th>Last Login</th><th>Actions</th></tr></thead><tbody>';
	while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
		echo '<tr><td>'.$row['first_name'].'</td><td>' . $row['last_name'].'</td><td><a href="mailto:'.$row['email'].'">'.$row['email'].'</a></td><td>'. checkActive($row['active']) .'</td><td>'. checkLevel($row['user_level']) .'</td><td>'.$row['last_login'].'</td><td><a href="admin-edituser.php?id='.$row['user_id'].'" class="more">edit</a>, <a href="admin-trips.php?id='.$row['user_id'].'">view trips</a>'.sendAlert($row['active'], $row['user_id']).'</td></tr>';
	}
	echo '</tbody></table>';
} else { // If no records were returned.
	echo '<p>You have not entered any trips yet.</p>';
}

mysqli_close($dbc);

require ('includes/admin-footer.inc.php');
?>