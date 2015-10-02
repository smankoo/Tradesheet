<?php

//require_once('class.phpmailer.php');
require_once('PHPMailerAutoload.php');


$subject = "Tradesheet Generated at " . date("Y-m-d h:i:sa");
$body_of_your_email = "<!DOCTYPE html>
<html lang=\"\" hola_ext_inject=\"disabled\">

<head>
    <link rel=\"shortcut icon\" href=\"\">
    <link rel=\"stylesheet\" href=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css\">
    <link rel=\"stylesheet\" href=\"https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css\">

</head>

<body>
    <div class=\"container col-lg-12\">
        <div class=\"starter-template\">
            <div class=\"\">

                <div style=\"clear: both;\">
                    <div id=\"preparedSheetDiv\" style=\"display: block; padding-top: 20px;\">
                        <div id=\"email_body_div\" style=\" border-style: solid; border-width: 2px;\">
" . $_POST['email_body'] . "</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>";


$mail = new PHPMailer();

$mail->IsSMTP();                       // telling the class to use SMTP

$mail->SMTPDebug = 0;                  
// 0 = no output, 1 = errors and messages, 2 = messages only.

$mail->SMTPAuth = true;                // enable SMTP authentication 
$mail->SMTPSecure = "tls";              // sets the prefix to the servier
$mail->Host = "smtp.mcgill.ca";        // sets Gmail as the SMTP server
$mail->Port = 587;                     // set the SMTP port for the GMAIL 

//$mail->Username = $_POST['user_email'];  
//$mail->Password = $_POST['user_pass'];      
$mail->Username = "sumeet.mankoo@mail.mcgill.ca";  
$mail->Password = "Sm123456";      

$mail->CharSet = 'windows-1250';
//$mail->SetFrom ($_POST['user_email'], 'Tradesheet Mailer');
$mail->SetFrom ("sumeet.mankoo@mail.mcgill.ca", 'Tradesheet Mailer');
//$mail->AddBCC ( 'sales@example.com', 'Example.com Sales Dep.'); 
$mail->Subject = $subject;
$mail->ContentType = 'text/html'; 
$mail->IsHTML(true);

$mail->Body = $body_of_your_email; 
// you may also use $mail->Body = file_get_contents('your_mail_template.html');

$mail->AddAddress ('sumeet.mankoo@mail.mcgill.ca');     
// you may also use this format $mail->AddAddress ($recipient);

if(!$mail->Send()) 
{
    $error_message = "Mailer Error: " . $mail->ErrorInfo;
    print "email body " . $_POST['user_email'] . $_POST['user_pass'];
    print "FAILED";
    print $mail->ErrorInfo;
} else {
    $error_message = "Successfully sent!";
    print "SUCCESS";
}

// You may delete or alter these last lines reporting error messages, but beware, that if you delete the $mail->Send() part, the e-mail will not be sent, because that is the part of this code, that actually sends the e-mail
?>