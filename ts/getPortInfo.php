<?php

// Check to make sure some value of the symbol was entered
if (empty($_GET['isin'])) {
    // If no value has been entered, exit without any output.
    return "";
    exit;
}
else {
    // If a value was entered, let's have fun with it
    $isin = $_GET['isin'];
}
session_start();
// Connect to the database
require_once 'connectdb.php';

// sending query
$query = "select * from portfolio where upper(isin) = upper('" . $isin . "') and upper(trading_group) = upper('" . $_SESSION['trading_group'] . "')";
//echo $query;
$result = mysql_query($query);
if (!$result) {
    die("Query to show fields from table failed");
}


while($row = mysql_fetch_assoc($result)) {
    print json_encode($row);
}

if ( mysql_num_rows($result) == 0 ) {
    print "NOTFOUND";
}

// Free the resources associated with the result set
// This is done automatically at the end of the script
mysql_free_result($result);

?>
