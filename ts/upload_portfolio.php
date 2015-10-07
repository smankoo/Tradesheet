<?php
session_start();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <title>Upload page</title>

    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="bootstrap/css/bootstrap-theme.min.css">

</head>

<body>
    <div class="starter-template">
        <div class="container">

            <?php

            //Upload File
            if (!empty($_POST['submit'])) {

            
            include "connectdb.php"; //Connect to Database

            $deleterecords = "delete from portfolio where trading_group = '" . $_SESSION['trading_group'] . "'"; //empty the table of its current records
            mysql_query($deleterecords);

            
                if (is_uploaded_file($_FILES['filename']['tmp_name'])) {
                    echo "<h1>" . "File ". $_FILES['filename']['name'] ." uploaded successfully." . "</h1>";
                    //echo "<h2>Displaying contents:</h2>";
                    //readfile($_FILES['filename']['tmp_name']);
                }

                //Import uploaded file to Database
                $handle = fopen($_FILES['filename']['tmp_name'], "r");

                $i = 0;
                while (($data = fgetcsv($handle, 0, ",")) !== FALSE) {

                    $i = $i + 1;
                    
                    // Skip first row, because it contains headers
                    if ( $i == 1 ){
                        continue;
                    }
                    
                    // inserting escape characters (cleansing input)

                    foreach ($data as &$value) {
                        $value = addslashes ($value);
                    }
                
                    $rep_acc_no_raw = $data[0];
                    
                    if ( $rep_acc_no_raw == "" ) {
                        // If for some reason there's a blank account number, skip this row
                        continue;
                    }
                    
                    $sec_desc =  $data[5];
                    $ticker = $data[49];
                    $isin = $data[50];
                    $local_curr_code = $data[13];
                    $share_par = str_replace(",","",$data[17]);
                    $base_cost = str_replace(",","",$data[18]);
                    $local_cost = str_replace(",","",$data[19]);
                    $base_market_value = str_replace(",","",$data[24]);
                    $base_net_income_rec = str_replace(",","",$data[22]);
                    $report_run_date = $data[38];

                    // debugging hook
                    //echo "rep_acc_no_raw " . $rep_acc_no_raw . " </br>";
                    //echo "sec_desc " . $sec_desc . " </br>";
                    //echo "ticker " .  $ticker  . " </br>";
                    //echo "isin " .  $isin  . " </br>";
                    //echo "local_curr_code " .  $local_curr_code  . " </br>";
                    //echo "share_par " .  $share_par  . " </br>";
                    //echo "base_cost " .  $base_cost  . " </br>";
                    //echo "local_cost " .  $local_cost  . " </br>";
                    //echo "base_market_value " . $base_market_value . " </br>";
                    //echo "base_net_income_rec " . $base_net_income_rec . " </br>";
                    //echo "report_run_date " .  $report_run_date  . " </br>";
                    
                    // removing the extra "0"
                    $rep_acc_no = substr($rep_acc_no_raw,0,8) . substr($rep_acc_no_raw,-3) ;
                    
                    //echo "rep_acc_no " .  $rep_acc_no  . " </br>";
                    
                    $base_market_value_total = $base_market_value + $base_net_income_rec;
                    
                    $report_run_date_formatted = date_format(date_create_from_format('n/j/Y',$report_run_date ),'Y-m-d');

                    
                    //echo "base_market_value_total " .  $base_market_value_total  . " </br>";
                    
                    // Build the SQL
                    if ( $i == 2 ){
                        $remove="delete from portfolio where trading_group = '" . $rep_acc_no . "'";
                        mysql_query($remove) or die(mysql_error());
                    }
                    
                    
                    $import="INSERT into portfolio(trading_group,
                            security_description,
                            local_currency_code,
                            shares_par,
                            base_cost,
                            local_cost,
                            base_market_value,
                            report_run_date,
                            ticker,
                            isin) values('" . $rep_acc_no . "',
                                        '" . $sec_desc . "',
                                        '" . $local_curr_code . "',
                                        '" . $share_par . "',
                                        '" . $base_cost . "',
                                        '" . $local_cost . "',
                                        '" . $base_market_value_total . "',
                                        '" . $report_run_date_formatted . "',
                                        '" . $ticker . "',
                                        '" . $isin . "')";


                    //echo $i . " about to run the following query </br>";
                    //echo $import . "</br>";

                    mysql_query($import) or die(mysql_error());

                }

                fclose($handle);

                print "Import done<br/>";
                print "<a href=\"index.php#portfolio\">OK. Take me back to the Tradesheet application</a><br/>";
                

                //view upload form
            } else {
            ?>
                <div class="container col-lg-6">
                    <div class="text-left" style="padding:20px 0px 20px 0px;">
                        Upload new csv by browsing to file and clicking on Upload<br/>
                    </div>
                    
                    <form enctype='multipart/form-data' action="upload_portfolio.php" method='post'>
                        <input size='50' type='file' name='filename'>
                        <div class="text-right">
                        <input type='submit' class="btn btn-primary" name='submit' value='Upload'>
                        </div>
                    </form>
                </div>

                <?php
            }

            ?>

        </div>
    </div>
</body>

</html>