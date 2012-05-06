<?php
/*
$dbname = 'flackwebservice';
mysql_connect("flackwebservice.db.9314520.hostedresource.com", "flackwebservice", "Wtbpa55word!") or die(mysql_error());
mysql_select_db($dbname) or die ("no database"); */
$dbname = 'flackwebservice';
	$host = "flackwebservice.db.9314520.hostedresource.com";
	$user = "flackwebservice";
	$pass = "Wtbpa55word!";
	$mysqli = new mysqli($host, $user, $pass, $dbname);

	/* check connection */
	if (mysqli_connect_errno()) {
		printf("Connect failed: %s\n", mysqli_connect_error());
		exit();
	}              
?>