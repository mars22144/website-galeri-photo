<?php
session_start();
include 'config.php';

// Pastikan FotoID diterima dari parameter URL
if (isset($_GET['id_foto'])) {
    $fotoID = intval($_GET['id_foto']); 

    // Query untuk mengambil detail foto berdasarkan FotoID
    $sql = "SELECT f.*, a.NamaAlbum 
            FROM foto f 
            LEFT JOIN album a ON f.AlbumID = a.AlbumID 
            WHERE f.FotoID = $fotoID";
    
    $result = $conn->query($sql);

    // Cek apakah foto ditemukan
    if ($result->num_rows > 0) {
        $foto = $result->fetch_assoc(); // Ambil data foto
    } else {
        // Jika tidak ditemukan, tampilkan pesan error
        echo "<script>alert('Foto tidak ditemukan.'); window.location.href='admin_dashboard.php';</script>";
        exit();
    }
} else {
    // Jika ID tidak diberikan, redirect ke dashboard
    echo "<script>alert('ID foto tidak valid.'); window.location.href='admin_dashboard.php';</script>";
    exit();
}

// Menangani pengiriman formulir untuk edit foto
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judulFoto = $conn->real_escape_string($_POST['judul_foto']);
    $deskripsiFoto = $conn->real_escape_string($_POST['deskripsi_foto']);
    $albumID = intval($_POST['album']);
    
    // Menangani penguploadan file foto
    $lokasiFile = $foto['LokasiFile'];

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
        $targetDir = "uploads/";
        $lokasiFile = $targetDir . basename($_FILES['foto']['name']);
        
        // Memindahkan file ke direktori yang dituju
        if (!move_uploaded_file($_FILES['foto']['tmp_name'], $lokasiFile)) {
            echo "<script>alert('Maaf, terjadi kesalahan saat mengupload file.');</script>";
        }
    }

    // Update query
    $updateSQL = "UPDATE foto SET 
        JudulFoto='$judulFoto', 
        DeskripsiFoto='$deskripsiFoto', 
        AlbumID='$albumID', 
        LokasiFile='$lokasiFile' 
        WHERE FotoID=$fotoID";
    
    if ($conn->query($updateSQL) === TRUE) {
        echo "<script>alert('Foto berhasil diperbarui.'); window.location.href='admin_dashboard.php';</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}

$albumQuery = "SELECT * FROM album";
$albumResult = $conn->query($albumQuery);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Galeri Photo</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
            display: flex;
        }

        /* Sidebar Styling */
        .sidebar {
            position: fixed;
            width: 200px;
            height: 100%;
            background-color: #C0C78C;
            padding-top: 20px;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }

        .sidebar h2 {
            text-align: center;
            color: white;
            font-size: 20px;
            margin-bottom: 30px;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
            transition: background-color 0.3s;
            font-size: 14px;
        }

        .sidebar a:hover {
            background-color: #A6B37D;
        }

        .sidebar i {
            margin-right: 10px;
        }

        /* Main Content */
        main {
            margin-left: 200px; /* Sesuai dengan lebar sidebar */
            padding: 40px;
            width: calc(100% - 200px);
            background-color: #f9f9f9; /* Warna latar belakang yang lebih lembut */
            min-height: 100vh;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 40px;
            font-size: 2em;
            font-weight: 600;
        }

        /* Form styling */
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: 500;
        }

        input[type="text"],
        textarea,
        select {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        textarea {
            resize: vertical;
            height: 120px;
        }

        input[type="file"] {
            margin-bottom: 15px;
        }

        button {
            padding: 12px;
            background-color: #A6B37D;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1em;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #8e9f69;
        }

    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>DASHBOARD</h2>
        <a href="admin_dashboard.php"><i class="fas fa-images"></i> Data Foto</a>
        <a href="admin_album.php"><i class="fas fa-folder"></i> Album</a>
        <a href="index.php"><i class="fas fa-user"></i> Index User</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <!-- Main Content -->
    <main>
        <h1>Edit Foto</h1>
        <form action="admin_edit.php?id_foto=<?php echo $fotoID; ?>" method="post" enctype="multipart/form-data">
            <label for="judul_foto">Judul Foto:</label>
            <input type="text" name="judul_foto" id="judul_foto" value="<?php echo htmlspecialchars($foto['JudulFoto']); ?>" required>

            <label for="deskripsi_foto">Deskripsi Foto:</label>
            <textarea name="deskripsi_foto" id="deskripsi_foto" rows="4" required><?php echo htmlspecialchars($foto['DeskripsiFoto']); ?></textarea>

            <label for="album">Album:</label>
            <select name="album" id="album" required>
                <?php
                while ($album = $albumResult->fetch_assoc()) {
                    $selected = ($album['AlbumID'] == $foto['AlbumID']) ? 'selected' : '';
                    echo "<option value='" . $album['AlbumID'] . "' $selected>" . htmlspecialchars($album['NamaAlbum']) . "</option>";
                }
                ?>
            </select>

            <label for="foto">Ganti Foto:</label>
            <input type="file" name="foto" id="foto" accept="image/*">

            <button type="submit">Perbarui</button>
        </form>
    </main>

</body>
</html>
