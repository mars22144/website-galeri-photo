<?php
session_start();
include 'config.php';

// Aktifkan tampilan kesalahan
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Cek apakah pengguna sudah login
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$userID = $_SESSION["user_id"]; 

// Cek apakah ID foto sudah diberikan melalui URL
if (!isset($_GET['foto'])) {
    echo "ID foto tidak diberikan.";
    exit;
}

$fotoID = $_GET['foto'];

// Ambil data foto berdasarkan ID foto
$sql = "SELECT * FROM foto WHERE FotoID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $fotoID);
$stmt->execute();
$result = $stmt->get_result();
$foto = $result->fetch_assoc();

if (!$foto) {
    echo "Foto tidak ditemukan.";
    exit;
}

// Cek apakah pengguna adalah pemilik foto (berdasarkan UserID di tabel foto)
if ($foto['UserID'] != $userID) {
    echo "<script>alert('Akses ditolak. Kamu bukan pemilik foto ini!'); window.location.href = 'index.php';</script>";
    exit;
}

// Proses form ketika disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judulFoto = $_POST['judul_foto'];
    $deskripsiFoto = $_POST['deskripsi_foto'];
    $albumID = $_POST['album_id'];
    $uploadOk = 1;

    // Proses upload file jika ada file baru yang diupload
    if (!empty($_FILES["foto"]["name"])) {
        $targetDir = "uploads/";
        $targetFile = $targetDir . basename($_FILES["foto"]["name"]);
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
                $sqlUpdate = "UPDATE foto SET LokasiFile = ? WHERE FotoID = ?";
                $stmtUpdate = $conn->prepare($sqlUpdate);
                $stmtUpdate->bind_param("si", $targetFile, $fotoID);
                $stmtUpdate->execute();
            } else {
                echo "Maaf, terjadi kesalahan saat mengupload file.";
                exit;
            }
        }
    }

    // Perbarui data di database
    $sql = "UPDATE foto SET JudulFoto = ?, DeskripsiFoto = ?, AlbumID = ? WHERE FotoID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $judulFoto, $deskripsiFoto, $albumID, $fotoID);

    if ($stmt->execute()) {
        header('Location: index.php');
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $stmt->error; 
    }
}

// Ambil daftar album
$sql = "SELECT AlbumID, NamaAlbum FROM album";
$result = $conn->query($sql); 

if (!$result) {
    echo "Query Error: " . mysqli_error($conn);
}

mysqli_close($conn);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Foto - <?= htmlspecialchars($foto['JudulFoto']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            display: flex;
            flex-direction: column;
        }

        body {
            font-size: 14px;
        }

        /* Navbar Styles */
        .navbar {
            background-color: #FEFFD2;
            padding: 15px 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            z-index: 1000;
            position: fixed;
        }

        .navbar .title {
            font-size: 24px;
            color: black;
            font-weight: bold;
        }

        .navbar .nav-links {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .navbar .nav-links a {
            color: #333;
            text-decoration: none;
            font-size: 18px;
            padding: 8px 15px;
        }

        .navbar .nav-links a:hover {
            color: #FF7D29;
        }

        .navbar .profile-icon {
            font-size: 36px;
            color: black;
            cursor: pointer;
            margin-right: 20px;
        }

        .navbar .dropdown-menu {
            display: none;
            position: absolute;
            right: 20px;
            top: 60px;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
            z-index: 1000;
            min-width: 160px;
        }

        .navbar .dropdown-menu a {
            display: block;
            padding: 12px 16px;
            color: #333;
            text-decoration: none;
            text-align: left;
        }

        .navbar .dropdown-menu a:hover {
            background-color: #ffffff;
            color: #FF7D29;
        }

        /* Main Styles */
        main {
            margin: 135px auto; 
            padding: 20px;
            max-width: 600px;
            background-color: #ffffff; 
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
        }

        h1 {
            font-size: 24px; 
            color: #2c3e50; 
            margin-bottom: 20px;
            text-align: center; 
            border-bottom: 2px solid #C0C78C; 
            padding-bottom: 10px; 
        }

        .form-group {
            margin-bottom: 15px; 
            padding: 10px 0; 
        }

        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 8px;
            font-weight: 500;
            color: #2c3e50;
        }

        input[type="text"],
        textarea,
        select,
        input[type="file"] {
            width: 100%;
            padding: 12px; 
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
            transition: border-color 0.3s; 
        }

        input[type="text"]:focus,
        textarea:focus,
        select:focus,
        input[type="file"]:focus {
            border-color: #C0C78C; 
            outline: none; 
        }

        input[type="submit"] {
            background-color: #C0C78C;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s; 
        }

        input[type="submit"]:hover {
            background-color: #A6B37D;
            transform: translateY(-2px);
        }

        input[type="submit"]:active {
            transform: translateY(0); 
        }

        /* Footer */
        footer { 
            text-align: center; 
            padding: 10px; 
            color: #333; 
            margin-top: 20px; 
            width: 100%; 
            bottom: 0; 
            left: 0; 
        } 

        footer p { 
            font-size: 15px;  
            margin: 0; 
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .section .col-5, .container .col-4 {
                flex: 1 1 100%;
            }
        }

        /* Image Preview */
        .image-preview {
            max-width: 100%; 
            margin: 10px 0; 
            border: 1px solid #ccc; 
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="title">Website Galeri Photo</div>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="album.php">Album</a>
            <div class="profile-actions">
                <div class="profile-icon" onclick="toggleDropdown()">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="dropdown-menu" id="dropdown-menu">
                    <a href="profile.php">Profil</a>
                    <a href="admin_dashboard.php">Dashboard Admin</a>
                    <a href="logout.php">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <main>
        <h1>Edit Foto</h1>
        
        <!-- Display the current photo -->
        <img src="<?= htmlspecialchars($foto['LokasiFile']); ?>" alt="Current Photo" class="image-preview">

        <form action="" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="judul_foto">Judul Foto:</label>
                <input type="text" name="judul_foto" value="<?= htmlspecialchars($foto['JudulFoto']); ?>" required>

                <label for="deskripsi_foto">Deskripsi Foto:</label>
                <textarea name="deskripsi_foto" required><?= htmlspecialchars($foto['DeskripsiFoto']); ?></textarea>

                <label for="album_id">Pilih Album:</label>
                <select name="album_id" required>
                    <?php
                    // Cek apakah ada album
                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $selected = ($foto['AlbumID'] == $row['AlbumID']) ? 'selected' : '';
                            echo "<option value='{$row['AlbumID']}' $selected>{$row['NamaAlbum']}</option>";
                        }
                    } else {
                        echo "<option value=''>Tidak ada album tersedia</option>";
                    }
                    ?>
                </select>
                
                <label for="foto">Ganti Foto (Opsional):</label>
                <input type="file" name="foto">
            </div>

            <input type="submit" value="Simpan"> 
        </form>
    </main>

    <footer>
    <p>&copy; 2024 Fauziadnan XII PPLG 1</p>
    </footer>

    <script>
        function toggleDropdown() {
            var dropdown = document.getElementById('dropdown-menu');
            dropdown.style.display = (dropdown.style.display === 'block') ? 'none' : 'block';
        }
    </script>
</body>
</html>
