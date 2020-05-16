<?php

$error_info="";
session_start();

//Code to manage session expire
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
//End 

if(isset($_SESSION['loggedin']) && $_SESSION['loggedin']===true)
{

require_once('../mysqli_connect.php');

function isalready()
{
	global $dbc;
	$queryToSee = 'select username from admins;';
	$resp1 = mysqli_query($dbc,$queryToSee);	

	while($row1 = mysqli_fetch_array($resp1))
	{
		if($row1['username']===$_POST['uname'])
			return false;
	}
	return true;
}

$result="";
$userName="";
$passWord="";
$cfpassWord="";
$error="";

if($_SERVER["REQUEST_METHOD"] == "POST"){
	
	if(isset($_POST['submit']))
	{
		$userName =$dbc->real_escape_string($_POST['uname']);
		$passWord = ($_POST['pwd']);
		$cfpassWord= ($_POST['cfpwd']);
		if(isalready()===true)
		{

			if($passWord===$cfpassWord)
			{
				$hashed_password=password_hash($passWord, PASSWORD_DEFAULT);

			$query = "insert into admins (username,hashed_password) values ('$userName','$hashed_password');";
			$response = mysqli_query($dbc,$query);		
			
				if($response)
				{
					$_SESSION['admin_added'] = 'Admin added successfully';
				}
				else
					$error_info = "792";

			}
			else
				$error_info="554";

		}
		else
			$error_info = '555';
			
		
		mysqli_close($dbc);
	}

}

}
else
{
	$error_info = '22';
}

if(!empty($error_info))
{
	header('location: error.php?error_code='.$error_info);
	exit;
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Add admins</title>
	<link rel="stylesheet" href="manageContent.css">		
</head>
<body>
	
<header>
	Widget Corp Admin
</header>
<div class="container">

	<div class="sidenav">
		<!-- ← 	&#8592; 	&larr; 	LEFTWARDS ARROW -->
		<p><a href="admin.php" id="mainMenu"> &larr; Main Menu</a></p>
	</div>

	<div class="manageScreen">
		<p style="font-size: 36pt">Sign up</p>
		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
		<p>Username <input type="text" name="uname" value="" required /></p>
		<p>Password <input type="password" name="pwd" value="" required /></p>
		<p>Confirm Password <input type="password" name="cfpwd" value="" required /></p>
		<input type="submit" name="submit" value="Submit" id="submit" />	
		</form>	
	

		<div class="success">
			<?php
				if(!empty($_SESSION['admin_added']))
				{
					echo $_SESSION['admin_added'].'..........';;
					$_SESSION['admin_added']="";
				}
			?>
		</div>
	</div>
</div>


</body>
</html>