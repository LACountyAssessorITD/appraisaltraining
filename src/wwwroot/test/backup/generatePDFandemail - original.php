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
include_once "constants.php";

session_start();
///////////////////////////////////////////////////////////////////
/* Access Database here */
$serverName = SQL_SERVER_NAME;
$uid = SQL_SERVER_USERNAME;
$pwd = SQL_SERVER_PASSWORD;
$db = SQL_SERVER_BOEDATABASE;
$connectionInfo = array( "UID"=>$uid,
                         "PWD"=>$pwd,
                         "Database"=>$db,
             "ReturnDatesAsStrings"=>true);  // convert datetime to string

/* Connect using SQL Server Authentication. */
$conn = sqlsrv_connect( $serverName, $connectionInfo);
if( $conn === false )
{
     echo "Unable to connect.</br>";
     die( print_r( sqlsrv_errors(), true));
}
$totalcarryover = 0;

$certid = $_SESSION["id"];
$year = $_SESSION["year"];

///////////////////////////////////////////////////////////////////

$pdf = new myPDF('L','mm','A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->personInfo($conn);

sqlsrv_close($conn);
//$pdf->Output('I');

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

$mail->Subject   = 'Your Report for FY'.$year.'-'.($year+1);
$mail->Body      = 'Please see the attached';
$mail->AddAddress('assessortestpdf@gmail.com');
$mail->addStringAttachment($pdf->Output("S",'Report_'.$certid.'.pdf'), 'Report_'.$certid.'.pdf', $encoding = 'base64', $type = 'application/pdf');
if(!$mail->send()) {
     echo "fail";
}
else{
   // $_SESSION["current"] = $_SESSION["current"] + 1;
   // $temp = $_SESSION["current"];
   // echo $temp;
   echo "success";
   flush();
   ob_flush();
}
?>
