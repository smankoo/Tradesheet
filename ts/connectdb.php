<?php
$db = mysql_connect("localhost", "root", "root") or die("Could not connect.");

if(!$db) 

	die("no db");

if(!mysql_select_db("trdsheet",$db))

 	die("No database selected.");
?>
