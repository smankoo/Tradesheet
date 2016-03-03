<?php


function getCashOnHand($curr) {
    // Check to make sure some value of the symbol was entered
    if (empty($curr)) {
        // If no value has been entered, exit without any output.
        return "";
        exit;
    }

    // Connect to the database
    require_once 'connectdb.php';


    if ( strtoupper($curr) == "CAD" ){
        // sending query
        $result = mysql_query("select base_cost from portfolio where upper(local_currency_code) = upper('" . $curr . "') and security_description = 'CASH' and upper(trading_group) = upper('" . $_SESSION['trading_group'] . "')");
        if (!$result) {
            die("Query to show fields from table failed");
        }
    } elseif ( strtoupper($curr) == "USD" ) {
        $result = mysql_query("select base_cost from portfolio where upper(local_currency_code)  = upper('" . $curr . "') and security_description = 'NON-BASE CURRENCY' and upper(trading_group) = upper('" . $_SESSION['trading_group'] . "')");
        if (!$result) {
            die("Query to show fields from table failed");
        }
    }

    $row = mysql_fetch_row($result);

    // Free the resources associated with the result set
    // This is done automatically at the end of the script
    mysql_free_result($result);

    echo $row[0];
}
?>