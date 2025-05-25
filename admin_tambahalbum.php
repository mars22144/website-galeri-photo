<?php
include 'config.php';
session_start();

// Cek apakah form sudah disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namaAlbum = mysqli_real_escape_string($conn, $_POST['namaAlbum']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $tanggalDibuat = date('Y-m-d H:i:s');

    // Ambil User ID dari sesi
    if (isset($_SESSION['user_id'])) {
        $userID = $_SESSION['user_id'];
    } else {
        header("Location: login.php");
        exit();
    }

    $query = "INSERT INTO album (NamaAlbum, Deskripsi, TanggalDibuat, UserID) 
              VALUES ('$namaAlbum', '$deskripsi', '$tanggalDibuat', '$userID')";

    // Eksekusi query dan cek keberhasilan
    if (mysqli_query($conn, $query)) {
        header("Location: admin_album.php?status=success");
        exit();
    } else {
        header("Location: admin_album.php?status=error");
        exit();
    }
}

mysqli_close($conn);
?>