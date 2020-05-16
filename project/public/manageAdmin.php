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

if(isset($_SESSION['loggedin']) && $_SESSION['loggedin']===true )
{



require_once('../mysqli_connect.php');

$query = "select username,id from admins;";
$response = mysqli_query($dbc,$query);

if($response)
{
	$usernames = array();
	$ids = array();

	while($row = mysqli_fetch_array($response))
	{
		array_push($usernames, $row['username']);
		array_push($ids, $row['id']);
	}
}

}
else
{
    header("location: index.php");
	exit;
}


$dbc->close();
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
	<!-- ← 	&#8592; 	&larr; 	LEFTWARDS ARROW -->
	<p><a href="admin.php" id="mainMenu"> &larr; Main Menu</a></p>
</div>

<div class="manageScreen">
	<p style="font-size: 36pt; color:  #8d0d19; margin-bottom: 10px">Manage Admins</p>
	<div class="container">
		<div>
			<strong>Username</strong>
			<?php
			foreach($usernames as $name)
			{
				echo '<p>'.$name.'</p>';
			}

			?>
		</div>
		<div id="actions">
			<strong>Actions</strong>
			<?php
			foreach($ids as $id)
			{
				if( $id == '1' && $_SESSION['username'] != 'satvik')
				{

				echo '<p style="width: 150px;">';
				echo '<a href=# class="disabled">Edit</a> &nbsp; ';
				echo '<a href=# class="disabled">Delete</a>';
				echo '</p>';
			
				}
				else
				{

				echo '<p style="width: 150px;">';	
				echo '<a href=edit.php?id='.$id.'>Edit</a> &nbsp;';
				echo '<a href=delete.php?id='.$id.'>Delete</a>';
				echo '</p>';
				}
			}

			?>
		</div>
	</div>
	<ul>
		<li><a href="add.php">Add new admin</a></li>
	</ul>

	<?php
	if(isset($_SESSION['deleteStatus']))
	{

		echo '<br/><b>'.$_SESSION['deleteStatus'].'</b>';
		$_SESSION['deleteStatus']="";
	}
	if(isset($_SESSION['editStatus']))
	{

		echo '<br/><b>'.$_SESSION['editStatus'].'</b>';
		$_SESSION['editStatus']="";
	}
	?>
</div>

</div>

<script type="text/javascript" src="admin.js">

</script>
</body>
</html>
