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

        /* Form Styling */
        .form-container {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .form-container label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .form-container input[type="text"], .form-container textarea {
            width: 98%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-family: 'Poppins', sans-serif;
        }

        .form-container input[type="submit"] {
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

        .form-container input[type="submit"]:hover {
            background-color: white;
            color: black;
        }

        /* Notification Styling */
        .notification {
            margin: 10px 0;
            padding: 10px;
            background-color: #d1e7dd;
            color: #0f5132;
            border: 1px solid #badbcc;
            border-radius: 5px;
            text-align: center;
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
        <h1>Kelola Album</h1>
        <div class="container">
            <div class="form-container">
                <h2>Tambah Album</h2>
                <form action="admin_tambahalbum.php" method="POST">
                    <label for="namaAlbum">Nama Album:</label>
                    <input type="text" id="namaAlbum" name="namaAlbum" required>

                    <label for="deskripsi">Deskripsi:</label>
                    <textarea id="deskripsi" name="deskripsi" rows="4" required></textarea>

                    <input type="submit" value="Tambah Album">
                </form>
            </div>

            <h2>Daftar Album</h2>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Album ID</th> 
                        <th>Nama Album</th>
                        <th>Deskripsi</th>
                        <th>Tanggal Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    include 'config.php';
                    
                    // Query untuk mengambil data album
                    $query = "SELECT * FROM album ORDER BY TanggalDibuat ASC";
                    $result = mysqli_query($conn, $query);
                    $no = 1;

                    // Menampilkan data album
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                            <td>{$no}</td>
                            <td>{$row['AlbumID']}</td> <!-- Display Album ID -->
                            <td>{$row['NamaAlbum']}</td>
                            <td>{$row['Deskripsi']}</td>
                            <td>{$row['TanggalDibuat']}</td>
                            <td>
                                <a href='admin_editalbum.php?id={$row['AlbumID']}' class='action-button edit-button'>Edit</a>
                                <a href='admin_hapusalbum.php?id={$row['AlbumID']}' class='action-button delete-button'>Hapus</a>
                            </td>
                        </tr>";
                        $no++;
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
