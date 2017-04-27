<?
$path = $_SERVER['PHP_SELF'];
$page = basename($path);
$page = basename($path, '.php');
?>

<div id="wrap">
<img src="/images/Sleeping-Shelter-48.png" height="48" width="48" alt="" class="adminlogo" /> <h1 class="header">Shelter Maintenance Admin Area</h1>
<ul id="nav">
<li><a href="/">App home</a></li>
<li><a href="admin.php" <? if($page == 'admin'): ?> class="active"<? endif ?>>Admin home</a></li>
<li><a href="admin-users.php" <? if($page == 'admin-users'): ?> class="active"<? endif ?>>Users</a></li>
<li><a href="admin-alerts.php" <? if($page == 'admin-alerts'): ?> class="active"<? endif ?>>Alerts</a></li>
<li><a href="admin-shelters.php" <? if($page == 'admin-shelters'): ?> class="active"<? endif ?>>Shelters</a></li>
<li><a href="admin-trips.php" <? if($page == 'admin-trips'): ?> class="active"<? endif ?>>Trips</a></li>
<li><a href="/login/logout.php">Logout</a></li>
</ul>