<?php
//Start session
session_start();
$error_info="";

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
if(isset($_SESSION['loggedin']) && $_SESSION['loggedin']===true)
{


	header("location: admin.php",true,302);
	exit;
}

require_once('../mysqli_connect.php');

$userName="";
$passWord="";
$userErr="";
$passErr="";

if($_SERVER["REQUEST_METHOD"] == "POST"){
	
	if(isset($_POST['submit']))
	{
		$userName = $_POST['uname'];
		$passWord = $_POST['pwd'];
		$query = "select hashed_password from admins where username = '$userName';";

		$response = mysqli_query($dbc,$query);

		if($response)
		{
			$row = mysqli_fetch_array($response);
			if(empty($row['hashed_password']))
			{
				$userErr = 'Username does not exist';
			}
			else if(password_verify($passWord, $row['hashed_password']))
			{
				session_start();
				$_SESSION["loggedin"] = true;
				$_SESSION['username'] = $userName;
				header("location: admin.php",true,302);
			}
			else
				$passErr = "Password not matched";
		}

		else
		{
			echo "Query failed to execute<br/>";
			echo mysqli_error($dbc);
		}

		mysqli_close($dbc);
	}

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

<div class="login">
	<p style="font-size: 36pt">Login</p>
	<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
	<p>Username <input type="text" name="uname" value="" required /></p>
	<p>Password <input type="password" name="pwd" value="" required /></p>
	<input type="submit" name="submit" value="Submit" id="submit" />

	<p class="error">
		<?php
		if(!empty($passErr))
			echo $passErr.'<br/>';
		if(!empty($userErr))
			echo $userErr.'<br/>';
		?>
	</p>	
	</form>	
</div>
</div>

</body>
</html>
