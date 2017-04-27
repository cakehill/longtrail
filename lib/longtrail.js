(function(window, $, PhotoSwipe){
	$(document).ready(function(){
	    
	    //make sure the maps are set to fill the entire screen
	    var mapH = viewPortSize().height+20;
		var mapW = viewPortSize().width;
		$("#map").css({"width":mapW+"px","height":mapH+"px"});

	    //this keeps the rel=external links from opening a new window when if fullscreen mode on mobile safari
		if ( ("standalone" in window.navigator) && window.navigator.standalone ) {
			$("a[rel*=external]").live('click', function(){
			window.location.href=this.href;
			return false;
			});
   		}
		
		//verify that user really wants to submit trip
		$('#btnSubmit').click(function(event){
			return confirm("Please make sure you have completed all forms before sumbitting to GMC. Would you like to continue and submit trip?");
    	});
		
		//verify that user really wants to remove trip
		$('#btnRemove').click(function(event){
			return confirm("Are you sure you want to delete the trip?");
    	});

		//setup validation on these forms
		$("#registerform, #loginform, #tripInfoForm").validate();
				
		//setup the photoswipe ability in the guide area
		$('div.gallery-page')
			.live('pageshow', function(e){
				var 
					currentPage = $(e.target),
					options = {},
					photoSwipeInstance = $("ul.gallery a", e.target).photoSwipe(options,  currentPage.attr('id'));
				return true;
			})					
			.live('pagehide', function(e){
				var 
					currentPage = $(e.target),
					photoSwipeInstance = PhotoSwipe.getInstance(currentPage.attr('id'));
					if (typeof photoSwipeInstance != "undefined" && photoSwipeInstance != null) {
						PhotoSwipe.detatch(photoSwipeInstance);
					}
					return true;
			});
	});
		
}(window, window.jQuery, window.Code.PhotoSwipe));

//finds the width and height of the device.  Used to generate the map at full screen.
function viewPortSize() {
	var h = window.innerHeight || document.documentElement.clientHeight || document.getElementsByTagName('body')[0].clientHeight;
	var w = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
	return { 
		width : w , 
		height : h 
	}
}

 /*	<script>
    jQuery(window).ready(function(){
    	$("#btnInit").click(initiate_geolocation);
    	$("#gps-action").hide();
		$('button[data-confirm]').live('click', function() {
    		if (!confirm($(this).attr('data-confirm'))) {
      		return false;
    		}
  		});
	});

	function initiate_geolocation() {
  		var startPos;
  		navigator.geolocation.getCurrentPosition(function(position) {
    	startPos = position;
    	document.getElementById('startLat').innerHTML = startPos.coords.latitude;
    	document.getElementById('startLon').innerHTML = startPos.coords.longitude;
    	document.getElementById('gpsAccuracy').innerHTML = startPos.coords.accuracy;
    	$("#gps-action").show();
		 });
    }
    
    //$(document).bind("mobileinit", function(){
  	//$.mobile.ajaxFormsEnabled = False;
	//});
    </script>*/