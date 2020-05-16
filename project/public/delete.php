<?php
//Start session
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

if(isset($_SESSION['loggedin']) && $_SESSION['loggedin']===true)
{


$result = "";
require_once('../mysqli_connect.php');
$idRemove = $_GET['id'];

if($idRemove == '1' && $_SESSION['username'] != 'satvik')
{
	$error_info = "1000";
	header('location: error.php?error_code='.$error_info,true,302);
	exit;
}
else
{
$query = "delete from admins where id = '$idRemove';";
$response = mysqli_query($dbc,$query);

if($response)
{
	$affected = mysqli_affected_rows($dbc);
	if($affected>0)
	{
		$result = "Removed Successfully";
	}
	else
		$result = "Invalid username";
	
}

else
{
	$result = "Query failed to execute";
}

$_SESSION['deleteStatus']=$result;

header("location: manageAdmin.php",true,302);
exit;
}

}
else
$error_info = '22';

if(!empty($error_info))
{
	header('location: error.php?error_code='.$error_info,true,302);
	exit;
}

?>