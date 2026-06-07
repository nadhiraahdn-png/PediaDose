<?php
$zip_file = 'PediaDose_Web.zip';
$extract_to = './';

if (!file_exists($zip_file)) {
    die("File $zip_file tidak ditemukan di server. Pastikan Anda sudah mengunggahnya.");
}

$zip = new ZipArchive;
$res = $zip->open($zip_file);
if ($res === TRUE) {
    $zip->extractTo($extract_to);
    $zip->close();
    echo "<h2 style='color:green;'>Berhasil! File ZIP telah diekstrak dengan sempurna, termasuk semua foldernya.</h2>";
    echo "<p>Silakan kembali ke File Manager dan refresh halamannya. Anda bisa menghapus file unzip.php ini setelah selesai.</p>";
} else {
    echo "<h2 style='color:red;'>Gagal mengekstrak file ZIP. Kode error: $res</h2>";
}
?>
