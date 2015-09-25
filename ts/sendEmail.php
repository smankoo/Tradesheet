<?php
//require_once('class.phpmailer.php');
require_once('PHPMailerAutoload.php');


$subject = "Test Mailer Subject";
$body_of_your_email = "Mail Body Shody";


$mail = new PHPMailer();

$mail->IsSMTP();                       // telling the class to use SMTP

$mail->SMTPDebug = 0;                  
// 0 = no output, 1 = errors and messages, 2 = messages only.

$mail->SMTPAuth = true;                // enable SMTP authentication 
$mail->SMTPSecure = "tls";              // sets the prefix to the servier
$mail->Host = "smtp.mcgill.ca";        // sets Gmail as the SMTP server
$mail->Port = 587;                     // set the SMTP port for the GMAIL 

$mail->Username = "sumeet.mankoo@mail.mcgill.ca";  
$mail->Password = "Sm123456";      

$mail->CharSet = 'windows-1250';
$mail->SetFrom ('sumeet.mankoo@mail.mcgill.ca', 'Sumeet Test Mailer');
//$mail->AddBCC ( 'sales@example.com', 'Example.com Sales Dep.'); 
$mail->Subject = $subject;
$mail->ContentType = 'text/plain'; 
$mail->IsHTML(false);

$mail->Body = $body_of_your_email; 
// you may also use $mail->Body = file_get_contents('your_mail_template.html');

$mail->AddAddress ('sumeet.mankoo@mail.mcgill.ca');     
// you may also use this format $mail->AddAddress ($recipient);

if(!$mail->Send()) 
{
        $error_message = "Mailer Error: " . $mail->ErrorInfo;
} else 
{
        $error_message = "Successfully sent!";
}

// You may delete or alter these last lines reporting error messages, but beware, that if you delete the $mail->Send() part, the e-mail will not be sent, because that is the part of this code, that actually sends the e-mail
?>