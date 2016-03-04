<?php
session_start();

// Check to make sure some value of the symbol was entered
if (empty($_GET['sym'])) {
    // If no value has been entered, exit without any output.
    return "";
    exit;
}
else {
    // If a value was entered, let's have fun with it
    $sym = $_GET['sym'];
}

// Connect to the database
require_once 'connectdb.php';

// Check if portfolio is fresh
$query = "select count(*) port_count from portfolio where upper(trading_group) = upper('" . $_SESSION['trading_group'] . "') and report_run_date = curdate()";

// sending query
$result = mysql_query($query);
if (!$result) {
    die("Query to show fields from table failed");
}

$row = mysql_fetch_assoc($result);

$port_count = $row["port_count"];

if ( $port_count <= 0 ) {
    // If no good record found, report portfolio as non current
    print "NOTCURRENT";
    exit;
}


// sending query
$result = mysql_query("select * from stocks where upper(bloomberg_fin_code) = upper('" . $sym . "')");
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

$service_url = 'https://query.yahooapis.com/v1/public/yql?q=select%20Name%2CLastTradePriceOnly%20from%20yahoo.finance.quotes%20where%20symbol%20in%20(%22' . $sym . '%22)&format=json&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys&callback=';
$curl = curl_init($service_url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$curl_response = curl_exec($curl);
if ($curl_response === false) {
    $info = curl_getinfo($curl);
    curl_close($curl);
    die('error occured during curl exec. Additional info: ' . var_export($info));
}
curl_close($curl);
$decoded = json_decode($curl_response);
if (isset($decoded->response->status) && $decoded->response->status == 'ERROR') {
    die('error occured: ' . $decoded->response->errormessage);
}
//echo 'response ok!';
//echo "<br>";
//var_dump($decoded);
//echo '<pre>' . var_export($decoded, true) . '</pre>';
//echo "<br>";

//echo "last trade price: ";
//echo $decoded->query->results->quote->Name;
//echo "<br>";
//echo $decoded->query->results->quote->LastTradePriceOnly;

//$securityInfo = array("SecurityName"=>$decoded->query->results->quote->Name,"SecurityPrice"=>$decoded->query->results->quote->LastTradePriceOnly);
//print json_encode($securityInfo);

//echo json_encode($securityInfo);

echo $curl_response;

?>
