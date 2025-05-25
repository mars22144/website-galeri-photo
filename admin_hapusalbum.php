<?php
session_start();
include 'config.php';

// Memeriksa apakah album ID ada di URL
if (!isset($_GET['id'])) {
    header("Location: admin_album.php"); 
    exit();
}

$albumId = $_GET['id'];
$deleteQuery = "DELETE FROM album WHERE AlbumID = '$albumId'";

if (mysqli_query($conn, $deleteQuery)) {
    header("Location: admin_album.php?status=success");
} else {
    header("Location: admin_album.php?status=error");
}

mysqli_close($conn);
?>
