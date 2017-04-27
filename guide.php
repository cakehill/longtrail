<?php
//start the session
session_start();

//include everything that is needed for this page.
require ('includes/config.inc.php'); 

//check to make sure they can access this area
checkPermission('user');
?>

<div data-role="page" class="gallery-page" id="gallery">
	<div data-role="header">
		<h1>Guide</h1>
		<a href="/" class="ui-btn-right" data-icon="home" data-iconpos="notext" data-direction="reverse">Home</a> 
	</div>
	<div data-role="content">	
		<ul class="gallery">
		
			<li><a href="/images/guide/NYNJMaint101-1.png" rel="external"><img src="/images/guide/NYNJMaint101-1.png" alt="Image 010" /></a></li>
			<li><a href="/images/guide/NYNJMaint101-2.png" rel="external"><img src="/images/guide/NYNJMaint101-2.png" alt="Image 011" /></a></li>
			<li><a href="/images/guide/NYNJMaint101-3.png" rel="external"><img src="/images/guide/NYNJMaint101-3.png" alt="Image 012" /></a></li>
			<li><a href="/images/guide/NYNJMaint101-4.png" rel="external"><img src="/images/guide/NYNJMaint101-4.png" alt="Image 013" /></a></li>
			<li><a href="/images/guide/NYNJMaint101-5.png" rel="external"><img src="/images/guide/NYNJMaint101-5.png" alt="Image 014" /></a></li>
			<li><a href="/images/guide/NYNJMaint101-6.png" rel="external"><img src="/images/guide/NYNJMaint101-6.png" alt="Image 015" /></a></li>
	
		</ul>

	</div><!-- /content -->
</div>
