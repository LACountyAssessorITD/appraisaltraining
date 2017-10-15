<?php
if(isset($_GET["mail"] ,$_POST["subject"] ,$_POST["message"] ,$_POST["headers"])) {
    // to:
    $mail = $_GET["mail"];
    // subejct:
    $subject = $_POST["subject"];
    // message:
    $message = $_POST["message"];
    // headers ("From:".$from):
    $headers = $_POST["headers"];
    // sendMail
    mail($mail, $subject, $message, $headers);
} elseif(isset($_GET["mail"])) {
    $mail = $_GET["mail"];
    echo '
<html>
    <head>
        <title>Mail to <?php echo $mail; ?></title>
    </head>
    <body>
        <form action="sendMail.php" method="post">
            // All inputs w/ names
        </form>
    </body>
</html>';
} else {
    echo "Error";
} ?>