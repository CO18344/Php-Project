<?php

$menuNameSubject="";
$idSubject="";

$menuNamePage="";
$contentPage="";
$idPage="";
$display = 'none';

$isReadFirstSubject=false;
$isReadFirstPage=false;

//Map type varibles
$map_subjectMenuNames_To_pageMenuNames=array();
$map_subjectMenuNames_To_subjectIds=array();
$map_pageMenuNames_To_pageIds=array();

require_once('project/mysqli_connect.php');

	$query ="select * from subjects where visible='yes' order by position;";
	$response = mysqli_query($dbc,$query);
	if($response)
	{
		//Reading subjects................
		while($row = mysqli_fetch_array($response))
		{
		$local_menuName = $row['menu_name'];
		$local_idSubject= $row['id'];
		$local_posSubject = $row['position'];

		if($isReadFirstSubject === false)
		{
			$idSubject = $local_idSubject;
			$isReadFirstSubject = true;
		}
		$local_query = "select * from pages where subject_id= '$local_idSubject' and visible='yes' order by position;";
		$local_response = mysqli_query($dbc,$local_query);
		if($local_response)
		{
			//Reading pages........
			$local_pageArray = array();
			while($row2 =  mysqli_fetch_array($local_response))
			{		
				$local_idPage = $row2['id'];
				$local_menuNamePage = $row2['menu_name'];
				$local_posPage = $row2['position'];

				if($isReadFirstPage === false && $local_idSubject === $idSubject)
				{
					$idPage = $local_idPage;
					$menuNamePage = $local_menuNamePage;
					$contentPage = $row2['content'];
					$isReadFirstPage = true;
				}

				array_push( $local_pageArray , $local_menuNamePage );
				$map_pageMenuNames_To_pageIds[ $local_menuNamePage ] = $local_idPage;
			}
			$map_subjectMenuNames_To_pageMenuNames[ $local_menuName ]= $local_pageArray;
			$map_subjectMenuNames_To_subjectIds[ $local_menuName ]= $local_idSubject;	
		}
		else
			$error_info = "700";


		}	
	}
	else
		$error_info = "792";

//If some subject is chosen...................
	if(isset($_GET['subject']))
	{	
	
		$idSubject=$_GET['subject'];
		$query="select * from subjects where id='$idSubject' and visible='yes';";
		$response = mysqli_query($dbc,$query);
		if($response)
		{
			$row = mysqli_fetch_array($response);
			if(!empty($row['id']))
			{
				$menuNameSubject=$row['menu_name'];

				$local_query = "select id,content,menu_name from pages where subject_id = '$idSubject' and visible = 'yes' order by position;";
				$local_response = mysqli_query($dbc,$local_query);
				if($local_response)
				{
					$local_row = mysqli_fetch_array($local_response);
					if(!empty($local_row['id']))
					{
						$idPage=$local_row['id'];
						$contentPage=$local_row['content'];
						$menuNamePage=$local_row['menu_name'];
					}
					else
					{
						$idPage="";
						$contentPage="";
						$menuNamePage="";

					}

				}
			}
			else
				$error_info = "700";
		}
		else
			$error_info = "792";
	}

//If some page is chosen.............
	else if(isset($_GET['page']))
	{
		$idPage=$_GET['page'];
		$query="select * from pages where id = '$idPage' and visible='yes';";
		$response=mysqli_query($dbc,$query);
		
		if($response)
		{
			//$statusPageView = "Executed page view query<br/>";
			$row = mysqli_fetch_array($response);
			if(empty($row['id']))	
				$error_info = "600";
			else
			{	
				$idSubject = $row['subject_id'];
				$menuNamePage = $row['menu_name'];
				$contentPage = $row['content'];
			}
		}
		else
			$error_info = "792";
	}

	mysqli_close($dbc);

?>

<!DOCTYPE html>
<html>
<head>
	<title>Widget Corp</title>
	<link rel="stylesheet" href="manageContent.css">		
</head>
<body>
	
<header>
	Widget Corp
</header>
<div class="container">
<div class="sidenav">

	
	<?php
	foreach ($map_subjectMenuNames_To_pageMenuNames as $_menu => $_pages)
	{
		if($idSubject===$map_subjectMenuNames_To_subjectIds[$_menu])
			{echo '<p class=collapse ><a href=index.php?subject='.$map_subjectMenuNames_To_subjectIds[$_menu].'><b>'.$_menu.'</b></a></p>';
			$display='block';
			}
		else 
		{echo '<p class=collapse ><a href=index.php?subject='.$map_subjectMenuNames_To_subjectIds[$_menu].'>'.$_menu.'</a></p>';
		$display='none';
		}
		if(count($_pages)>0)
		{	
			echo '<div style=display:'.$display.'; ><ul>';
			foreach($_pages as $page)
			{
				if($idPage===$map_pageMenuNames_To_pageIds[ $page ])
					echo '<li ><a href=index.php?page='.$map_pageMenuNames_To_pageIds[$page].'><b>'.$page.'</b></a></li>';
				else
					echo '<li><a href=index.php?page='.$map_pageMenuNames_To_pageIds[$page].'>'.$page.'</a></li>';
			}
			echo "</ul></div>";
		}
	}
	?>
	

</div>

<div class="manageScreen">

	<div class="heading">
		<?php
			echo '<b><h1>'.$menuNamePage.'</h1></b>';		
		?>
	</div>

	<div class="properties">
		<?php
		if(empty($map_subjectMenuNames_To_pageMenuNames))
		{
			$contentPage="Currently there is no subject to view............";
		}
		else if(empty($map_pageMenuNames_To_pageIds))
		{
			$contentPage="Currently there is no page to view..............";
		}
		else if(empty($menuNamePage))
		{
			echo 'There are no pages in this subject';
		}
		echo '<div style="border: 2px solid black; margin-top: 8px; margin-left:30px; padding:8px; overflow:scroll; width: 80% ; height: 150px; ">'.$contentPage.'</div></div>';
		?>
		
	</div>


</div>

</div>


</body>
</html>
