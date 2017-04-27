<?php
//start the session
session_start();

//include everything that is needed for this page.
require ('includes/config.inc.php'); 

//check to make sure they can access this area
checkPermission('user');

//set the page title
$title = 'Trip Details';

//include the header info
include('includes/header.inc.php');

//check the querystring for correct input
//bounce to homescreen if found incorrect
if (isset($_GET['id'])) {
	$theTripId = $_GET['id'];
	if ((empty($theTripId)) || (!is_numeric($theTripId))) {
		// Redirect the user:
		$url = BASE_URL . 'index.php'; // Define the URL.
		header("Location: $url");
		exit(); // Quit the script.
	}
} elseif (isset($_POST['id'])) {
	$theTripId = $_POST['id'];
} elseif ((!isset($_GET['id'])) || (!isset($_POST['id']))) {
	// Redirect the user:
	$url = BASE_URL . 'index.php'; // Define the URL.
	header("Location: $url");
	exit(); // Quit the script.
} 
// Need the database connection:
require (MYSQL);

//determine whether or not they are admin
//if so, let them edit anything, if not, they will be bounced to homescreen.
//ex. if the non-admin user switches the querystring parameter manually,
//we don't want them seeing content that is not theirs.
$and = "";
// initialize flags
if ($_SESSION['user_level'] != '1') {
	$and = 'AND users_id = ?';
}
$OK = false;
$done = false;
// initialize statement
$stmt = $dbc->stmt_init();

// get details of selected record
if (isset($theTripId) && !$_POST) {
  // prepare SQL query
  $sql = 'SELECT shelters_id,
  work_completed, 
  additional_comments, 
  structure_walls_intact,
  structure_floor_intact,
  structure_stain_adequate,
  structure_rot_present,
  structure_leaks_present, 
  structure_sills_rot_present, 
  structure_sills_silt,
  structure_roof_material,
  privy_structure_intact,
  privy_stains_adequate, 
  privy_comments,
  privy_room_in_crib_text,
  signs_site_map,
  signs_shelter_log, 
  signs_leave_no_trace,
  signs_water_not_tested,
  signs_request,
  action_still_todo,
  action_urgency, 
  action_need_help,
  action_explain,
  hours_travel,
  hours_work,	
  number_of_people, 
  trip_name,
  visit_date,
  firering_only_one,
  firering_pit_clean,	
  washpit_exists,	
  washpit_screen_intact,	
  washpit_clean,	
  watersource_adequate,	
  garbage_removed,	
  outdoor_comments,
  submitted,
  removed,
  modified_datetime,
  trip_done,
  work_done,
  structure_done,
  action_done,
  privy_done,
  signs_done,
  outdoor_done,
  additional_done
  FROM trips WHERE trips_id = ? '. $and;
  if ($stmt->prepare($sql)) {
	// bind the query parameter
	//determing their level before we create the statement
	if ($_SESSION['user_level'] == '1') {
		$stmt->bind_param('i', $theTripId);
	} else {
		$stmt->bind_param('ii', $theTripId, $_SESSION['user_id']);
	}
	// bind the results to variables
	$stmt->bind_result($sheltersId,
	$workCompleted, 
	$additionalComments, 
	$wallsIntact, 
	$floorIntact, 
	$stainAdequate, 
	$rotPresent, 
	$leaksPresent, 
	$sillsRotPresent, 
	$sillsSilt, 
	$structureRoofMaterial,
	$privyStructureIntact,
	$privyStainsAdequate,
	$privyComments,
	$privyRoomInCribText,
	$signsSiteMap,
	$signsShelterLog,
	$signsLeaveNoTrace,
	$signsWaterNotTested,
	$signsRequest,
	$actionStillTodo,
  	$actionUrgency, 
  	$actionNeedHelp,
  	$actionExplain,
  	$hoursTravel,
  	$hoursWork,	
  	$numberOfPeople, 
  	$tripName,
  	$visitDate,
  	$fireringOne,
  	$fireringClean,
  	$washpitExists,
  	$washpitScreenIntact,
  	$washpitClean,
  	$watersourceAdequate,
  	$garbageRemoved,
  	$outdoorComments,
  	$submitted,
  	$removed,
  	$modifiedDatetime,
  	$tripDone,
  	$workDone,
  	$structureDone,
  	$actionDone,
  	$privyDone,
  	$signsDone,
  	$outdoorDone,
  	$additionalDone);
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
	}
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Handle the form.
  	
  	if (!empty($_POST['reactivate-trip'])) {
  	// prepare update query
  		$sql = 'UPDATE trips SET removed = 0
			WHERE trips_id = ?';
  		if ($stmt->prepare($sql)) {
			$stmt->bind_param('i', $theTripId);
			$done = $stmt->execute();
		}
	// Redirect the user:
	$url = BASE_URL . 'admin.php'; // Define the URL.
	header("Location: $url");
	exit(); // Quit the script.
	}

  	if (!empty($_POST['remove-trip'])) {
  	// prepare update query
  		$sql = 'UPDATE trips SET removed = 1
			WHERE trips_id = ?';
  		if ($stmt->prepare($sql)) {
			$stmt->bind_param('i', $theTripId);
			$done = $stmt->execute();
		}
	// Redirect the user:
	$url = BASE_URL . 'trips.php'; // Define the URL.
	header("Location: $url");
	exit(); // Quit the script.
	}

  	if (!empty($_POST['submit-trip'])) {
  	// prepare update query
  		$sql = 'UPDATE trips SET submitted = 1
			WHERE trips_id = ?';
  		if ($stmt->prepare($sql)) {
			$stmt->bind_param('i', $theTripId);
			$done = $stmt->execute();
		}
	// Redirect the user:
	$url = BASE_URL . 'trips.php'; // Define the URL.
	header("Location: $url");
	exit(); // Quit the script.
	}
	  	
  	if (!empty($_POST['work-submit'])) {
  		$wc = strip_tags($_POST['textarea-work']);
  		// prepare update query
  		$sql = 'UPDATE trips SET work_completed = ?, work_done = "1"
			WHERE trips_id = ?';
  		if ($stmt->prepare($sql)) {
			$stmt->bind_param('si', $wc, $theTripId);
			$done = $stmt->execute();
		}
	}
  	if (!empty($_POST['structure-submit'])) {
  		// prepare update query
  		$sql = 'UPDATE trips SET 
  		structure_walls_intact = ?, 
  		structure_floor_intact = ?, 
  		structure_stain_adequate = ?,
  		structure_roof_material = ?,
  		structure_rot_present = ?, 
  		structure_leaks_present = ?, 
  		structure_sills_rot_present = ?, 
  		structure_sills_silt = ?,
  		structure_done = "1"
			WHERE trips_id = ?';
  		if ($stmt->prepare($sql)) {
			$stmt->bind_param('iiisiiiii', $_POST['slider1'], $_POST['slider2'], $_POST['slider3'], $_POST['radio-choice-1'], $_POST['slider4'], $_POST['slider5'],  $_POST['slider6'],  $_POST['slider7'], $theTripId);
			$done = $stmt->execute();
		}	
	}	
  	if (!empty($_POST['privy-submit'])) {
  		$pt = strip_tags($_POST['privy-textarea']);
  		// prepare update query
  		$sql = 'UPDATE trips SET privy_structure_intact = ?,
  		privy_stains_adequate = ?, 
  		privy_comments = ?,
  		privy_room_in_crib_text = ?,
  		privy_done = "1"
			WHERE trips_id = ?';
  		if ($stmt->prepare($sql)) {
			$stmt->bind_param('iissi', $_POST['privy-slider1'], $_POST['privy-slider2'], $pt, $_POST['privy-radio-choice-1'], $theTripId);
			$done = $stmt->execute();
		}	
	}
  	if (!empty($_POST['signs-submit'])) {
  		$st = strip_tags($_POST['signs-textarea']);
  		// prepare update query
  		$sql = 'UPDATE trips SET signs_site_map = ?,
  		 signs_shelter_log = ?, 
  		 signs_leave_no_trace = ?,
  		 signs_water_not_tested= ?,
  		 signs_request = ?,
  		 signs_done = "1"
			WHERE trips_id = ?';
  		if ($stmt->prepare($sql)) {
			$stmt->bind_param('sssssi', $_POST['signs-radio-choice-1'], $_POST['signs-radio-choice-2'], $_POST['signs-radio-choice-3'], $_POST['signs-radio-choice-4'], $st, $theTripId);
			$done = $stmt->execute();
		}	
	}	
  	if (!empty($_POST['action-submit'])) {
  		$tn = strip_tags($_POST['action-textarea-needstobedone']);
  		$te = strip_tags($_POST['action-textarea-explain']);
  		// prepare update query
  		$sql = 'UPDATE trips SET action_still_todo = ?,
  		 action_urgency = ?, 
  		 action_need_help = ?,
  		 action_explain = ?,
  		 action_done = "1"
  		 WHERE trips_id = ?';
  		if ($stmt->prepare($sql)) {
			$stmt->bind_param('ssisi', $tn, $_POST['urgent-radio-choice-1'], $_POST['action-help'], $te, $theTripId);
			$done = $stmt->execute();
		}	
	}	

  	if (!empty($_POST['comments-submit'])) {
  		$ta = strip_tags($_POST['textarea-additional']);
  		// prepare update query
  		$sql = 'UPDATE trips SET additional_comments = ?, additional_done = "1"
			WHERE trips_id = ?';
  		if ($stmt->prepare($sql)) {
			$stmt->bind_param('si', $ta, $theTripId);
			$done = $stmt->execute();
		}	
	}	

  	if (!empty($_POST['outdoor-submit'])) {
  		$oc = strip_tags($_POST['outdoor-comments']);
  		// prepare update query
  		$sql = 'UPDATE trips SET firering_only_one = ?,
  			firering_pit_clean = ?,	
  			washpit_exists = ?,	
  			washpit_screen_intact = ?,	
  			washpit_clean = ?,	
  			watersource_adequate = ?,	
  			garbage_removed = ?,	
  			outdoor_comments = ?,
  			outdoor_done = "1"
			WHERE trips_id = ?';
  		if ($stmt->prepare($sql)) {
			$stmt->bind_param('ssssssssi', $_POST['outdoor-firering-one'], $_POST['outdoor-firering-clean'], $_POST['outdoor-washpit-exists'], $_POST['outdoor-washpit-screen'], $_POST['outdoor-washpit-clean'], $_POST['outdoor-watersource'], $_POST['outdoor-garbage'], $oc, $theTripId);
			$done = $stmt->execute();
		}	
	}	

  	if (!empty($_POST['info-submit'])) {
  		$it = strip_tags($_POST['info-tripname']);
  		$iv = strip_tags($_POST['info-visitdate']);
  		// prepare update query
  		$sql = 'UPDATE trips SET hours_travel = ?,
  		hours_work = ?,
  		number_of_people = ?, 
  		trip_name = ?,
  		shelters_id = ?,
  		visit_date = ?,
  		trip_done = "1"
			WHERE trips_id = ?';
  		if ($stmt->prepare($sql)) {
			$stmt->bind_param('iiisisi', $_POST['info-travel'], $_POST['info-work'], $_POST['info-people'], $it, $_POST['sheltersId'], $iv, $theTripId);
			$done = $stmt->execute();
		}	
	}	

	// Redirect the user:
	$url = BASE_URL . 'details.php?id=' . $theTripId; // Define the URL.
	header("Location: $url");
	mysqli_close($dbc);
	exit(); // Quit the script.
}
?>

<body> 
<!-- Start of the main menu -->
<div data-role="page">
	<div data-role="header">
		<h1>Trip Details</h1>
		<a href="/" class="ui-btn-right" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a> 
	</div>	
	<div data-role="content" id="one">
			<ul data-role="listview" data-inset="true" data-theme="c" data-dividertheme="f">
				<li <? if ($tripDone) echo 'data-icon="check" data-theme="b"'; ?>><a href="#info">Trip Info</a></li>
				<li <? if ($workDone) echo 'data-icon="check" data-theme="b"'; ?>><a href="#work">Work Completed During Visit</a></li>
				<li <? if ($structureDone) echo 'data-icon="check" data-theme="b"'; ?>><a href="#structure">Structure Checklist</a></li>
				<li <? if ($actionDone) echo 'data-icon="check" data-theme="b"'; ?>><a href="#action">Action Items</a></li>
				<li <? if ($privyDone) echo 'data-icon="check" data-theme="b"'; ?>><a href="#privy">Privy Checklist</a></li>
				<li <? if ($signsDone) echo 'data-icon="check" data-theme="b"'; ?>><a href="#signs">Signs</a></li>
				<li <? if ($outdoorDone) echo 'data-icon="check" data-theme="b"'; ?>><a href="#outdoor">Outdoor Misc</a></li>
				<li <? if ($additionalDone) echo 'data-icon="check" data-theme="b"'; ?>><a href="#comments">Additional Comments</a></li>
			</ul>
			<? //only show submit and delete if the trip has not been submitted 
			if (!$submitted) { ?>
			<form action="details.php" method="post" data-ajax="false">
			<input type="hidden" name="id" value="<? echo $theTripId ?>" />	
			<input type="submit" data-theme="c" name="submit-trip" id="btnSubmit" value="Submit Trip">
			<input type="submit" data-theme="c" name="remove-trip" id="btnRemove" data-icon="delete" value="Delete Trip">
			</form>
			<? } else { ?>
			<p>This trip has been submitted and was last modified <? echo $modifiedDatetime; ?>.</p>
			<? } 
			if (($removed) && ($_SESSION['user_level'] == '1')) {
			?>
			<form action="details.php" method="post" data-ajax="false">
			<input type="hidden" name="id" value="<? echo $theTripId ?>" />	
			<input type="submit" data-theme="c" name="reactivate-trip" id="btnReactivate" value="Reactivate Trip">
			</form>

			<?
			}
			?>

	</div><!-- /content -->
</div><!-- /main menu -->

<!-- Start of first page: #info -->
<div data-role="page" id="info" data-theme="c">
	<div data-role="header">
		<h1>Trip Info</h1>
		<a href="/" class="ui-btn-right" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a> 
	</div>
<form action="details.php" method="post" data-ajax="false">
<input type="hidden" name="id" value="<? echo $theTripId ?>" />	
	<div data-role="content" data-theme="c">	
		<div data-role="fieldcontain">
	         <label for="info-tripname">Name:</label>
	         <input type="text" maxlength="40" name="info-tripname" id="info-tripname" value="<? echo $tripName; ?>"  />
		</div>
		<div data-role="fieldcontain">
	         <label for="info-visitdate">Date of Visit:</label>
	         <input type="date" name="info-visitdate" id="info-visitdate" data-role="datebox" data-options='{"mode": "calbox"}' value="<? echo $visitDate; ?>"  />
		</div>

<?		
		$q = "SELECT shelters_name, shelters_id FROM shelters ORDER BY shelters_name ASC";		
		$r = @mysqli_query ($dbc, $q) or die("MySQL error: " . mysqli_error($dbc) . "<hr>\nQuery: $q"); // Run the query.

		// Count the number of returned rows:
		$num = mysqli_num_rows($r);

		if ($num > 0) { // If it ran OK, display the records.
			echo '<div data-role="fieldcontain">';
			echo '<label for="sheltersId" class="select">Shelter</label>';
			echo '<select name="sheltersId" id="sheltersId">';
			while ($row = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
				echo '<option value="'.$row['shelters_id'].'" ';
				if ($sheltersId == $row['shelters_id']) {
				echo 'selected';
				}
				echo '>'.$row['shelters_name'].'</option>';
			}
		echo '</select>';
		echo '</div>';
		}
	else { // If no records were returned.
	echo '<p>There are no shelters to choose from.</p>';
	}
?>
		<div data-role="fieldcontain">
   			<label for="info-travel">Hours of Travel (include driving and hiking time):</label>
   			<input type="range" name="info-travel" id="info-travel" value="<? echo $hoursTravel; ?>" min="0" max="50"  />
		</div>

		<div data-role="fieldcontain">
   			<label for="info-work">Hours of Work:</label>
   			<input type="range" name="info-work" id="info-work" value="<? echo $hoursWork; ?>" min="0" max="50"  />
		</div>

		<div data-role="fieldcontain">
   			<label for="info-people">Number of people:</label>
   			<input type="range" name="info-people" id="info-people" value="<? echo $numberOfPeople; ?>" min="0" max="50"  />
		</div>

		<button type="submit" data-theme="c" name="info-submit" value="submit-value">Save</button>
	</div><!-- /content -->
</form>	
</div><!-- /first page -->

<!-- Start of second page: #work -->
<div data-role="page" id="work" data-theme="c">
	<div data-role="header">
		<h1>Work Completed During Visit</h1>
		<a href="/" class="ui-btn-right" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a> 
	</div>
<form action="details.php" method="post" data-ajax="false">
<input type="hidden" name="id" value="<? echo $theTripId ?>" />
<input type="hidden" name="action" value="work" />
	<div data-role="content" data-theme="c">
		<h2>Work Completed During Visit</h2>
		<div data-role="fieldcontain">
			<textarea cols="40" rows="8" name="textarea-work" id="textarea-work"><? echo $workCompleted; ?></textarea>
		</div>
		<button type="submit" data-theme="c" name="work-submit" value="submit-value">Save</button>
	</div><!-- /content -->
</form>	
</div><!-- /page two -->

<!-- Start of third page: #structure -->
<div data-role="page" id="structure" data-theme="c">
	<div data-role="header">
		<h1>Structure Checklist</h1>
		<a href="/" class="ui-btn-right" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a> 
	</div>
<form action="details.php" method="post" data-ajax="false">
<input type="hidden" name="id" value="<? echo $theTripId ?>" />	
	<div data-role="content" data-theme="c">
		<div data-role="fieldcontain">
			<label for="slider1">Walls intact?</label>
			<select name="slider1" id="slider1" data-role="slider">
				<option value="0" <? if (!$wallsIntact) echo 'selected="selected"'; ?>>no</option>
				<option value="1" <? if ($wallsIntact) echo 'selected="selected"'; ?>>yes</option>
			</select> 
		</div>	
		<div data-role="fieldcontain">
			<label for="slider2">Floor intact?</label>
			<select name="slider2" id="slider2" data-role="slider">
				<option value="0" <? if (!$floorIntact) echo 'selected="selected"'; ?>>no</option>
				<option value="1" <? if ($floorIntact) echo 'selected="selected"'; ?>>yes</option>
			</select> 
		</div>	
		<div data-role="fieldcontain">
			<label for="slider3">Stain adequate?</label>
			<select name="slider3" id="slider3" data-role="slider">
				<option value="0" <? if (!$stainAdequate) echo 'selected="selected"'; ?>>no</option>
				<option value="1" <? if ($stainAdequate) echo 'selected="selected"'; ?>>yes</option>
			</select> 
		</div>	
		<div data-role="fieldcontain">
    		<fieldset data-role="controlgroup" data-type="horizontal">
				<legend>Roof material:</legend>
				<input type="radio" name="radio-choice-1" id="radio-choice-1" value="shingle" <? if ($structureRoofMaterial == 'shingle') echo 'checked="checked"'; ?> />
         		<label for="radio-choice-1">Shingle</label>

         		<input type="radio" name="radio-choice-1" id="radio-choice-2" value="metal" <? if ($structureRoofMaterial == 'metal') echo 'checked="checked"'; ?> />
         		<label for="radio-choice-2">Metal</label>

         		<input type="radio" name="radio-choice-1" id="radio-choice-3" value="roll" <? if ($structureRoofMaterial == 'roll') echo 'checked="checked"'; ?> />
         		<label for="radio-choice-3">Roll</label>
    		</fieldset>
		</div>
		<div data-role="fieldcontain">
			<label for="slider4">Rot present on roof?</label>
			<select name="slider4" id="slider4" data-role="slider">
				<option value="0" <? if (!$rotPresent) echo 'selected="selected"'; ?>>no</option>
				<option value="1" <? if ($rotPresent) echo 'selected="selected"'; ?>>yes</option>
			</select> 
		</div>	
		<div data-role="fieldcontain">
			<label for="slider5">Leaks present on roof?<br />(Check entries in logbook)</label>
			<select name="slider5" id="slider5" data-role="slider">
				<option value="0" <? if (!$leaksPresent) echo 'selected="selected"'; ?>>no</option>
				<option value="1" <? if ($leaksPresent) echo 'selected="selected"'; ?>>yes</option>
			</select> 
		</div>	
		<div data-role="fieldcontain">
			<label for="slider6">Rot present on sills?</label>
			<select name="slider6" id="slider6" data-role="slider">
				<option value="0" <? if (!$sillsRotPresent) echo 'selected="selected"'; ?>>no</option>
				<option value="1" <? if ($sillsRotPresent) echo 'selected="selected"'; ?>>yes</option>
			</select> 
		</div>	
		<div data-role="fieldcontain">
			<label for="slider7">Silt against sills?</label>
			<select name="slider7" id="slider7" data-role="slider">
				<option value="0" <? if (!$sillsSilt) echo 'selected="selected"'; ?>>no</option>
				<option value="1" <? if ($sillsSilt) echo 'selected="selected"'; ?>>yes</option>
			</select> 
		</div>	
	
		<button type="submit" data-theme="c" name="structure-submit" value="submit-value">Save</button>
	</div><!-- /content -->
</form>	
</div><!-- /page three -->

<!-- Start of fourth page: #action -->
<div data-role="page" id="action" data-theme="c">
	<div data-role="header">
		<h1>Action Items</h1>
		<a href="/" class="ui-btn-right" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a> 
	</div>
<form action="details.php" method="post" data-ajax="false">
<input type="hidden" name="id" value="<? echo $theTripId ?>" />
	<div data-role="content" data-theme="c">
		<!--<h2>Action Items</h2>
		<p id="gps-action">Help needed:<br/>
    	Latitude: <span id="startLat">???</span>&deg;<br />
    	Longitude: <span id="startLon">???</span>&deg;<br />
    	Accuracy: <span id="gpsAccuracy">???</span> meters 
  		</p>
		<button type="submit" data-theme="c" id="btnInit" name="submit" value="submit-value">GPS mark an urgent area</button>-->
		<div data-role="fieldcontain">
		<label for="textarea-needstobedone">What still needs to be done?</label>
			<textarea cols="40" rows="8" name="action-textarea-needstobedone" id="textarea-needstobedone"><? echo $actionStillTodo; ?></textarea>
		</div>
		<div data-role="fieldcontain">
    		<fieldset data-role="controlgroup" data-type="horizontal">
			<legend>How urgent?</legend>
				<input type="radio" name="urgent-radio-choice-1" id="urgent-choice-1" value="very" <? if ($actionUrgency == 'very') echo 'checked="checked"'; ?> />
         		<label for="urgent-choice-1">very</label>
         		<input type="radio" name="urgent-radio-choice-1" id="urgent-choice-2" value="not very" <? if ($actionUrgency == 'not very') echo 'checked="checked"'; ?> />
         		<label for="urgent-choice-2">not very</label>
			</fieldset>
		</div>
		<div data-role="fieldcontain">
			<label for="help">Do you need help?</label>
			<select name="action-help" id="help" data-role="slider">
				<option value="0" <? if (!$actionNeedHelp) echo 'selected="selected"'; ?>>no</option>
				<option value="1" <? if ($actionNeedHelp) echo 'selected="selected"'; ?>>yes</option>
			</select> 
		</div>	
		<div data-role="fieldcontain">
		<label for="textarea-explain">Explain:</label>
			<textarea cols="40" rows="8" name="action-textarea-explain" id="textarea-explain"><? echo $actionExplain; ?></textarea>
		</div>
		<button type="submit" data-theme="c" name="action-submit" value="submit-value">Save</button>
	</div><!-- /content -->
</form>	
</div><!-- /page four -->

<!-- Start of fifth page: #privy -->
<div data-role="page" id="privy" data-theme="c">
	<div data-role="header">
		<h1>Privy Checklist</h1>
		<a href="/" class="ui-btn-right" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a> 
	</div>
	<form action="details.php" method="post" data-ajax="false">
	<input type="hidden" name="id" value="<? echo $theTripId ?>" />	
	<div data-role="content" data-theme="c">
		<div data-role="fieldcontain">
			<label for="privy-slider1">Structure intact?</label>
			<select name="privy-slider1" id="privy-slider1" data-role="slider">
				<option value="0" <? if (!$privyStructureIntact) echo 'selected="selected"'; ?>>no</option>
				<option value="1" <? if ($privyStructureIntact) echo 'selected="selected"'; ?>>yes</option>
			</select> 
		</div>	
		<div data-role="fieldcontain">
			<label for="privy-slider2">Stain adequate?</label>
			<select name="privy-slider2" id="privy-slider2" data-role="slider">
				<option value="0" <? if (!$privyStainsAdequate) echo 'selected="selected"'; ?>>no</option>
				<option value="1" <? if ($privyStainsAdequate) echo 'selected="selected"'; ?>>yes</option>
			</select> 
		</div>	
		<div data-role="fieldcontain">
    		<fieldset data-role="controlgroup" data-type="horizontal">
			<legend>How much room left in crib/pit/bin?</legend>
				<input type="radio" name="privy-radio-choice-1" id="radio-choice-1" value="1/4" <? if ($privyRoomInCribText == '1/4') echo 'checked="checked"'; ?> />
         		<label for="radio-choice-1">1/4</label>

         		<input type="radio" name="privy-radio-choice-1" id="radio-choice-2" value="1/2" <? if ($privyRoomInCribText == '1/2') echo 'checked="checked"'; ?> />
         		<label for="radio-choice-2">1/2</label>

         		<input type="radio" name="privy-radio-choice-1" id="radio-choice-3" value="3/4" <? if ($privyRoomInCribText == '3/4') echo 'checked="checked"'; ?> />
         		<label for="radio-choice-3">3/4</label>
         		
         		<input type="radio" name="privy-radio-choice-1" id="radio-choice-4" value="full" <? if ($privyRoomInCribText == 'full') echo 'checked="checked"'; ?> />
         		<label for="radio-choice-4">full</label>         		
			</fieldset>
		</div>
		<div data-role="fieldcontain">
			<label for="privy-textarea">Comments:</label>
			<textarea cols="40" rows="8" name="privy-textarea" id="privy-textarea"><? echo $privyComments; ?></textarea>
		</div>
		<button type="submit" data-theme="c" name="privy-submit" value="submit-value">Save</button>
	</div><!-- /content -->
</form>	
</div><!-- /page five -->

<!-- Start of sixth page: #signs -->
<div data-role="page" id="signs" data-theme="c">
	<div data-role="header">
		<h1>Signs</h1>
		<a href="/" class="ui-btn-right" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a> 
	</div>
	<form action="details.php" method="post" data-ajax="false">
	<input type="hidden" name="id" value="<? echo $theTripId ?>" />
	<div data-role="content" data-theme="c">	
		<div data-role="fieldcontain">
    		<fieldset data-role="controlgroup" data-type="horizontal">
				<legend>Site Map (hand made to suit site):</legend>
				<input type="radio" name="signs-radio-choice-1" id="radio-choice-1" value="ok" <? if ($signsSiteMap == 'ok') echo 'checked="checked"'; ?> />
         		<label for="radio-choice-1">Ok</label>

         		<input type="radio" name="signs-radio-choice-1" id="radio-choice-2" value="lost" <? if ($signsSiteMap == 'lost') echo 'checked="checked"'; ?> />
         		<label for="radio-choice-2">Lost</label>

         		<input type="radio" name="signs-radio-choice-1" id="radio-choice-3" value="replace" <? if ($signsSiteMap == 'replace') echo 'checked="checked"'; ?> />
         		<label for="radio-choice-3">Replace</label>
    		</fieldset>
		</div>
		<div data-role="fieldcontain">
    		<fieldset data-role="controlgroup" data-type="horizontal">
				<legend>Shelter/Site Log:</legend>
				<input type="radio" name="signs-radio-choice-2" id="radio-choice-4" value="ok" <? if ($signsShelterLog == 'ok') echo 'checked="checked"'; ?> />
         		<label for="radio-choice-4">Ok</label>

         		<input type="radio" name="signs-radio-choice-2" id="radio-choice-5" value="lost" <? if ($signsShelterLog == 'lost') echo 'checked="checked"'; ?> />
         		<label for="radio-choice-5">Lost</label>

         		<input type="radio" name="signs-radio-choice-2" id="radio-choice-6" value="replace" <? if ($signsShelterLog == 'replace') echo 'checked="checked"'; ?> />
         		<label for="radio-choice-6">Replace</label>
    		</fieldset>
		</div>
		<div data-role="fieldcontain">
    		<fieldset data-role="controlgroup" data-type="horizontal">
				<legend>Leave No Trace:</legend>
				<input type="radio" name="signs-radio-choice-3" id="radio-choice-7" value="ok" <? if ($signsLeaveNoTrace == 'ok') echo 'checked="checked"'; ?> />
         		<label for="radio-choice-7">Ok</label>

         		<input type="radio" name="signs-radio-choice-3" id="radio-choice-8" value="lost" <? if ($signsLeaveNoTrace == 'lost') echo 'checked="checked"'; ?> />
         		<label for="radio-choice-8">Lost</label>

         		<input type="radio" name="signs-radio-choice-3" id="radio-choice-9" value="replace" <? if ($signsLeaveNoTrace == 'replace') echo 'checked="checked"'; ?> />
         		<label for="radio-choice-9">Replace</label>
    		</fieldset>
		</div>
		<div data-role="fieldcontain">
    		<fieldset data-role="controlgroup" data-type="horizontal">
				<legend>Water Not Tested:</legend>
				<input type="radio" name="signs-radio-choice-4" id="radio-choice-10" value="ok" <? if ($signsWaterNotTested == 'ok') echo 'checked="checked"'; ?> />
         		<label for="radio-choice-10">Ok</label>

         		<input type="radio" name="signs-radio-choice-4" id="radio-choice-11" value="lost" <? if ($signsWaterNotTested == 'lost') echo 'checked="checked"'; ?> />
         		<label for="radio-choice-11">Lost</label>

         		<input type="radio" name="signs-radio-choice-4" id="radio-choice-12" value="replace" <? if ($signsWaterNotTested == 'replace') echo 'checked="checked"'; ?> />
         		<label for="radio-choice-12">Replace</label>
    		</fieldset>
		</div>
		<h4>Sign Request:</h4>
		<div data-role="fieldcontain">
		<label for="signs-textarea">Please write text (as it should appear on sign including directional arrow(s) if needed) below.</label>
			<textarea cols="40" rows="8" name="signs-textarea" id="textarea"><? echo $signsRequest; ?></textarea>
		</div>	
		<button type="submit" data-theme="c" name="signs-submit" value="submit-value">Save</button>
	</div><!-- /content -->
</form>	
</div><!-- /page six -->

<!-- Start of fourth page: #outdoor -->
<div data-role="page" id="outdoor" data-theme="c">
	<div data-role="header">
		<h1>Outdoor Misc.</h1>
		<a href="/" class="ui-btn-right" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a> 
	</div>
<form action="details.php" method="post" data-ajax="false">
<input type="hidden" name="id" value="<? echo $theTripId ?>" />
	<div data-role="content" data-theme="c">
		<div data-role="fieldcontain">
			<label for="outdoor-firering-one">Only One Firering?</label>
			<select name="outdoor-firering-one" id="outdoor-firering-one" data-role="slider">
				<option value="0" <? if (!$fireringOne) echo 'selected="selected"'; ?>>no</option>
				<option value="1" <? if ($fireringOne) echo 'selected="selected"'; ?>>yes</option>
			</select> 
		</div>	
		<div data-role="fieldcontain">
			<label for="outdoor-firering-clean">Firering Clean?</label>
			<select name="outdoor-firering-clean" id="outdoor-firering-clean" data-role="slider">
				<option value="0" <? if (!$fireringClean) echo 'selected="selected"'; ?>>no</option>
				<option value="1" <? if ($fireringClean) echo 'selected="selected"'; ?>>yes</option>
			</select> 
		</div>	
		<div data-role="fieldcontain">
			<label for="outdoor-washpit-exists">Washpit Exists?</label>
			<select name="outdoor-washpit-exists" id="outdoor-washpit-exists" data-role="slider">
				<option value="0" <? if (!$washpitExists) echo 'selected="selected"'; ?>>no</option>
				<option value="1" <? if ($washpitExists) echo 'selected="selected"'; ?>>yes</option>
			</select> 
		</div>	
		<div data-role="fieldcontain">
			<label for="outdoor-washpit-screen">Washpit Screen Intact?</label>
			<select name="outdoor-washpit-screen" id="outdoor-washpit-screen" data-role="slider">
				<option value="0" <? if (!$washpitScreenIntact) echo 'selected="selected"'; ?>>no</option>
				<option value="1" <? if ($washpitScreenIntact) echo 'selected="selected"'; ?>>yes</option>
			</select> 
		</div>	
		<div data-role="fieldcontain">
			<label for="outdoor-washpit-clean">Washpit Clean?</label>
			<select name="outdoor-washpit-clean" id="outdoor-washpit-clean" data-role="slider">
				<option value="0" <? if (!$washpitClean) echo 'selected="selected"'; ?>>no</option>
				<option value="1" <? if ($washpitClean) echo 'selected="selected"'; ?>>yes</option>
			</select> 
		</div>	
		<div data-role="fieldcontain">
			<label for="outdoor-watersource">Watersource Adequate?</label>
			<select name="outdoor-watersource" id="outdoor-watersource" data-role="slider">
				<option value="0" <? if (!$watersourceAdequate) echo 'selected="selected"'; ?>>no</option>
				<option value="1" <? if ($watersourceAdequate) echo 'selected="selected"'; ?>>yes</option>
			</select> 
		</div>	
		<div data-role="fieldcontain">
			<label for="outdoor-garbage">Garbage Removed?</label>
			<select name="outdoor-garbage" id="outdoor-garbage" data-role="slider">
				<option value="0" <? if (!$garbageRemoved) echo 'selected="selected"'; ?>>no</option>
				<option value="1" <? if ($garbageRemoved) echo 'selected="selected"'; ?>>yes</option>
			</select> 
		</div>	

		<div data-role="fieldcontain">
		<label for="outdoor-comments">Additional Comments:</label>
			<textarea cols="40" rows="8" name="outdoor-comments" id="outdoor-comments"><? echo $outdoorComments; ?></textarea>
		</div>
		<button type="submit" data-theme="c" name="outdoor-submit" value="submit-value">Save</button>
	</div><!-- /content -->
</form>	
</div><!-- /page outdoor -->

<!-- Start of seventh page: #comments -->
<div data-role="page" id="comments" data-theme="c">
	<div data-role="header">
		<h1>Additional Comments</h1>
		<a href="/" class="ui-btn-right" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a> 
	</div>
<form action="details.php" method="post" data-ajax="false">
<input type="hidden" name="id" value="<? echo $theTripId ?>" />
	<div data-role="content" data-theme="c">
		<h2>Additional Comments</h2>
		<div data-role="fieldcontain">
			<label for="textarea-additional">(Please list names of additional volunteers here.)</label>
			<textarea cols="40" rows="8" name="textarea-additional" id="textarea-additional"><? echo $additionalComments; ?></textarea>
		</div>
		<button type="submit" data-theme="c" name="comments-submit" value="submit-value">Save</button>
	</div><!-- /content -->
</form>	
</div><!-- /page seven -->

<?php
include('includes/footer.inc.php'); 
?>