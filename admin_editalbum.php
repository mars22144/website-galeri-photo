<?php
session_start();
include 'config.php';

// Memeriksa apakah album ID ada di URL
if (!isset($_GET['id'])) {
    header("Location: admin_album.php"); // Kembali ke halaman album jika ID tidak ada
    exit();
}

$albumId = $_GET['id'];

// Mengambil data album berdasarkan AlbumID
$query = "SELECT * FROM album WHERE AlbumID = '$albumId'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) === 0) {
    header("Location: admin_album.php"); // Kembali jika album tidak ditemukan
    exit();
}

$album = mysqli_fetch_assoc($result);

// Memeriksa jika form di-submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namaAlbum = $_POST['namaAlbum'];
    $deskripsi = $_POST['deskripsi'];

    // Update album di database
    $updateQuery = "UPDATE album SET NamaAlbum = '$namaAlbum', Deskripsi = '$deskripsi' WHERE AlbumID = '$albumId'";

    if (mysqli_query($conn, $updateQuery)) {
        header("Location: admin_album.php?status=success");
    } else {
        header("Location: admin_album.php?status=error");
    }
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

        form {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-size: 1em;
            color: #333;
        }

        input[type="text"], textarea {
            width: 96%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input[type="submit"] {
            padding: 10px 15px;
            background-color: #A6B37D;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 1em;
        }

        input[type="submit"]:hover {
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
        <h1>Edit Album</h1>
        <form action="" method="POST">
            <label for="namaAlbum">Nama Album:</label>
            <input type="text" id="namaAlbum" name="namaAlbum" value="<?php echo htmlspecialchars($album['NamaAlbum']); ?>" required>

            <label for="deskripsi">Deskripsi:</label>
            <textarea id="deskripsi" name="deskripsi" rows="4" required><?php echo htmlspecialchars($album['Deskripsi']); ?></textarea>

            <input type="submit" value="Update Album">
        </form>
    </main>

</body>
</html>
