<?php
include 'config.php';
session_start(); 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $judulFoto = $_POST['judul_foto'];
    $deskripsiFoto = $_POST['deskripsi_foto'];
    $albumID = $_POST['album_id'];
    $userID = $_SESSION["user_id"];

    // Proses upload file
    $targetDir = "uploads/"; 
    $targetFile = $targetDir . basename($_FILES["foto"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Cek apakah file gambar adalah gambar sebenarnya atau bukan
    $check = getimagesize($_FILES["foto"]["tmp_name"]);
    if ($check === false) {
        echo "File yang diupload bukan gambar.";
        $uploadOk = 0;
    }

    // Cek ukuran file (maksimum 5MB)
    if ($_FILES["foto"]["size"] > 5000000) {
        echo "Maaf, ukuran file terlalu besar.";
        $uploadOk = 0;
    }

    // Cek format file
    if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
        echo "Maaf, hanya file JPG, JPEG, PNG & GIF yang diperbolehkan.";
        $uploadOk = 0;
    }

    // Jika semua cek lulus, upload file
    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["foto"]["tmp_name"], $targetFile)) {
            $tanggalUnggah = date('Y-m-d');
            $sql = "INSERT INTO foto (JudulFoto, DeskripsiFoto, TanggalUnggah, LokasiFile, AlbumID, UserID) 
                    VALUES ('$judulFoto', '$deskripsiFoto', '$tanggalUnggah', '$targetFile', '$albumID', '$userID')"; // Ganti $userID

            if (mysqli_query($conn, $sql)) {
                header('Location: admin_dashboard.php');
                exit(); 
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
            }
        } else {
            echo "Maaf, terjadi kesalahan saat mengupload file.";
        }
    }

    mysqli_close($conn);
}
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
            background-color: #FFEEA9;
            display: flex;
        }

        /* Sidebar Styling */
        .sidebar {
            position: fixed;
            width: 200px;
            height: 100%;
            background-color: #FFEEA9;
            padding-top: 20px;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }

        .sidebar h2 {
            text-align: center;
            color: black;
            font-size: 20px;
            margin-bottom: 30px;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: black;
            text-decoration: none;
            transition: background-color 0.3s;
            font-size: 14px;
        }

        .sidebar a:hover {
            background-color: #FEFFD2;
        }

        .sidebar i {
            margin-right: 10px;
        }

        /* Main Content */
        main {
            margin-left: 200px;
            padding: 40px;
            width: calc(100% - 200px);
            background-color: #FEFFD2;
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

        /* Styling untuk label dan input bersebelahan */
        .form-group {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .form-group label {
            width: 150px;
            font-weight: 500;
            color: #555;
            font-size: 1em;
        }

        .form-group input[type="text"],
        .form-group textarea,
        .form-group select,
        .form-group input[type="file"] {
            font-family: 'Poppins', sans-serif;
            flex: 1;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 1em;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus,
        textarea:focus,
        select:focus,
        input[type="file"]:focus {
            border-color: #C0C78C;
            outline: none;
        }

        textarea {
            resize: vertical;
            height: 120px;
        }

        input[type="submit"] {
            background-color: black;
            color: white;
            padding: 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s ease;
            width: 100%;
            font-family: 'Poppins', sans-serif;
        }

        input[type="submit"]:hover {
            background-color: white;
            color: black;
        }

        input[type="submit"]:focus {
            outline: none;
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

    <main>
        <h1>Tambah Foto</h1>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="judul_foto">Judul Foto:</label>
                <input type="text" name="judul_foto" required>
            </div>
            
            <div class="form-group">
                <label for="deskripsi_foto">Deskripsi Foto:</label>
                <textarea name="deskripsi_foto" required></textarea>
            </div>

            <div class="form-group">
                <label for="album_id">Pilih Album:</label>
                <select name="album_id" required>
                    <?php
                    // Ambil daftar album dari database
                    $sql = "SELECT AlbumID, NamaAlbum FROM album";
                    $result = mysqli_query($conn, $sql);
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<option value='{$row['AlbumID']}'>{$row['NamaAlbum']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="foto">Upload Foto:</label>
                <input type="file" name="foto" required>
            </div>
            
            <input type="submit" value="Simpan"> 
        </form>
    </main>
</body>
</html>
