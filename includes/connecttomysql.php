<?php
$dbname = 'mydb';
mysql_connect("localhost", "root", "") or die(mysql_error());
mysql_select_db($dbname) or die ("no database");             
?>