<!DOCTYPE html>
<html>
<head>
<title>Long Trail Admin <?php if (isset($title)) {echo "| {$title}";} ?></title>

<link rel="stylesheet" href="/css/blue/style.css" type="text/css" media="print, projection, screen" />
<link rel="stylesheet" href="/css/styles.css" type="text/css" media="print, projection, screen" />
<link rel="stylesheet" href="/css/demo_table_jui.css" type="text/css" media="print, projection, screen" />
<link rel="stylesheet" href="/css/jquery-ui-1.8.4.custom.css" type="text/css" media="print, projection, screen" />

<script src="http://code.jquery.com/jquery-1.6.4.min.js"></script>
<script src="/lib/jquery.tablesorter.min.js"></script>
<script src="/lib/jquery.dataTables.min.js"></script> 
<script src="/lib/jquery.simplemodal.1.4.1.min.js"></script> 
<script src="/lib/jquery.validate.js"></script>	
<script src="/lib/admin.js"></script>

<? 
//this will only run once a year.  A popup to tell then that the admin area works best not on mobile.
if (!isset($_COOKIE['admin-message']))  {
?>
<script>
function setCookie(c_name,value,exdays) {
	var exdate=new Date();
	exdate.setDate(exdate.getDate() + exdays);
	var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
	document.cookie=c_name + "=" + c_value;
}

function isMobile() {
	var index = navigator.appVersion.indexOf("Mobile");
	return (index > -1);
}

if (isMobile()) {
	alert("Admin area is best viewed on a desktop computer.");
	setCookie("admin-message","yes",365);
}
</script>
<? 
} 
?>

</head>
<body class="admin">