<?php

session_start();

//require_once('class.phpmailer.php');
require_once('PHPMailerAutoload.php');

date_default_timezone_set('America/Montreal');

$subject = "Applied Investments: " . $_SESSION['trading_group'] . " Trades " . date("Y-m-d h:i:sa");
$body_of_your_email = "<!DOCTYPE html>
<head>
<style>
* {
  box-sizing: border-box;
}

body {
  background: none;
  font-family: \"Open Sans\", arial;
}

table {
  width: 100%;
  max-width: 600px;
  border-collapse: collapse;
  border: 1px solid #38678f;
  background: white;
}

th {
  background: steelblue;
  width: 25%;
  font-weight: lighter;
  text-shadow: 0 1px 0 #38678f;
  color: white;
  border: 1px solid #38678f;
  box-shadow: inset 0px 1px 2px #568ebd;
  transition: all 0.2s;
  padding: 10px;
}

tr {
  border-bottom: 1px solid #cccccc;
}

tr:last-child {
  border-bottom: 0px;
}

td {
  border-right: 1px solid #cccccc;
  padding: 10px;
  transition: all 0.2s;
}

</style>
</head>
<html>
<body>
" . $_POST['email_body'] . "
</body>

</html>";


$mail = new PHPMailer();

$mail->IsSMTP();                       // telling the class to use SMTP

$mail->SMTPDebug = 0;                  
// 0 = no output, 1 = errors and messages, 2 = messages only.

$mail->SMTPAuth = true;                // enable SMTP authentication 
$mail->SMTPSecure = "tls";              // sets the prefix to the servier
$mail->Host = "smtp.mcgill.ca";        // sets McGill as the SMTP server
$mail->Port = 587;                     // set the SMTP port for the McGill 

$user_email = $_POST['user_email'];

$mail->Username = $_POST['user_email'];
$mail->Password = $_POST['user_pass'];      

$mail->CharSet = 'windows-1250';
//$mail->SetFrom ($_POST['user_email'], 'Tradesheet Mailer');
$mail->SetFrom ($user_email, 'Tradesheet Mailer');
//$mail->AddBCC ( 'sales@example.com', 'Example.com Sales Dep.'); 
$mail->Subject = $subject;
$mail->ContentType = 'text/html'; 
$mail->IsHTML(true);

$mail->Body = $body_of_your_email; 
// you may also use $mail->Body = file_get_contents('your_mail_template.html');

$mail->AddAddress ($user_email);     
// you may also use this format $mail->AddAddress ($recipient);

if(!$mail->Send()) 
{
    $error_message = "Mailer Error: " . $mail->ErrorInfo;
    //print "email body " . $_POST['user_email'] . $_POST['user_pass'];
    print "Error Sending Email to : " . $_POST['user_email'] . $_POST['user_pass'];;
    print $mail->ErrorInfo;
} else {
    $error_message = "Successfully sent!";
    print "Email Sent Successfully to " . $user_email;
}

// You may delete or alter these last lines reporting error messages, but beware, that if you delete the $mail->Send() part, the e-mail will not be sent, because that is the part of this code, that actually sends the e-mail
?>