<?php
session_start();

$error_info="";
require_once('../mysqli_connect.php');

//Session expire code
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
		$menuNameSubject =$dbc->real_escape_string($_POST['menu_name']);
		$posSubject = $_POST['position'];
		$visibleSubject = $_POST['visibility'];

		$local_query = "select count(id) as count from subjects;";
		$local_response = mysqli_query($dbc,$local_query);
		if($local_response)
		{
			$local_row = mysqli_fetch_array($local_response);
			if(!empty($local_row['count']))
			{
				//$local_idSubject = $local_row['subject_id'];
			if(array_key_exists('count', $local_row) && is_null($local_row['count'])===false)
			{	

				$local_count=$local_row['count']+1;
				if($posSubject > $local_count)
					$posSubject=$local_count;
				else if($posSubject <= $local_count - 1)
				{
				$local_query = "update subjects set position='$local_count' where position='$posSubject';";
				$local_response = mysqli_query($dbc,$local_query);
				if(mysqli_affected_rows($dbc)>0)
				{
					echo 'Cool';

				}



				}
			}

			}
		}
		$error_info = '792';

		$query="insert into subjects (menu_name,position,visible) values ('$menuNameSubject','$posSubject','$visibleSubject');";


		$response = mysqli_query($dbc,$query);

		if($response)
		{
			$_SESSION['subject_added']='Subject added successfully';
			mysqli_close($dbc);
			header('location: manageContent.php');
			exit;
		}
		else
		{
			$error_info = "712"; 
		}


	}
}
else
{
	$error_info = "22";
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
	<title>Add Subject</title>
	<link rel="stylesheet" href="manageContent.css">	
</head>
<body>
	
<header>
	Widget Corp Admin
</header>
<div class="container">

	<div class="sidenav">
		<p><a href="admin.php"> &larr; Main Menu</a></p>
	</div>

	<?php
	
	$local_query = "select count(id) as count from subjects;";
	$local_response = mysqli_query($dbc,$local_query);
	if($local_response)
		{
			$local_row = mysqli_fetch_array($local_response);
			if(isset($local_row['count']))
			{
				//$local_idSubject = $local_row['subject_id'];
			if(array_key_exists('count', $local_row) && is_null($local_row['count'])===false)
			{	

				$local_count=$local_row['count']+1;
				$max=$local_count;

			}

			}
		}

	?>
	<div class="manageScreen">
		 <p style="font-size: 36pt; color:  #8d0d19; margin-bottom: 10px">Subject Details</p> 
		 <form action='<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>' method="post">
		 	<p>Menu Name<input type="text" name="menu_name" value="" required /></p>
		 	<p>Position<input type="number" name="position" value="<?php echo $max; ?>" min="1" max="<?php echo $max;?>" required /></p>
		 	<p id="radio">Visibility
		 		<br/>
		 		<input type="radio" name="visibility" value="yes" id="yes" checked required />
		 		<label for="yes">Yes</label>
		 		<br/>
		 		<input type="radio" name="visibility" value="no" id="no" required />
		 		<label for="no">No</label>	
		 	</p>
		 	<input type="submit" name="submit" value="Add subject" required />
		 </form>
	</div>

</div>



</body>
</html>