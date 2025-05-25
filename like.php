<?php
session_start();
require_once("config.php");

// Cek apakah pengguna sudah login
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION["user_id"];

// Mendapatkan FotoID dan AlbumID dari form
$fotoId = filter_input(INPUT_POST, 'foto_id', FILTER_SANITIZE_NUMBER_INT);
$albumId = filter_input(INPUT_POST, 'album_id', FILTER_SANITIZE_NUMBER_INT);

// Pastikan foto_id dan album_id valid
if (!$fotoId || !$albumId) {
    echo "Foto atau Album tidak valid.";
    exit;
}

// Cek apakah pengguna sudah memberi like pada foto ini
$sql_check_like = "SELECT COUNT(*) FROM likefoto WHERE FotoID = ? AND UserID = ?";
$stmt_check_like = $conn->prepare($sql_check_like);
$stmt_check_like->bind_param("ii", $fotoId, $user);
$stmt_check_like->execute();
$stmt_check_like->bind_result($like_exists);
$stmt_check_like->fetch();
$stmt_check_like->close();

// Jika pengguna belum memberi like, masukkan ke database
if ($like_exists == 0) {
    $sql_like = "INSERT INTO likefoto (FotoID, UserID, TanggalLike) VALUES (?, ?, NOW())";
    $stmt_like = $conn->prepare($sql_like);
    $stmt_like->bind_param("ii", $fotoId, $user);
    $stmt_like->execute();
    $stmt_like->close();
} else {
    // Jika pengguna sudah memberi like, hapus like dari database
    $sql_unlike = "DELETE FROM likefoto WHERE FotoID = ? AND UserID = ?";
    $stmt_unlike = $conn->prepare($sql_unlike);
    $stmt_unlike->bind_param("ii", $fotoId, $user);
    $stmt_unlike->execute();
    $stmt_unlike->close();
}

// Redirect kembali ke halaman album
header("Location: viewalbum.php?album=" . $albumId);
exit;
?>
