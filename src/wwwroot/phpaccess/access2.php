<?php

$db = '\\\\server\\resource\\db.mdb';
$conn = new COM('ADODB.Connection');
$conn->Open("DRIVER={Driver do Microsoft Access (*.mdb)}; DBQ=$db");

?>