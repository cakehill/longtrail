<?php
//start the session
session_start();

//include everything that is needed for this page.
require ('includes/config.inc.php'); 

//check to make sure they can access this area
checkPermission('user');

//set the page title
$title = 'List of Trips';

//include the header info
include('includes/header.inc.php');
?>
<div data-role="page">
	<div data-role="header">
		<h1>List of Trips</h1>
		<a href="/" class="ui-btn-right" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a> 
	</div>
	<div data-role="content" data-theme="c">
<?
// Need the database connection:
require (MYSQL);
// Make the query:
$q = "SELECT trip_name, trips_id, submitted, visit_date FROM trips WHERE users_id = $_SESSION[user_id] AND removed = '0' ORDER BY submitted, visit_date DESC";		
$r = @mysqli_query ($dbc, $q); // Run the query.

// Count the number of returned rows:
$num = mysqli_num_rows($r);

if ($num > 0) { // If it ran OK, display the records.

	$currentcategory = NULL;	
	$first = TRUE;
	echo '<div data-role="collapsible-set">';
	while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {

		$myDate = date('m/d/y', strtotime($row['visit_date']));
	
		if ($row['submitted'] == 0) {
	   		$rowHeader = 'In Progress';
		}else{
	    	$rowHeader = 'Submitted';
		}
	   
	    //Check for a category change
	    if($currentcategory != $row['submitted']){
			if (!$first) {
	      		echo '</ul></div>';
   				echo "<div data-role='collapsible' data-content-theme='c'>";
   				echo "<h3>{$rowHeader}</h3>";
	    	} else {
				echo "<div data-role='collapsible' data-collapsed='false' data-content-theme='c'>";
   				echo "<h3>{$rowHeader}</h3>";
			}
			echo '<ul data-role="listview" data-inset="true" data-theme="c" data-dividertheme="b">';  
			//echo '<li data-role="list-divider" role="heading" >'.$rowHeader.'</li>';
	        if($currentcategory != $row['submitted']){
	        	$end = TRUE;
	        }
	        $currentcategory = $row['submitted'];
	        $first = false;
	    }
		echo '<li><a href="details.php?id=' . $row['trips_id'] . '" rel="external">' . $row['trip_name'] . ' <span class="ui-li-aside">' .$myDate.'</span></a></li>';
	}
	echo '</ul></div></div>';
}
else { // If no records were returned.
	echo '<p>You have not entered any trips yet.</p>';
}

mysqli_close($dbc);

?>
	</div><!-- /content -->
</div>

<?php
include('includes/footer.inc.php'); 
?>