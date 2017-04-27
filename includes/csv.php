<?php
session_start();

require ('../includes/config.inc.php'); 

if ($_SESSION['user_level'] != '1') {
	// Redirect the user:
	$url = BASE_URL . '../login/index.php'; // Define the URL.
	header("Location: $url");
	exit(); // Quit the script.
}

// Need the database connection:
require (MYSQL);

// Connect to the database
$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
mysql_select_db(DB_NAME);
require ('../includes/exportcsv.inc.php');
 
$ref = getenv("HTTP_REFERER"); 

if(strstr($ref,'users')) {
	$table="users";
} elseif(strstr($ref,'shelters')) {
	$table="shelters";
} elseif(strstr($ref,'alerts')) {
	$table="alerts";
}

exportMysqlToCsv($table);

?>
