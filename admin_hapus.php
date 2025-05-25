<?php
session_start();
include 'config.php';


$success = false;
$error = "";

if (isset($_GET['id_foto'])) {
    $id_foto = $conn->real_escape_string($_GET['id_foto']);

    $sql = "SELECT LokasiFile FROM foto WHERE FotoID = '$id_foto'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $foto = $result->fetch_assoc();
        $fotoFile = $foto['LokasiFile'];

        $deleteSql = "DELETE FROM foto WHERE FotoID = '$id_foto'";
        if ($conn->query($deleteSql) === TRUE) {
            // Delete the file from the server
            $filePath = 'uploads/' . basename($fotoFile);
            if (file_exists($filePath)) {
                unlink($filePath); 
            }
            $success = true; 
        } else {
            $error = "Error deleting record: " . $conn->error; 
        }
    } else {
        $error = "Photo not found.";
    }
} else {
    $error = "Invalid request.";
}

$conn->close();

// Redirect to dashboard with a message
if ($success) {
    header("Location: admin_dashboard.php?message=Photo deleted successfully.");
    exit();
} else {
    header("Location: admin_dashboard.php?error=" . urlencode($error));
    exit();
}
?>
