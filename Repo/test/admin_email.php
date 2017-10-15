<?php
/*
This Code dynamically generate all PDFs (for a specific fiscal year)and
send the PDF as attachment to GMAIL
@ Yining Huang		yininghu@usc.edu
*/

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
include_once 'lib/PHPMailer/src/Exception.php';
include_once 'lib/PHPMailer/src/PHPMailer.php';
include_once 'lib/PHPMailer/src/SMTP.php';

$mail = new PHPMailer;
$mail->isSMTP();                                // Set mailer to use SMTP
$mail->Host = 'smtp.gmail.com';                      // SMTP server
$mail->SMTPAuth = true;                         // Enable SMTP authentication
$mail->Username = 'assessortestpdf@gmail.com';                 // SMTP username
$mail->Password = 'passwordpdf';                 // SMTP password
$mail->SMTPSecure = 'tls';                      // Enable TLS encryption, `ssl` also accepted
$mail->From = 'assessortestpdf@gmail.com';
$mail->Port = 587;                              // SMTP Port
$mail->FromName  = 'Assessor PDF Sender';

$mail->Subject   = 'New Report Remainder';
$mail->Body      = "Hi somebody, \r\nPlease see the attached";
$mail->AddAddress('assessortestpdf@gmail.com');
//$mail->addStringAttachment($pdf->Output("S",'Report_'.$certid.'.pdf'), 'Report_'.$certid.'.pdf', $encoding = 'base64', $type = 'application/pdf');
$mail->send();
if(!$mail->send()) {
     echo "fail";
}
else{
   echo "success";
}
sleep(1);


?>