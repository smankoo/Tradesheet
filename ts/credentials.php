<?php

//* Modify XXXX to an access code and include in URL, phpmysqlezedit.php?access=XXXX or you will "bounce" to www.google.com!
$access='XXXX';
$preferences_loaded='1';
// Database Access Credentials
$hostname = "localhost";
$dbuser = "root";
$dbpassword = "root";
$dbname = "trdsheet";
$table = "users";
$limit = "50";
$title = "User Edit";
$greeting = "Tradesheet User Edit System";
$can_add = '1';
$can_mod = '1';
$can_del = '1';
// This can be set to your company home page or a security page.
$url = 'http://www.google.com';
////Options: Uncomment to activate:
////lock to a specific table:
//$tablelock = true;
////run a script after specific activities:
//$addscript='/usr/share/poundteam/goodguysstartup.php';
//$editscript='/usr/share/poundteam/goodguysstartup.php';
//$deletescript='/usr/share/poundteam/goodguysstartup.php';
