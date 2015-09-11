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

// Connect to the database
require_once 'connectdb.php';


// sending query
$result = mysql_query("select * from portfolio where upper(isin) = upper('" . $isin . "')");
if (!$result) {
    die("Query to show fields from table failed");
}


while($row = mysql_fetch_assoc($result)) {
    print json_encode($row);
}


// Free the resources associated with the result set
// This is done automatically at the end of the script
mysql_free_result($result);

?>
