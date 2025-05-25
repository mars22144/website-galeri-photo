<?php
// Include database connection
require 'config.php';

session_start();

// Fetch albums from the database
$query = "SELECT * FROM album";
$albums = $conn->query($query);

// Initialize array to store photos by album ID
$photos = [];

if ($albums->num_rows > 0) {
    while ($album = $albums->fetch_assoc()) {
        $albumId = $album['AlbumID'];
        $photoQuery = "SELECT * FROM foto WHERE AlbumID = $albumId";
        $photoResult = $conn->query($photoQuery);

        // Store each photo under its respective AlbumID in the $photos array
        if ($photoResult->num_rows > 0) {
            $photos[$albumId] = $photoResult->fetch_all(MYSQLI_ASSOC);
        } else {
            $photos[$albumId] = []; 
        }
    }
    // Move the result pointer back to the beginning of the albums query
    $albums->data_seek(0);
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Website Galeri Photo</title>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        /* Global Styles */
        html, body {
            height: 100%;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            display: flex;
            flex-direction: column;
            background-color: #FFEEA9;
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
            top: 0;
            left: 0;
        }

        .navbar .title {
            font-size: 24px;
            color: black;
            font-weight: bold;
            text-transform: uppercase;
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

        /* Hero Section */
        .hero {
            height: 400px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
        }

        .hero-content {
            max-width: 600px;
            color: #333;
        }

        .hero h1 {
            font-size: 35px;
            font-weight: bold;
            margin-top: 45px;
            margin-bottom: 10px;
        }

        .hero p {
            font-size: 20px;
            margin-bottom: 20px;
        }

        /* Albums Section */
        .section {
            padding: 40px;
            margin: 0 auto;
            max-width: 1200px;
        }

        .section h3 {
            font-size: 28px;
            color: #2c3e50;
            text-align: center;
            margin-bottom: 25px;
        }

        /* Albums List */
        .album-list {
            list-style-type: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .album-list li {
            flex: 1 1 18%;
            text-align: center;
            margin: 10px;
            padding: 20px;
            border-radius: 12px;
            background: #FBFBFB;
            transition: all 0.3s ease;
        }

        .album-list li:hover {
            transform: translateY(-10px);
            box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.5);
            background-color: #FFBF78;
        }

        .album-list li a {
            text-decoration: none;
            color: #2c3e50;
        }

        .album-list li p {
            margin-top: 10px;
            font-size: 18px;
            font-weight: bold;
        }

        .upload-button {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .upload-button {
            position: fixed; 
            bottom: 30px;
            right: 30px; 
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .rounded-button {
            background-color: #FFBF78;
            color: white;
            width: 60px; 
            height: 60px; 
            border-radius: 50%; 
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); 
            transition: background-color 0.3s, transform 0.3s; 
        }

        .rounded-button:hover {
            background-color: #FF7D29; 
            transform: translateY(-3px); 
            color: black;
        }

        /* Grid Gallery Styles */
        .grid-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .grid-item {
            position: relative;
            overflow: hidden;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .grid-item img {
            width: 100%;
            height: auto;
            display: block;
            transition: transform 0.3s ease;
        }

        .grid-item:hover img {
            transform: scale(1.05);
        }

        .overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.6);
            color: white;
            padding: 10px;
            text-align: center;
            opacity: 0;
            transition: opacity 0.3s ease;
            font-size: 18px;
        }

        .grid-item:hover .overlay {
            opacity: 1;
        }

        footer { 
            text-align: center; 
            padding: 10px; 
            background-color: #fff; 
            color: #333; 
            margin-top: 25px; 
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

    <!-- Hero Section -->
    <div class="hero">
        <div class="hero-content">
            <?php if (isset($_SESSION['username'])): ?>
                <h1>
                    Halo <span style="color: #FF7D29;"><?= htmlspecialchars($_SESSION['username']); ?>!</span><br>
                    Selamat Datang
                </h1>
            <?php else: ?>
                <h1>Halo, Selamat Datang</h1>
            <?php endif; ?>
            <p>Temukan dan unggah foto-foto terbaik Anda.</p>
        </div>
    </div>

    <!-- Albums Section -->
    <div class="section">
        <ul class="album-list">
            <?php if ($albums->num_rows > 0): ?>
                <?php while ($album = $albums->fetch_assoc()): ?>
                    <li>
                        <a href="viewalbum.php?album=<?= $album['AlbumID']; ?>">
                            <i class="fas fa-folder" style="font-size: 50px; color: #FF7D29;"></i>
                            <p><?= $album['NamaAlbum']; ?></p>
                        </a>
                    </li>
                <?php endwhile; ?>
            <?php endif; ?>
        </ul>
    </div>

    <!-- Photo Gallery Section -->
    <div class="section">
        <h3>Photo Gallery</h3>
        <div class="grid-gallery">
            <?php 
            foreach ($photos as $albumId => $photoArray): 
                foreach ($photoArray as $photo): ?>
                    <div class="grid-item">
                        <img src="<?= htmlspecialchars($photo['LokasiFile']); ?>" alt="<?= htmlspecialchars($photo['JudulFoto']); ?>">
                        <div class="overlay">
                            <p><?= htmlspecialchars($photo['JudulFoto']); ?></p>
                        </div>
                    </div>
                <?php endforeach; 
            endforeach; ?>
        </div>
    </div>

    <div class="upload-button">
        <a href="upload.php" class="rounded-button">
            <i class="fas fa-plus"></i>
        </a>
    </div>

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