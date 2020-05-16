<?php

define('DB_HOST', 'sql306.epizy.com');
define ('DB_USER','epiz_25784398');
define('DB_PASSWORD', '86bE3iVmKWyTH');
define('DB_NAME', 'epiz_25784398_CMS');

$dbc = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME)
OR die('Connection failed'.mysqli_connect_error());

?>