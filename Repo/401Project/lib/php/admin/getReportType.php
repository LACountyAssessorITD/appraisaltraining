<?php
$dir    = 'reportType';
$files1 = scandir($dir);
echo json_encode($files1);
?>