<?php
//start the session
session_start();

//include everything that is needed for this page.
require ('includes/config.inc.php'); 

//check to make sure they can access this area
checkPermission('user');

//set the page title
$title = 'Shelter Maps';

//include the header info
include('includes/header.inc.php');

//do some validation - making sure the id is set
if (isset($_GET['id'])) {
	$theShelterId = $_GET['id'];
	if ((empty($theShelterId)) || (!is_numeric($theShelterId))) {
		// Redirect the user:
		$url = BASE_URL . 'index.php'; // Define the URL.
		header("Location: $url");
		exit(); // Quit the script.
	}
} elseif (!isset($_GET['id'])) {
	// Redirect the user:
	$url = BASE_URL . 'index.php'; // Define the URL.
	header("Location: $url");
	exit(); // Quit the script.
} 

// Need the database connection:
require (MYSQL);

// initialize flags
$OK = false;

// initialize statement
$stmt = $dbc->stmt_init();

// prepare SQL query
$sql = 'SELECT shelters_name, latitude, longitude FROM shelters WHERE shelters_id = ?';
if ($stmt->prepare($sql)) {
	// bind the query parameter
	$stmt->bind_param('i', $theShelterId);
	// bind the results to variables
	$stmt->bind_result($sheltersName, $latitude, $longitude);
	// execute the query, and fetch the result
	$OK = $stmt->execute();
	$stmt->store_result();
	$stmt->fetch();
	//if there are no results, send them back to home page
	if ($stmt->num_rows < 1) {
		$url = BASE_URL . 'index.php'; // Define the URL.
		header("Location: $url");
		exit(); // Quit the script.
	}
	unset($stmt);
	mysqli_close($dbc);
}


?>
<body>
<div data-role="page" class="page-map">

    <div data-role="header">
    	<a data-rel="back">Back</a>
        <h1><? echo $sheltersName; ?></h1>
        <a href="/" class="ui-btn-right" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a> 
    </div>

    <div data-role="content" style="padding:0px">

    <div id="map" class="gmap"></div>

	<!--the script that generates the map-->
    <script type="text/javascript">
        var map, latlng, options;
        function initialize() {
            latlng = new google.maps.LatLng(<? echo $latitude; ?>, <? echo $longitude; ?>);
            options = { 
            	zoom: 12, 
            	center: latlng, 
            	mapTypeId: google.maps.MapTypeId.TERRAIN, 
            	streetViewControl: false
            };
            map = new google.maps.Map(document.getElementById("map"), options);
			var marker = new google.maps.Marker({
        		position: latlng,
        		map: map,
        		title:"This is the spot."
    		});    
			marker.info = new google.maps.InfoWindow({
  				content: '<h3><? echo $sheltersName; ?></h3><p>Latitude: <? echo $latitude; ?><br />Longitude: <? echo $longitude; ?></p>'
			});
			google.maps.event.addListener(marker, 'click', function() {
  				marker.info.open(map, marker);
			});
		}
        $('.page-map').live("pagecreate", function() {
            initialize();
        });
        $('.page-map').live('pageshow',function(){
            google.maps.event.trigger(map, 'resize');
            map.setOptions(options); 
        });
    </script> 

    </div>
</div>
</body>
</html>