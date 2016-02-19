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
                
                    $trading_group = $data[0];
                    
                    if ( $trading_group == "" ) {
                        // If for some reason there's a blank account number, skip this row
                        continue;
                    }

                    $user_email = $data[1];
                    $user_name = $data[2];
                    $user_password = $data[3];

                    $user_password_hash = password_hash($user_password, PASSWORD_DEFAULT);
                    
                    echo $user_name;
                    // check if user or email address already exists
                    $sql = "SELECT * FROM users WHERE user_name = '" . $user_name . "' OR user_email = '" . $user_email . "';";
                    $result = mysql_query($sql) or die(mysql_error());
                    $query_check_user_name = mysql_num_rows($result);

                    if ($query_check_user_name == 1) {
                        echo " : Sorry, that username / email address is already taken.";
                    } else {
                        // write new user's data into database
                        $sql = "INSERT INTO users (user_name, user_password_hash, user_email, trading_group)
                                VALUES('" . $user_name . "', '" . $user_password_hash . "', '" . $user_email . "', '" . $trading_group . "');";

                        mysql_query($sql) or die(mysql_error());

                        echo " : user created.";
                    }
                    echo "<br/>";
                }
                fclose($handle);

                print "Import done<br/>";
                print "<a href=\"index.php#portfolio\">OK. Take me back to the Tradesheet application</a><br/>";
                

                //view upload form
            } else {
            ?>
                    <div class="container col-lg-6">
                        <div class="text-left" style="padding:20px 0px 20px 0px;">
                            Upload new csv by browsing to file and clicking on Upload
                            <br/>
                        </div>

                        <form enctype='multipart/form-data' action="bulk.php" method='post'>
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