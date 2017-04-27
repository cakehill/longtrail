<?php
session_start();
require ('includes/config.inc.php'); 

if (!isset($_SESSION['first_name'])) {
	// Redirect the user
	redirect_user();
}

$title = 'Your Location';
include('includes/header.inc.php'); 
?>
<body>
<div data-role="page" class="page-map">

    <div data-role="header">
    	<a data-rel="back">Back</a>
        <h1>Your Location</h1>
        <a href="/" class="ui-btn-right" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a> 
    </div>

    <div data-role="content" style="padding:0px">

    <div id="map" class="gmap"></div>

	<!--taken from http://code.google.com/apis/maps/articles/geolocation.html-->
    <script type="text/javascript">
		var initialLocation;
		var siberia = new google.maps.LatLng(60, 105);
		var newyork = new google.maps.LatLng(40.69847032728747, -73.9514422416687);
		var browserSupportFlag =  new Boolean();
  		var myOptions = {
    		zoom: 12,
    		mapTypeId: google.maps.MapTypeId.TERRAIN,
    		streetViewControl: false
  		};
  		var map = new google.maps.Map(document.getElementById("map"), myOptions);

		function initialize() {
  
  			// Try W3C Geolocation (Preferred)
  			if(navigator.geolocation) {
    			browserSupportFlag = true;
    			navigator.geolocation.getCurrentPosition(function(position) {
      				initialLocation = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
     				 map.setCenter(initialLocation);
     				 var marker = new google.maps.Marker({
     				 	position: initialLocation,
     				 	map: map,
     				 	title:"You are here."
     				 });
   			}, function() {
      				handleNoGeolocation(browserSupportFlag);
    			});
    		} else {
    			browserSupportFlag = false;
    			handleNoGeolocation(browserSupportFlag);
    		}
		function handleNoGeolocation(errorFlag) {
			if (errorFlag == true) {
      			alert("Geolocation service failed. Please turn it on in your settings.");
      			initialLocation = newyork;
    		} else {
      			alert("Your browser doesn't support geolocation. We've placed you in Siberia.");
      			initialLocation = siberia;
    		}
    		map.setCenter(initialLocation);
  		}
	} 

	$('.page-map').live("pagecreate", function() {
		initialize();
	});
    $('.page-map').live('pageshow',function(){
    	google.maps.event.trigger(map, 'resize');
        map.setOptions(myOptions); 
	});
</script> 

    </div>
</div>
</body>
</html>