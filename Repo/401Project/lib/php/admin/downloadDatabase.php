<?php

// Get real path for our folder
$rootPath = realpath('D:/temp/'.$_GET['data']);
$zip_file = "BOE_RAW_DATA_".$_GET['data'].".zip";

// Initialize archive object
$zip = new ZipArchive();
$tmp_file = tempnam('.','');
$zip->open($tmp_file, ZipArchive::CREATE | ZipArchive::OVERWRITE);

// Create recursive directory iterator
/** @var SplFileInfo[] $files */
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootPath),
    RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ($files as $name => $file)
{
    // Skip directories (they would be added automatically)
    if (!$file->isDir())
    {
        // Get real and relative path for current file
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen($rootPath) + 1);

        // Add current file to archive
        $zip->addFile($filePath, $relativePath);
    }
}

// Zip archive will be created only after closing object
$zip->close();
header("Content-Disposition: attachment; filename=" . urlencode($zip_file));
header("Content-Type: application/force-download");
header("Content-Type: application/octet-stream");
header("Content-Type: application/download");
header("Content-Description: File Transfer");
header("Content-Length: " . filesize($zip_file));
flush(); // this doesn't really matter.
// $fp = fopen($zip_file, "r");
ob_end_clean();
readfile($tmp_file);
?>
