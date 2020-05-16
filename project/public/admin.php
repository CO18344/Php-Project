<?php
session_start();
$error_info="";


//session expire
$time = $_SERVER['REQUEST_TIME'];

$timeout_duration = 120;

if (isset($_SESSION['LAST_ACTIVITY']) && 
   ($time - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    $error_info = 222;
    session_unset();
    session_destroy();
    session_start();
}

$_SESSION['LAST_ACTIVITY'] = $time;
//end

if(!isset($_SESSION['username']))
{
	header("location: index.php");
	exit;
}



?>

<!DOCTYPE html>
<html>
<head>
	<title>Widget Corp</title>
	<link rel="stylesheet" href="manageContent.css">		
</head>
<body>
	
<header>
	Widget Corp Admin
</header>
<div class="container">
<div class="sidenav">

</div>

<div class="welcome">
	<p style="font-size: 36pt; color:  #8d0d19; margin-bottom: 10px">Admin Menu</p>
	Welcome to the admin area , <?php echo $_SESSION['username'];?>
	<div>
		<ul>
			<li><a href="manageContent.php">Manage Website Content</a></li>
			<li><a href="manageAdmin.php">Manage Admin Users</a></li>
			<li><a href="logout.php">Logout</a></li>
		</ul>
	</div>
</div>
</div>

</body>
</html>
