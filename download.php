<?php
// Mengambil nama gambar dari parameter URL
$image = isset($_GET['image']) ? $_GET['image'] : '';

// Pastikan gambar ada sebelum melanjutkan
if (!$image) {
    die('Gambar tidak ditemukan.');
}

// Lokasi direktori tempat gambar disimpan
$imagePath = 'uploads/' . basename($image);

// Cek apakah file gambar ada
if (file_exists($imagePath)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($imagePath) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($imagePath));
    readfile($imagePath);
    exit;
} else {
    die('Gambar tidak ditemukan di: ' . $imagePath);
}