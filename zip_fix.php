<?php
$source = 'c:/xampp/htdocs/PediaDose';
$destination = 'c:/xampp/htdocs/PediaDose/deploy_package/PediaDose_Web_Fix.zip';

if (file_exists($destination)) {
    unlink($destination);
}

$zip = new ZipArchive();
if (!$zip->open($destination, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
    die("Failed to create zip file.\n");
}

$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ($files as $name => $file) {
    if (!$file->isDir()) {
        $filePath = $file->getRealPath();
        
        // Skip .git and deploy_package folders
        if (strpos($filePath, '\\.git\\') !== false || strpos($filePath, '/.git/') !== false) continue;
        if (strpos($filePath, '\\deploy_package\\') !== false || strpos($filePath, '/deploy_package/') !== false) continue;

        $relativePath = substr($filePath, strlen($source) + 1);
        // Force forward slashes for Linux compatibility
        $relativePath = str_replace('\\', '/', $relativePath);
        
        $zip->addFile($filePath, $relativePath);
    }
}

$zip->close();
echo "Berhasil membuat PediaDose_Web_Fix.zip dengan pemisah folder yang benar!\n";
?>
