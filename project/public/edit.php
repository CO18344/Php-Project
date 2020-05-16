<?php
//Start session
session_start();
require_once('../mysqli_connect.php');

$error_info="";
$currentUser="";
$result = "";
$currentPass="";

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

if(isset($_SESSION['loggedin']) && $_SESSION['loggedin']===true )
{


if(isset($_POST['submit']))
{
	$hashed_password= password_hash($_POST['pwd'], PASSWORD_DEFAULT);
	$userName=$_POST['uname'];
	$idEdit=$_POST['id'];
$query = "update admins set username='$userName', hashed_password='$hashed_password' where id='$idEdit';";
$response = mysqli_query($dbc,$query);
if($response)
{
	$affected = mysqli_affected_rows($dbc);
	if($affected > 0 )
		$result = 'Information updated';
	else
		$result = 'Invalid data found';
}
else
{
	$result = "Query failed to execute";
}
$_SESSION['editStatus']=$result;
header('location: manageAdmin.php',true,302);
exit;
}
else
{

$idEdit = $_GET['id'];

$query = "select username from admins where id = '$idEdit';";
$response = mysqli_query($dbc,$query);

if($response)
{
	$row = mysqli_fetch_array($response);
	if(!empty($row['username']))
	{
		$currentUser = $row['username'];
		$result = 'update username or password';
	}
	else
	{
		$result = 'Invalid input';
	}

}

else
{
	$result = "Query failed to execute";
}


}

$_SESSION['editStatus']=$result;
}
else
$error_info='22';

if(!empty($error_info))
{
	header('location: error.php?error_code='.$error_info);
	exit;
}

$dbc->close();

?>




<!DOCTYPE html>
<html>
<head>
	<title>Edit</title>
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
	<p style="font-size: 36pt">Update</p>
	<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
	<p>ID <input readonly type="text" name="id" value="<?php echo $idEdit;?>" required /></p>
	<p>Username <input type="text" name="uname" value="<?php echo htmlspecialchars($currentUser); ?>" required /></p>
	<p>Password <input type="password" name="pwd" value="" required /></p>
	<input type="submit" name="submit" value="Submit" id="submit" />
	</form>	

	<p>
		<?php
			echo $_SESSION['editStatus'];
		?>
	</p>
</div>
</div>

<script type="text/javascript" src="index.js">
	
</script>
</body>
</html>


