<?php
session_start();

// Fungsi untuk memeriksa apakah pengguna sudah login dan merupakan admin
function checkAdminLoggedIn() {
    if (!isset($_SESSION["user_id"])) {
        header("Location: login.php");
        exit; 
    }

    if (!isset($_SESSION["role"]) || $_SESSION["role"] !== 'admin') {
        header("Location: index.php"); 
        exit; 
    }
}

checkAdminLoggedIn();
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
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
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
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 40px;
        }

        .container {
            margin: 0 auto;
            max-width: 1100px;
        }

        /* Container for the top actions */
        .top-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        /* Button styling */
        .buttons-group {
            display: flex;
            gap: 5px; /* Adds space between buttons */
        }

        /* Styling for both add and print buttons */
        .add-button, .print-button {
            padding: 10px 15px;
            background-color: black;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            display: flex;
            align-items: center;
            transition: background-color 0.3s;
            font-family: 'Poppins', sans-serif; /* Ensure Poppins font is applied */
        }

        .add-button:hover, .print-button:hover {
            background-color: white;
            color: black;
        }

        .add-button i, .print-button i {
            margin-right: 5px;
        }

        /* Search bar styling */
        .search-bar {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Input field for search */
        .search-bar input[type="text"] {
            width: 250px;
            padding: 10px; 
            border: 1px solid #ccc;
            border-radius: 5px;
            font-family: 'Poppins', sans-serif; 
            font-size: 14px; 
            
        }

        /* Submit button for search */
        .search-bar input[type="submit"] {
            padding: 10px 15px; 
            background-color: black;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            font-size: 14px; 
            font-family: 'Poppins', sans-serif;
        }

        .search-bar input[type="submit"]:hover {
            background-color: white;
            color: black;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0; 
            margin-top: 20px;
            font-size: 14px; 
            background-color: white;
            border: 1px solid #ddd; 
            border-radius: 8px;
            overflow: hidden;
        }

        table th, table td {
            padding: 15px;
            text-align: center;
        }

        table th {
            background-color: #FFEEA9;
            color: black;
            position: sticky; 
            top: 0;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            z-index: 1;
            text-align: center;
        }

        table td {
            border-bottom: 1px solid #ddd;
            color: #333;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        /* Notification styling */
        .notification {
            margin: 10px 0;
            padding: 10px;
            background-color: #d1e7dd;
            color: #0f5132;
            border: 1px solid #badbcc;
            border-radius: 5px;
            text-align: center;
        }

        @media print {
        .sidebar {
            display: none;
        }

        /* Sembunyikan elemen lain yang tidak ingin dicetak */
        .top-actions, .print-button, .add-button {
            display: none;
        }

        /* Hapus semua elemen di luar main */
        body > *:not(main) {
            display: none;
        }

        main {
            display: block;
            margin: 0; 
            padding: 0; 
            text-align: center; 
        }

        h1 {
            display: block;
            margin: 20px 0; 
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse; 
            margin: 0 auto;
        }

        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }

        /* Sembunyikan kolom aksi dalam tabel */
        td:last-child, th:last-child {
            display: none; 
        }
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
        <h1>Kelola Data Foto</h1>
        <div class="container">
            <div class="top-actions">
                <div class="buttons-group">
                <a href="#" onclick="console.log('Print clicked'); window.print();" class="print-button">
                    <i class="fas fa-print"></i> Cetak Data
                </a>
                <a href="admin_tambah.php" class="add-button">
                    <i class="fas fa-plus"></i> Tambah Data
                </a>
                </div>
                
                <!-- Search Form -->
                <form action="admin_dashboard.php" method="get" class="search-bar">
                    <input type="text" id="cari" name="cari" placeholder="Telusuri foto...">
                    <input type="submit" value="Cari">
                </form>
            </div>

            <!-- Table -->
            <table>
                <tr>
                    <th>No</th>
                    <th>AlbumID</th>
                    <th>Album</th>
                    <th>Judul Foto</th>
                    <th>Deskripsi</th>
                    <th>Tanggal Unggah</th>
                    <th>Foto</th>
                    <th>Aksi</th>
                </tr>

                <?php
                include 'config.php';
                $no = 1;
                $searchQuery = "";
                if (isset($_GET['cari'])) {
                    $search = $conn->real_escape_string($_GET['cari']);
                    $searchQuery = "WHERE f.JudulFoto LIKE '%$search%' OR f.DeskripsiFoto LIKE '%$search%' OR a.NamaAlbum LIKE '%$search%' OR f.AlbumID LIKE '%$search%'";
                }

                $sql = "SELECT f.*, a.NamaAlbum FROM foto f LEFT JOIN album a ON f.AlbumID = a.AlbumID $searchQuery";
                $result = $conn->query($sql);
                
                while ($foto = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $no++ . "</td>";
                    echo "<td>" . htmlspecialchars($foto['AlbumID']) . "</td>";
                    echo "<td>" . htmlspecialchars($foto['NamaAlbum']) . "</td>";
                    echo "<td>" . htmlspecialchars($foto['JudulFoto']) . "</td>";
                    echo "<td>" . htmlspecialchars($foto['DeskripsiFoto']) . "</td>";
                    echo "<td>" . htmlspecialchars($foto['TanggalUnggah']) . "</td>";
                    echo "<td><img src='" . htmlspecialchars($foto['LokasiFile']) . "' alt='" . htmlspecialchars($foto['JudulFoto']) . "' style='max-width: 100px; max-height: 100px;'></td>";
                    echo "<td>
                            <a href='admin_edit.php?id_foto=" . $foto['FotoID'] . "' class='action-button edit-button'>edit</a>
                            <a href='download.php?image=" . urlencode(basename($foto['LokasiFile'])) . "' class='action-button cetak-button'>unduh</a>
                            <a href='admin_hapus.php?id_foto=" . $foto['FotoID'] . "' class='action-button delete-button' onclick='return confirm(\"Anda yakin ingin menghapus foto ini?\");'>hapus</a>
                        </td>";
                    echo "</tr>";
                }
                ?>
            </table>
        </div>
    </main>
</body>
</html>
