<?php

require_once 'connectdb.php';

// sending query
$result = mysql_query("delete from portfolio where upper(trading_group) = upper('" . $_SESSION['trading_group'] . "')");
if (!$result) {
    die("Query to delete user portfolio failed");
}

?>