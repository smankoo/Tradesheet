<?php

session_start();

// Connect to the database
require_once 'connectdb.php';

$query = "select count(*) port_count from portfolio where upper(trading_group) = upper('" . $_SESSION['trading_group'] . "') and report_run_date = curdate()";

// sending query
$result = mysql_query($query);
if (!$result) {
    die("Query to show fields from table failed");
}

$row = mysql_fetch_assoc($result);

$port_count = $row["port_count"];

if ( $port_count > 0 ) {
    print "CURRENT";
} else {
    print "NOTCURRENT";
}

// Free the resources associated with the result set
// This is done automatically at the end of the script
mysql_free_result($result);

?>