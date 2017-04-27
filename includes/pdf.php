<?php

//this was adapted from http://www.tcpdf.org/index.php

session_start();

require ('../includes/config.inc.php'); 

if (($_SESSION['user_level'] != '1') || (!isset($_GET['id']))) {
	// Redirect the user:
	$url = BASE_URL . '../index.php'; // Define the URL.
	header("Location: $url");
	exit(); // Quit the script.
}

function yesOrNo ($oneOrZero) {
	if ($oneOrZero) {
		return "Yes";
	} else {
		return "No";
	}
}

$theTripId = $_GET['id'];
require (MYSQL);

// initialize statement
$stmt = $dbc->stmt_init();

// get details of selected record
if (isset($theTripId)) {
  // prepare SQL query
  $sql = 'SELECT trips.users_id,
  trips.shelters_id,
  trips.work_completed, 
  trips.additional_comments, 
  trips.structure_walls_intact,
  trips.structure_floor_intact,
  trips.structure_stain_adequate,
  trips.structure_rot_present,
  trips.structure_leaks_present, 
  trips.structure_sills_rot_present, 
  trips.structure_sills_silt,
  trips.structure_roof_material,
  trips.privy_structure_intact,
  trips.privy_stains_adequate, 
  trips.privy_comments,
  trips.privy_room_in_crib_text,
  trips.signs_site_map,
  trips.signs_shelter_log, 
  trips.signs_leave_no_trace,
  trips.signs_water_not_tested,
  trips.signs_request,
  trips.action_still_todo,
  trips.action_urgency, 
  trips.action_need_help,
  trips.action_explain,
  trips.hours_travel,
  trips.hours_work,	
  trips.number_of_people, 
  trips.trip_name,
  DATE_FORMAT(trips.visit_date, "%M %D, %Y"),
  trips.firering_only_one,
  trips.firering_pit_clean,	
  trips.washpit_exists,	
  trips.washpit_screen_intact,	
  trips.washpit_clean,	
  trips.watersource_adequate,	
  trips.garbage_removed,	
  trips.outdoor_comments,
  trips.submitted,
  trips.removed,
  trips.modified_datetime,
  shelters.shelters_name,
  users.first_name,
  users.last_name,
  users.email
  FROM trips 
  INNER JOIN shelters ON shelters.shelters_id = trips.shelters_id
  INNER JOIN users ON users.user_id = trips.users_id
  WHERE trips_id = ?';
  if ($stmt->prepare($sql)) {
	$stmt->bind_param('i', $theTripId);
	// bind the results to variables
	$stmt->bind_result($usersId,
	$sheltersId,
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
  	$shelterName,
  	$firstName,
  	$lastName,
  	$email);
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

require_once('tcpdf/eng.php');
require_once('tcpdf/tcpdf.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
//$pdf->SetCreator(PDF_CREATOR);
//$pdf->SetAuthor('Nicola Asuni');
//$pdf->SetTitle('TCPDF Example 001');
//$pdf->SetSubject('TCPDF Tutorial');
//$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// ---------------------------------------------------------

// set default font subsetting mode
$pdf->setFontSubsetting(true);

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
$pdf->SetFont('times', '', 14, '', true);

// Add a page
// This method has several options, check the source code documentation for more information.
$pdf->AddPage();

// Set some content to print
$html = '<h2>General Information</h2>
<table border="1" cellpadding="5">
<tr>
<td colspan="2">Date of Visit: '.$visitDate.' </td>
</tr>
<tr>
<td colspan="2">Shelter Assignment: '. $shelterName.'</td>
</tr>
<tr>
<td>Name: '.$firstName.' '.$lastName.'</td>
<td>Email: '.$email.'</td>
</tr>
</table>
<p></p>
<h2>Hours</h2>
<table border="1" cellpadding="5">
<tr>
<td>Travel:  '. $hoursTravel.' Hours/Person (including driving and hiking time.)</td>
</tr>
<tr>
<td>Field Work: '. $hoursWork.' Hours/Person</td>
</tr>
<tr>
<td>Number of People: '. $numberOfPeople.'</td>
</tr>
</table>
<p></p>
<h2>Work Completed During This Visit</h2>
<table border="1" cellpadding="5">
<tr>
<td>'. nl2br($workCompleted).'</td>
</tr>
</table>
<p></p>
<h2>Shelter/Tent Platform Structure Checklist</h2>
<table border="1" cellpadding="5">
<tr>
<td>Walls intact? '.yesOrNo($wallsIntact).'</td>
<td class="three">Floors Intact? '. yesOrNo($floorIntact).'</td>
<td class="three">Stain Adequate? '. yesOrNo($stainAdequate).'</td>
</tr>
<tr>
<td>Roof Material: '. $structureRoofMaterial.'</td>
<td class="three">Rot Present? '. yesOrNo($rotPresent).'</td>
<td class="three">Leaks Present? '. yesOrNo($leaksPresent).'</td>
</tr>
<tr>
<td>Sills - Rot Present? '. yesOrNo($sillsRotPresent).'</td>
<td colspan="2">Silt/Leaves against sills? '. yesOrNo($sillsSilt).'</td>
</tr>
</table>
<p></p>
<h2>Privy Checklist</h2>
<table border="1" cellpadding="5">
<tr>
<td>Structure intact? '. yesOrNo($privyStructureIntact).'</td>
<td>Stain Adequate? '. yesOrNo($privyStainsAdequate).'</td>
</tr>
<tr>
<td colspan="2">Room left in crib/pit/bin? '. $privyRoomInCribText.'</td>
</tr>
<tr>
<td colspan="2">Comments: '. nl2br($privyComments).'</td>
</tr>
</table>
<p></p>
<h2>Outdoor Misc</h2>
<table border="1" cellpadding="5">
<tr>
<td>Fire Ring: One ring present? '. yesOrNo($fireringOne).'</td>
<td colspan="2">Pit clean? '. yesOrNo($fireringClean).'</td>
</tr>
<tr>
<td>Wash Pit: Exists? '. yesOrNo($washpitExists).'</td>
<td class="three">Screen Intact? '. yesOrNo($washpitScreenIntact).'</td>
<td class="three">Pit clean? '. yesOrNo($washpitClean).'</td>
</tr>
<tr>
<td>Water Source: Adequate? '. yesOrNo($watersourceAdequate).'</td>
<td colspan="2">Garbage: Trash removed? '. yesOrNo($garbageRemoved).'</td>
</tr>
<tr>
<td colspan="3">Comments: '. nl2br($outdoorComments).'</td>
</tr>
</table>
<p></p>
<h2>Action Items</h2>
<table border="1" cellpadding="5">
<tr>
<td colspan="2">What still needs to be done? '. nl2br($actionStillTodo).'</td>
</tr>
<tr>
<td>How Urgent? '. $actionUrgency.'</td>
<td>Do you need help? '. yesOrNo($actionNeedHelp).'</td>
</tr>
<tr>
<td colspan="2">Explain: '. nl2br($actionExplain).'</td>
</tr>
</table>
<p></p>
<h2>Signs</h2>
<table border="1" cellpadding="5">
<tr>
<td>Site Map (hand made to suit site)? '. $signsSiteMap.'</td>
<td>Shelter/Site Log: '. $signsShelterLog.'</td>
</tr>
<tr>
<td>Leave No Trace: '. $signsLeaveNoTrace.'</td>
<td>Water Not Tested: '. $signsWaterNotTested.'</td>
</tr>
</table>
<p></p>
<h2>Signs Request</h2>
<table border="1" cellpadding="5">
<tr>
<td>'. nl2br($signsRequest).'</td>
</tr>
</table>
<p></p>
<h2>Additional Comments</h2>
<table border="1" cellpadding="5">
<tr>
<td>'. nl2br($additionalComments).'</td>
</tr>
</table>';

// Print text using writeHTMLCell()
$pdf->writeHTML($html, true, false, false, false, '');

// ---------------------------------------------------------
ob_clean();
// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output('report.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+