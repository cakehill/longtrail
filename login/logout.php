<?php # Script 18.9 - logout.php
// This is the logout page for the site.

session_start();

require ('../includes/config.inc.php'); 
$page_title = 'Logout';
include ('../includes/header.inc.php'); 

// If no first_name session variable exists, redirect the user:
if (!isset($_SESSION['first_name'])) {

	$url = BASE_URL . 'index.php'; // Define the URL.
	ob_end_clean(); // Delete the buffer.
	header("Location: $url");
	exit(); // Quit the script.
	
} else { // Log out the user.

	setcookie("token","",mktime(12,0,0,1, 1, 1990), "/");
	$_SESSION = array(); // Destroy the variables.
	session_destroy(); // Destroy the session itself.
	setcookie (session_name(), '', time()-3600); // Destroy the cookie.

}
?>
<body>
<div data-role="page" data-theme="c">
	<div data-role="header">
		<h1>Logged Out</h1>
	</div>
	<div data-role="content" data-theme="c">	
	<p>Thanks, you are now logged out.</p>
	<a href="/login/index.php" data-role="button" data-theme="c" rel="external">Log in</a>  
	</div>
</div>

<?
include ('../includes/footer.inc.php');
?>