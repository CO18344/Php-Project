<?php

session_start();
require_once('../mysqli_connect.php');
//Database status variables
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

//Static Text
$selectText="";
$headingTextPageOnSubject_View="Pages in this subject:";
$headingTextPage_Add="Add Page to Subject: ";

$headingTextSubject_View="Manage Subject";
$menuTextSubject_View="Menu Name: ";
$posTextSubject_View="Position: ";
$visTextSubject_View="Visibility: ";

$headingTextPage_View="Manage Page";
$menuTextPage_View="Menu Name: ";
$posTextPage_View="Position: ";
$visTextPage_View="Visibility: ";
$contentTextPage_View="Content: ";

//Edit Text
$headingTextSubject_Edit="Edit Subject: ";
$menuTextSubject_Edit="Menu Name";
$posTextSubject_Edit="Position";
$visTextSubject_Edit="Visibility";

$headingTextPage_Edit="Edit Page: ";
$menuTextPage_Edit="Menu Name";
$posTextPage_Edit="Position";
$visTextPage_Edit="Visibility";
$contentTextPage_Edit="Content: ";

//Links
$link_editSubject="Edit Subject";
$link_editPage="Edit Page";
$link_addNewPage="Add a new page to this subject";
$link_cancelDeletingPage="Cancel";
$link_deletePage="Delete page";
$link_cancelDeletingSubject="Cancel";
$link_deleteSubject="Delete Subject";
$link_pageSelect="";
$link_mainMenu="Main Menu";

//Form Variable
$formForUpdatingSubjects="";
$formForUpdatingPages="";

//Booleans
$isGETedit_subject=false;
$isGETview_subject=false;
$isGETview_page=false;
$isGETedit_page=false;
$isGETadd_page=false;

//Database main variables
$menuNameSubject="";
$posSubject="";
$visibleSubject="";
$idSubject="";

$menuNamePage="";
$posPage="";
$visiblePage="";
$contentPage="";
$idPage="";



//Map type varibles
$map_subjectMenuNames_To_pageMenuNames=array();
$map_subjectMenuNames_To_subjectIds=array();
$map_pageMenuNames_To_pageIds=array();

$local_idSubject="";
$local_idPage="";
$local_menuName="";
$local_query="";
$local_response="";

$min="1";
$max="";

//Basic code to update subjects and pages.
if(isset($_SESSION['loggedin']) && $_SESSION['loggedin']===true )
{
	$selectText = "Please select a subject or a page";
	
	$query ='select * from subjects order by position';
	$response = mysqli_query($dbc,$query);
	if($response)
	{
		//Reading subjects................
		while($row = mysqli_fetch_array($response))
		{
		$local_menuName = $row['menu_name'];
		$local_idSubject= $row['id'];
		$local_query = "select * from pages where subject_id= '$local_idSubject' order by position;";
		$local_response = mysqli_query($dbc,$local_query);
		if($local_response)
		{
			//Reading pages........
			$local_pageArray = array();
			while($row2 =  mysqli_fetch_array($local_response))
			{		
				$local_idPage = $row2['id'];
				$local_menuNamePage = $row2['menu_name'];
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
		$selectText = "";

		$idSubject=$_GET['subject'];
		$query="select * from subjects where id='$idSubject';";
		$response = mysqli_query($dbc,$query);
		if($response)
		{
			$row = mysqli_fetch_array($response);
			if(!empty($row['id']))
			{
				$isGETview_subject=true;
				$menuNameSubject=$row['menu_name'];
				$posSubject=$row['position'];
				$visibleSubject=$row['visible'];		
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
		$selectText="";

		$idPage=$_GET['page'];
		$query="select * from pages where id = '$idPage';";
		$response=mysqli_query($dbc,$query);

		if($response)
		{
			//$statusPageView = "Executed page view query<br/>";
			$row = mysqli_fetch_array($response);
			if(empty($row['id']))	
				$error_info = "600";
			else
			{	
				$isGETview_page=true;
				$menuNamePage = $row['menu_name'];
				$posPage = $row['position'];
				$visiblePage = $row['visible'];
				$contentPage = $row['content'];
			}
		}
		else
			$error_info = "792";
	}

//If subject is selected for editing................
	else if(isset($_GET['edit_subject']))
	{
		$selectText = "";

		$idSubject= $_GET['edit_subject'];
		$local_query = "select count(id) as count from subjects;";
		$local_response = mysqli_query($dbc,$local_query);
		if($local_response)
		{
			$local_row = mysqli_fetch_array($local_response);
			if(isset($local_row['count']));
			{
				$max = $local_row['count'];
			}
		}

		$query="select * from subjects where id = '$idSubject';";
		$response = mysqli_query($dbc,$query);
		if($response)
		{
			//$statusSubjectEdit="Executed Subject edit query<br/>";
			$row=mysqli_fetch_array($response);
			if(empty($row['id']))
			{
				$error_info = "700";		
			}
			else
			{
				//$statusSubjectEdit=$statusSubjectEdit."Subject found <br/>";
				$isGETedit_subject=true;
				$headingTextSubject_Edit=$headingTextSubject_Edit.$row['menu_name'];
				$menuNameSubject=$row['menu_name'];
				$posSubject=$row['position'];
				$visibleSubject=$row['visible'];

				$yesChecked="";
				$noChecked="";
				if($visibleSubject === "yes")
					$yesChecked="checked";
				else
					$noChecked="checked";

				$formForUpdatingSubjects='<form action="'.$_SERVER["PHP_SELF"].'"'.' method=post>'.
				'<p>Id'.'<input type="text" name="id" value='.$idSubject.' readonly required /></p>'.
				'<p>'.$menuTextSubject_Edit.'<input type="text" name="menu_name" value="'.$menuNameSubject.'" required /></p>'.
				'<p>'.$posTextSubject_Edit.'<input type="number" name="position" value="'.$posSubject.'" min="1" max='.$max.' required /></p>'.
				'<p id="radio">'.$visTextSubject_Edit.
				'<br/>'.
				'<input type="radio" name="visibility" value="yes" id="yes" '.$yesChecked.' required />'.
				'<label for="yes">Yes</label>'.
				'<br/>'.
				'<input type="radio" name="visibility" value="no" id="no" '.$noChecked.' required />'.
				'<label for="no">No</label>'.
				'</p>'.
				'<input type="submit" name="submit_EditSubject" value="Edit Subject" id="submit" required />'.
				'</form>';
			}
		}
		else
			$error_info = "792";
	}

//If page is chosen for editing
	else if(isset($_GET['edit_page']))
	{
		$selectText = "";

		$idPage= $_GET['edit_page'];
	$local_query = "select subject_id from subjects A,pages B where A.id=B.subject_id and B.id='$idPage';";
	$local_response = mysqli_query($dbc,$local_query);
		
		if($local_response)
		{
			$local_row = mysqli_fetch_array($local_response);
			if(!empty($local_row['subject_id']))
			{
				$local_idSubject = $local_row['subject_id'];
			}
		}

		$query="select * from pages where id = '$idPage';";
		$response = mysqli_query($dbc,$query);
		if($response)
		{
			//$statusPageEdit="Executed Page edit query<br/>";
			$row=mysqli_fetch_array($response);
			if(empty($row['id']))
			{
				$error_info = "600";		
			}
			else
			{
				//$statusPageEdit=$statusPageEdit."Page found <br/>";
				$isGETedit_page=true;
				$headingTextPage_Edit=$headingTextPage_Edit.$row['menu_name'];
				$menuNamePage=$row['menu_name'];
				$posPage=$row['position'];
				$visiblePage=$row['visible'];
				$contentPage=$row['content'];

				$query_getmax = "select count(*) as count from pages where subject_id='$local_idSubject';";
				$response_getmax = mysqli_query($dbc,$query_getmax);
				if($response_getmax )
				{
					$row=mysqli_fetch_array($response_getmax);
					if(isset($row['count']))
					{
						$max=$row['count'];

						$yesChecked="";
						$noChecked="";

					if($visiblePage === "yes")
						$yesChecked="checked";
					else
						$noChecked="checked";

				$formForUpdatingPages='<form action="'.$_SERVER["PHP_SELF"].'"'.' id=editform method=post>'.
				'<p>Id'.'<input type="text" readonly name="id" value="'.$idPage.'" required /></p>'.
				'<p>'.$menuTextPage_Edit.'<input type="text" name="menu_name" value="'.$menuNamePage.'" required /></p>'.
				'<p>'.$posTextPage_Edit.'<input type="number" min='.$min.' max='.$max.' name="position" value="'.$posPage.'" required /></p>'.
				'<p id="radio">'.$visTextPage_Edit.
				'<br/>'.
				'<input type="radio" name="visibility" value="yes" id="yes" '.$yesChecked.' required />'.
				'<label for="yes">Yes</label>'.
				'<br/>'.
				'<input type="radio" name="visibility" value="no" id="no" '.$noChecked.' required />'.
				'<label for="no">No</label>'.
				'</p>'.
				'<p>'.$contentTextPage_Edit.'</p>'.
				 '<textarea name="content" rows="15" cols="100">'
				.$contentPage.	
				'</textarea>'.
				'<p><input type="submit" name="submit_EditPage" value="Edit Page" id="submit" required /></p>'.
				'</form>';
					}
				}
			}
		}
		else
			$error_info = "792";
	}

//If page is being added
	else if(isset($_GET['addPage']))
	{
		$selectText = "";

		$idSubject=$_GET['addPage'];
		$isGETadd_page=true;
		$query = "select menu_name from subjects where id = '$idSubject';";
		$response = mysqli_query($dbc,$query);

		if($response)
		{
			//$statusPageAdd="Executed page add query<br/>";
			$row = mysqli_fetch_array($response);
			if(empty($row['menu_name']))
			{
				$error_info = "700";
			}
			else
			{
				//$statusPageAdd = $statusPageAdd.'Subject found<br/>';
				$menuNameSubject = $row['menu_name'];
				$headingTextPage_Add = $headingTextPage_Add.$menuNameSubject;
			}

		$query_getmax = "select count(*) as count from pages where subject_id='$idSubject';";
		$response_getmax = mysqli_query($dbc,$query_getmax);

		if($response_getmax)
		{
			$row_getmax=mysqli_fetch_array($response_getmax);
			if(array_key_exists('count', $row_getmax) && is_null($row_getmax['count'])===false) 
			{

			$max=$row_getmax['count'] + 1;
			$posPage=$max;

			$formForAddingPages='<form action="'.$_SERVER["PHP_SELF"].'"'.' id=editform method=post>'.
			'<p>'.$menuTextPage_Edit.'<input type="text" name="menu_name" value="'.$menuNamePage.'" required /></p>'.
			'<p>Subject Id'.'<input type="text" readonly name="subject_id" value="'.$idSubject.'" required /></p>'.
			'<p>'.$posTextPage_Edit.'<input type="number" name="position" value='.$posPage.' required   min='.$min.' max='.$max.' /></p>'.
			'<p id="radio">'.$visTextPage_Edit.
			'<br/>'.
			'<input type="radio" name="visibility" value="yes" id="yes" checked required />'.
			'<label for="yes">Yes</label>'.
			'<br/>'.
			'<input type="radio" name="visibility" value="no" id="no" required />'.
			'<label for="no">No</label>'.
			'</p>'.
			'<p>'.$contentTextPage_Edit.'</p>'.
			 '<textarea name="content" rows="15" cols="100">'
			.$contentPage.	
			'</textarea>'.
			'<p><input type="submit" name="submit_AddPage" value="Add Page" id="submit" required /></p>'.
			'</form>';
			}
		}

		else
		{
			$error_info = "792";
		}	

		}


		else
		{
			$error_info = "792";
		}			
	}

//If subject is to be deleted
	else if(isset($_GET['delete_subject']))
	{
	$idSubject = $_GET['delete_subject'];

		$local_query = "select id from pages where subject_id='$idSubject';";
		$local_response = mysqli_query($dbc,$local_query);

			if($local_response)
			{
				while($local_row = mysqli_fetch_array($local_response))
				{

				$local_idPage = $local_row['id'];
				$query2 = "delete from pages where id='$local_idPage';";
				$response2 = mysqli_query($dbc,$query2);

				if($response2===false)
				{
					$error_info="610";
				}

				}
			}

		$local_query = "select position from subjects where id='$idSubject';";
		$local_response = mysqli_query($dbc,$local_query);

		$local_position = "";
		if($local_response)
		{
			$local_row = mysqli_fetch_array($local_response);
			if(!empty($local_row['position']))
			{
				$local_position = $local_row['position'];
			}
		}

		//$statusSubjectDelete= "Executed subject delete query<br/>";
		$query = "delete from subjects where id='$idSubject';";
		$response = mysqli_query($dbc,$query);
		if($response)
		{
			if(mysqli_affected_rows($dbc)>0)
			{
			$local_query = "update subjects set position = position - 1 where position > '$local_position';";
			$local_response = mysqli_query($dbc,$local_query);
			
			$_SESSION['delete_subject']="Subject deleted successfully";

			if($local_response)
			{
			//$statusSubjectDelete = $statusSubjectDelete.'Subject removed Successfully<br/>';

			

			header('location: manageContent.php');
			exit;

			}

			}
			else
				$error_info = "710";
		}
		else
			$error_info = "792";
		header('location: manageContent.php');
		exit;
	}

//if page is to be deleted
	else if(isset($_GET['delete_page']))
	{
		$idPage = $_GET['delete_page'];

		$query = "select subject_id,position from pages where id='$idPage';";
		$response = mysqli_query($dbc,$query);
		if($response)
		{
			$row = mysqli_fetch_array($response);
			//$statusPageDelete = "Executed page delete query<br/>";
			if(empty($row['subject_id']))
				$error_info = "642";
			else
			{
				$idSubject = $row['subject_id'];
				$local_position = $row['position'];
				$query = "delete from pages where id='$idPage';";
				$response = mysqli_query($dbc,$query);
				if($response)
				{
					if(mysqli_affected_rows($dbc)>0)
					{

						$local_query = "update pages set position = position - 1 where position > '$local_position' and subject_id='$idSubject';";
						$local_response = mysqli_query($dbc,$local_query);

						$_SESSION['delete_page']="page deleted successfully";
						if($local_response)
						{
						
						header('location: manageContent.php?subject='.$idSubject);
						exit;
						}

					}
					else
						$error_info = "610";
				}
				else
					$error_info = "792";
			}
		}
		else
			$error_info = "792";
		header('location: manageContent.php');
		exit;
	}

//form has been submitted for adding new page
	else if(isset($_POST['submit_AddPage']))
	{
		$menuNamePage = $dbc->real_escape_string($_POST['menu_name']);
		$posPage = $_POST['position'];
		$visiblePage = $_POST['visibility'];
		$contentPage = $dbc->real_escape_string($_POST['content']);
		$idSubject = $_POST['subject_id'];

		$local_query = "select count(id) as count from pages where subject_id='$idSubject';";
		$local_response = mysqli_query($dbc,$local_query);
		if($local_response)
		{
			$local_row = mysqli_fetch_array($local_response);
			if(isset($local_row['count']))
			{
				//$local_idSubject = $local_row['subject_id'];
			if(array_key_exists('count', $local_row) && is_null($local_row['count'])===false)
			{	
				$local_count=$local_row['count'] + 1;
				if($posPage > $local_count)
					$posPage=$local_count;
				else if($posPage <= $local_count - 1)
				{
				
				$local_query = "update pages set position='$local_count' where position='$posPage' and subject_id='$idSubject';";
				$local_response = mysqli_query($dbc,$local_query);
				
				if(mysqli_affected_rows($dbc)>0)
				{
					
					echo "Rows affected";
				}

				}
			}

			}
		}
		else
		{
			$error_info = "792";
		}

		$query = "insert into pages (menu_name,subject_id,position,content,visible) values ('$menuNamePage','$idSubject','$posPage','$contentPage','$visiblePage');";
		$response = mysqli_query($dbc,$query);

		if($response)
		{
			//$statusPageAdd = 'Executed page add query<br/>';
			if(mysqli_affected_rows($dbc)>0)
				$_SESSION['page_added']="Page added successfully";
			else
				$error_info = "612";

		}
		else
			$error_info = "792";
		header('location: manageContent.php?subject='.$idSubject);
		exit;

	}

//If form has been submitted for editing page
	else if(isset($_POST['submit_EditPage']))
	{
		$menuNamePage = $dbc->real_escape_string($_POST['menu_name']);
		$posPage = $_POST['position'];
		$visiblePage = $_POST['visibility'];
		$contentPage = $dbc->real_escape_string($_POST['content']);
		$idPage = $_POST['id'];

		$local_query = "select subject_id from subjects A,pages B where A.id=B.subject_id and B.id='$idPage';";
		$local_response = mysqli_query($dbc,$local_query);
		if($local_response)
		{
			$local_row = mysqli_fetch_array($local_response);
			if(!empty($local_row['subject_id']))
			{
				$local_idSubject = $local_row['subject_id'];
			}
		}

		$local_query = "select count(id) as count from pages where subject_id='$local_idSubject';";
		$local_response = mysqli_query($dbc,$local_query);
		if($local_response)
		{
			$local_row = mysqli_fetch_array($local_response);
			if(isset($local_row['count']))
			{
				//$local_idSubject = $local_row['subject_id'];
			if(array_key_exists('count', $local_row) && is_null($local_row['count'])===false)
			{	
				$local_count=$local_row['count'];
				if($posPage > $local_count)
				{
				header('location: manageContent.php?page='.$idPage);
				exit;
					
				}
				else
				{
					$local_query = "select position from pages where id = '$idPage';";
					$local_response = mysqli_query($dbc,$local_query);
					if($local_response)
					{
						$local_row = mysqli_fetch_array($local_response);
						if(!empty($local_row['position']))
						{
							$local_position = $local_row['position'];
						}
					}


	
				$local_query = "select id from pages where position = '$posPage' and subject_id='$local_idSubject';";
				$local_response = mysqli_query($dbc,$local_query);

				if($local_response)
				{
					$local_row = mysqli_fetch_array($local_response);
					if(!empty($local_row['id']))
					{
						$local_idPage = $local_row['id'];
						$local_query = "update pages set position = '$local_position' where id='$local_idPage';";
						$local_response = mysqli_query($dbc,$local_query);
						if($local_response)
						{
							echo 'Cool';
						}
					}
				}

				}
			}
			}
		}
		else
		{
			$error_info = "792";
		}

		$query = "update pages set menu_name = '$menuNamePage',position ='$posPage' ,content='$contentPage',visible='$visiblePage' where id='$idPage';";
		$response = mysqli_query($dbc,$query);

		if($response)
		{
			//$statusPageEdit = 'Executed page update query<br/>';
			if(mysqli_affected_rows($dbc)>0)
				$_SESSION['page_update'] = 'Page updated';
			else
				$error_info = '622';
		}
		else
			$error_info = "792";

		header('location: manageContent.php?page='.$idPage);
		exit;
	}

//If form is subitted for editing subject
	else if(isset($_POST['submit_EditSubject']))
	{
		//echo "I get that one also also";
		$menuNameSubject = $dbc->real_escape_string($_POST['menu_name']);
		$posSubject = $_POST['position'];
		$visibleSubject = $_POST['visibility'];
		$idSubject = $_POST['id'];

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

				$local_count=$local_row['count'];
				if($posSubject > $local_count)
				{
					header('location: manageContent.php?subject='.$idSubject);
					exit;
				}
				else
				{
					$local_query = "select position from subjects where id = '$idSubject';";
					$local_response = mysqli_query($dbc,$local_query);
					if($local_response)
					{
						$local_row = mysqli_fetch_array($local_response);
						if(!empty($local_row['position']))
						{
							$local_position = $local_row['position'];
						}
					}

					$local_query = "select id from subjects where position = '$posSubject';";

					$local_response = mysqli_query($dbc,$local_query);

					if($local_response)
					{
						$local_row = mysqli_fetch_array($local_response);
						if(!empty($local_row['id']))
						{
							$local_idSubject = $local_row['id'];
			

							$local_query = "update subjects set position = '$local_position' where id='$local_idSubject';";

							$local_response = mysqli_query($dbc,$local_query);

							if($local_response)
							{
								echo 'Cool';
							}
						}
					}	
				}
				}
			}
			}
			else
				$error_info = "792";
		
		$query = "update subjects set menu_name = '$menuNameSubject',position ='$posSubject' ,visible='$visibleSubject' where id='$idSubject';";
		$response = mysqli_query($dbc,$query);

		if($response)
		{
			//$statusSubjectEdit= 'Executed Subject update query<br/>';
			if(mysqli_affected_rows($dbc)>0)
				$_SESSION['subject_update'] = 'Subject updated';
			else
				$error_info = '722';
		}
		else
			$error_info = "792";
		header('location: manageContent.php?subject='.$idSubject);
		exit;
	}
	
	
}
else{
	$error_info = '22';
}
$dbc->close();

if(!empty($error_info))
{
	header('location: error.php?error_code='.$error_info);
	exit;
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Manage Content</title>

	<link rel="stylesheet" href="manageContent.css">	
</head>
<body>
	
<header>
	Widget Corp Admin
</header>

<div class="container">

<div class="sidenav">

	<p><a href="admin.php" id="mainMenu"> &larr; <?php echo $link_mainMenu ?> </a></p>
	<?php
	foreach ($map_subjectMenuNames_To_pageMenuNames as $_menu => $_pages)
	{
		if($idSubject===$map_subjectMenuNames_To_subjectIds[$_menu])
			echo '<p><a href=manageContent.php?subject='.$map_subjectMenuNames_To_subjectIds[$_menu].'><b>'.$_menu.'</b></a></p>';
		else echo '<p><a href=manageContent.php?subject='.$map_subjectMenuNames_To_subjectIds[$_menu].'>'.$_menu.'</a></p>';
		
		if(count($_pages)>0)
		{	
			echo '<ul>';
			foreach($_pages as $page)
			{
				if($idPage===$map_pageMenuNames_To_pageIds[ $page ])
					echo '<li><a href=manageContent.php?page='.$map_pageMenuNames_To_pageIds[$page].'><b>'.$page.'</b></a></li>';
				else
					echo '<li><a href=manageContent.php?page='.$map_pageMenuNames_To_pageIds[$page].'>'.$page.'</a></li>';
			}
			echo "</ul>";
		}
	}
	?>
	<p><a href="addSubject.php">+ Add a Subject</a></p>
</div>

<div class="manageScreen">
	<p>
		<?php
		echo $selectText;
		?>
	</p>

	<div class="heading">
		<?php
		if($isGETedit_subject===true)
			echo '<b><h1>'.$headingTextSubject_Edit.'</h1></b>';
		else if($isGETedit_page===true)
			echo '<b><h1>'.$headingTextPage_Edit.'</h1></b>';
		else if($isGETview_subject===true)
			echo '<b><h1>'.$headingTextSubject_View.'</h1></b>';
		else if($isGETview_page===true)
			echo '<b><h1>'.$headingTextPage_View.'</h1></b>';
		else if($isGETadd_page===true)
			echo '<b><h1>'.$headingTextPage_Add.'</h1></b>';
		?>
	</div>

	<div class="manageform">
	<?php

	if($isGETedit_subject === true)
	{
		echo $formForUpdatingSubjects;
	}
	else if($isGETedit_page===true)
	{
		echo $formForUpdatingPages;
	}
	else if($isGETadd_page===true)
	{
		echo $formForAddingPages;
	}

	?>
	</div>

	<div class="properties">
		<?php
		if($isGETview_subject === true)
		{
		echo '<div style="margin-bottom: 8px;">'.$menuTextSubject_View.$menuNameSubject.'</div>';
		echo '<div style="margin-bottom: 8px;">'.$posTextSubject_View.$posSubject.'</div>';
		echo '<div style="margin-bottom: 8px;">'.$visTextSubject_View.$visibleSubject.'</div>';
		}
		else if($isGETview_page === true)
		{
		echo '<div style="margin-bottom: 8px;">'.$menuTextPage_View.$menuNamePage.'</div>';
		echo '<div style="margin-bottom: 8px;">'.$posTextPage_View.$posPage.'</div>';
		echo '<div style="margin-bottom: 8px;">'.$visTextPage_View.$visiblePage.'</div>';
		echo '<div style="margin-bottom: 8px; ">'.$contentTextPage_View;
			echo '<div style="border: 2px solid black; margin-top: 8px; margin-left:30px; padding:8px; overflow:scroll; width: 80% ; height: 300px; ">'.$contentPage.'</div></div>';
		}
		?>
	</div>
	
	<div class="links_on_page">
		<?php
		if($isGETview_subject===true)
		{
			echo '<a href=manageContent.php?edit_subject='.$idSubject.'>'.$link_editSubject.'</a><hr/>';
			echo '<div class="heading"><h2>'.$headingTextPageOnSubject_View.'</h2></div>';

			if(count($map_subjectMenuNames_To_pageMenuNames[ $menuNameSubject ])>0)
			{
				echo '<ul style="color:black";>';
				foreach($map_subjectMenuNames_To_pageMenuNames[ $menuNameSubject ] as $page)
				{
					echo '<li><a href=manageContent.php?page='.$map_pageMenuNames_To_pageIds[$page].'>'.$page.'</a></li>';
				}
				echo '</ul>';
			}
			else
			{
				echo '<div style="margin-bottom: 16px;"><i>There are no pages in this subject...</i></div>';
			}

			echo '<div>+<a href=manageContent.php?addPage='.$idSubject.'>'.$link_addNewPage.'</a></div>';
		}
		else if($isGETview_page===true)
			echo '<a href=manageContent.php?edit_page='.$idPage.'>'.$link_editPage.'</a>';
		else if($isGETedit_page===true)
		{	
			echo '<div style="margin-top:40px;">';
			echo '<a style="margin-left:20px;" href=manageContent.php?page='.$idPage.'>'.$link_cancelDeletingPage.'</a>';
			echo '<a style="margin-left:20px;" href=manageContent.php?delete_page='.$idPage.'>'.$link_deletePage.'</a>';
			echo '</div>';
		}
		else if($isGETedit_subject===true)
		{	
			echo '<a style="margin-left:20px;" href=manageContent.php?subject='.$idSubject.'>'.$link_cancelDeletingSubject.'</a>';
			echo '<a style="margin-left:20px;" href=manageContent.php?delete_subject='.$idSubject.'>'.$link_deleteSubject.'</a>';
		}
		?>
	</div>
	<div class="success">
		<?php
			if(!empty($_SESSION['delete_subject']))
			{
				echo $_SESSION['delete_subject'].'..........';;
				$_SESSION['delete_subject']="";
			}

			else if(!empty($_SESSION['delete_page']))
			{
				echo $_SESSION['delete_page'].'..........';;
				$_SESSION['delete_page']="";
			}
			
			else if(!empty($_SESSION['page_added']))
			{
				echo $_SESSION['page_added'].'..........';;
				$_SESSION['page_added']="";
			}

			else if(!empty($_SESSION['page_update']))
			{
				echo $_SESSION['page_update'].'..........';
				$_SESSION['page_update']="";
			}

			else if(!empty($_SESSION['subject_update']))
			{
				echo $_SESSION['subject_update'].'..........';
				$_SESSION['subject_update']="";
			}

			else if(!empty($_SESSION['subject_added']))
			{
				echo $_SESSION['subject_added'].'..........';
				$_SESSION['subject_added']="";
			}
		?>
	</div>
</div>
</div>

</body>
</html>
