<?php

if(isset($_GET['error_code']))
{
	$error_code=$_GET['error_code'];
	if(!empty($_GET['error_code']))
	{

	echo '<h1>Error</h1>';

	switch ($error_code) {
		case '22':
			echo 'Login to continue.......';
			# code...
			break;

		case '600':
			echo 'Page not found.......';
			# code...
			break;

		case '610':
			echo 'Page not removed.......';
			# code...
			break;

		case '612':
			echo 'Page not added.......';
			# code...
			break;

		case '622':
			echo 'Page not updated.......';
			# code...
			break;

		case '642':
			echo 'Invalid Page.......';
			# code...
			break;

		case '700':
			echo 'Subject not found.......';
			# code...
			break;

		case '710':
			echo 'Subject not removed.......';
			# code...
			break;

		case '712':
			echo 'Subject not added.......';
			# code...
			break;

		case '722':
			echo 'Subject not updated.......';
			# code...
			break;

		case '792':
			echo 'Database connection failed.......';
			# code...
			break;
		case '555':
			echo "User with given username already exists";
			break;

		case '554':
			echo "Password and confirm password do not match";
			break;

		case '1000':
			echo "Root user cannot be removed unless root user is logged in";
			break;

        case '222':
            echo "Session expired<br/>".'Login again to continue';
            break;
		
		default:
			echo 'Some error occurred.....';
			break;
	}

	}


}

?>