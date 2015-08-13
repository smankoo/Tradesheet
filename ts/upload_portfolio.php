<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <title>Upload page</title>
    <style type="text/css">
        body {
            background: #E3F4FC;
            font: normal 14px/30px Helvetica, Arial, sans-serif;
            color: #2b2b2b;
        }
        
        a {
            color: #898989;
            font-size: 14px;
            font-weight: bold;
            text-decoration: none;
        }
        
        a:hover {
            color: #CC0033;
        }
        
        h1 {
            font: bold 14px Helvetica, Arial, sans-serif;
            color: #CC0033;
        }
        
        h2 {
            font: bold 14px Helvetica, Arial, sans-serif;
            color: #898989;
        }
        
        #container {
            background: #CCC;
            margin: 100px auto;
            width: 945px;
        }
        
        #form {
            padding: 20px 150px;
        }
        
        #form input {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div id="container">
        <div id="form">

            <?php

            //Upload File
            if (!empty($_POST['submit'])) {

            include "connectdb.php"; //Connect to Database

            $deleterecords = "TRUNCATE TABLE stocks"; //empty the table of its current records
            mysql_query($deleterecords);

            
                if (is_uploaded_file($_FILES['filename']['tmp_name'])) {
                    echo "<h1>" . "File ". $_FILES['filename']['name'] ." uploaded successfully." . "</h1>";
                    //echo "<h2>Displaying contents:</h2>";
                    //readfile($_FILES['filename']['tmp_name']);
                }

                //Import uploaded file to Database
                $handle = fopen($_FILES['filename']['tmp_name'], "r");

                $i = 0;
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

                    // inserting escape characters (cleansing input)

                    foreach ($data as &$value) {
                        $value = addslashes ($value);
                    }

                    $import="INSERT into portfolio() values('')";

                    //$i = $i + 1;
                    //echo $i . " about to run the following query\n";
                    //echo $import . "\n";

                    mysql_query($import) or die(mysql_error());
                }

                fclose($handle);

                print "Import done";

                //view upload form
            } else {

                print "Upload new csv by browsing to file and clicking on Upload<br />\n";
                print "<form enctype='multipart/form-data' action=" . htmlspecialchars($_SERVER["PHP_SELF"]) . " method='post'>";
                print "File name to import:<br />\n";
                print "<input size='50' type='file' name='filename'><br />\n";
                print "<input type='submit' name='submit' value='Upload'></form>";

            }

            ?>

        </div>
    </div>
</body>

</html>