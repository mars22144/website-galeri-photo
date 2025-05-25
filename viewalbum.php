<?php
session_start();
require_once("config.php");

// Mendapatkan AlbumID dari URL
$albumID = filter_input(INPUT_GET, 'album', FILTER_SANITIZE_NUMBER_INT);

// Pastikan album ID valid
if (!$albumID) {
    echo "Album tidak valid.";
    exit;
}

// Fetch data album dari database dengan mysqli
$sql_album = "SELECT * FROM album WHERE AlbumID = ?";
$stmt_album = $conn->prepare($sql_album);
$stmt_album->bind_param("i", $albumID);
$stmt_album->execute();
$result_album = $stmt_album->get_result();
$album = $result_album->fetch_assoc();

// Jika album tidak ditemukan, tampilkan pesan error
if (!$album) {
    echo "Album tidak ditemukan.";
    exit;
}

// Fetch foto terkait album, termasuk jumlah like dan komentar
$sql_foto = "
    SELECT f.*, 
           u.Username,
           f.TanggalUnggah,
           (SELECT COUNT(*) FROM likefoto WHERE FotoID = f.FotoID) AS total_likes,
           (SELECT COUNT(*) FROM komentarfoto WHERE FotoID = f.FotoID) AS total_comments,
           (SELECT COUNT(*) FROM likefoto WHERE FotoID = f.FotoID AND UserID = ?) AS user_liked
    FROM foto f
    JOIN user u ON f.UserID = u.UserID
    WHERE f.AlbumID = ?";
$stmt_foto = $conn->prepare($sql_foto);
$stmt_foto->bind_param("ii", $_SESSION['user_id'], $albumID); // bind user ID
$stmt_foto->execute();
$result_foto = $stmt_foto->get_result();
$fotos = $result_foto->fetch_all(MYSQLI_ASSOC);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Website Galeri Photo - <?= htmlspecialchars($album['NamaAlbum']); ?></title>
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

        /* Styles for the section */
        .section {
            padding: 40px;
            margin: 0 auto;
            max-width: 1200px;
        }

        /* Heading styles */
        .section h3 {
            font-size: 28px;
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
        }

        .photo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(345px, 1fr)); /* Sesuaikan min-width card agar lebih besar */
            gap: 45px;
            justify-content: space-between;
            margin: 50px auto;
            max-width: 1200px;
        }

        /* Individual photo card styles */
        .photo-card {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 16px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            width: 100%; /* Mengisi seluruh lebar kolom */
            max-width: 100%; /* Pastikan lebar maksimal mengikuti kolom grid */
            box-sizing: border-box; /* Pastikan padding dan border tidak menambah ukuran card */
        }

        /* Menambahkan padding pada img untuk konsistensi */
        .photo-card img {
            width: 100%; /* Memastikan gambar mengisi lebar kontainer */
            height: 300px; /* Tetapkan tinggi tetap untuk gambar */
            object-fit: cover; /* Memastikan gambar tidak terdistorsi */
            border-radius: 8px;
        }

        .photo-card h4 {
            font-size: 20px;
            font-weight: bold;
            margin-top: 10px;
        }

        .photo-card p {
            color: #666;
        }

        /* Like and Comment Counts */
        .photo-card .mt-2 {
            margin-top: 10px;
        }

        .photo-card .flex {
            display: flex;
            align-items: center;
        }

        /* Comments Section */
        .comments {
            margin-top: 30px;
            padding: 20px;
            background-color: #f2f2f2;
            border-radius: 8px;
        }

        .comments h4 {
            margin-bottom: 20px;
        }

        /* Comment styles */
        .comment {
            margin-bottom: 10px;
            padding: 10px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Buttons */
        .like-btn,
        .comment-btn {
            background-color: #FF7D29; 
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .comment-btn {
            background-color: #FF7D29; 
        }

        .like-btn:hover,
        .comment-btn:hover {
            background-color: #FF7D29;; /* Warna saat hover untuk like button */
        }

        .comment-btn:hover {
            background-color: #FEFFD2; /* Warna saat hover untuk comment button */
            color: black; 
        }

        input[type="text"] {
            width: 200px; /* Set a fixed width for uniformity */
            padding: 8px; /* Add padding for better appearance */
            border: 1px solid #ccc; /* Add a border */
            border-radius: 4px; /* Rounded corners */
            box-sizing: border-box; /* Ensure padding and border are included in total width */
            transition: border-color 0.3s; /* Smooth transition for focus effect */
        }

        /* Focus effect */
        input[type="text"]:focus {
            border-color: #2563EB; /* Change border color on focus */
            outline: none; /* Remove default outline */
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

        .dropdown-menu {
            display: none;
            position: absolute; /* Pastikan ini absolute */
            z-index: 1000;
            min-width: 160px;
            top: 100%; /* Posisikan dropdown di bawah tombol */
            right: 0; /* Menjaga dropdown tetap terhadap tombol */
            margin-top: 5px; /* Jarak antara dropdown dan tombol */
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
            <div class="profile-icon" onclick="toggleProfileDropdown()">
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

    <!-- Album Info -->
    <br><br><br><br>
    <div class="section">
        <h3><?= htmlspecialchars($album['NamaAlbum']); ?></h3>
        <p class="text-gray-600 text-center"><?= htmlspecialchars($album['Deskripsi']); ?></p>

        <!-- Photos Section -->
        <div class="photo-grid">
            <?php foreach ($fotos as $foto): ?>
                <div class="photo-card">
                    <img src="<?= htmlspecialchars($foto['LokasiFile']); ?>" alt="<?= htmlspecialchars($foto['JudulFoto']); ?>">
                    <h4><?= htmlspecialchars($foto['JudulFoto']); ?></h4>
                    <p><?= htmlspecialchars($foto['DeskripsiFoto']); ?></p>
                    <p class="text-black-500">Uploaded by: <?= htmlspecialchars($foto['Username']); ?> on <?= htmlspecialchars(date('d F Y', strtotime($foto['TanggalUnggah']))); ?></p>

                    <!-- Display Like and Comment Counts -->
                    <div class="mt-2 flex">
                        <span class="mr-4 flex items-center">
                            <i class="fas fa-heart mr-1"></i>
                            <?= htmlspecialchars($foto['total_likes']); ?> Likes
                        </span>
                        <span class="flex items-center">
                            <i class="fas fa-comment mr-1"></i>
                            <?= htmlspecialchars($foto['total_comments']); ?> Comments
                        </span>
                    </div>

                    <!-- Fetch Comments for this photo -->
                    <div class="comments mt-4">
                        <?php
                        $fotoID = $foto['FotoID'];
                        $sql_comments = "
                            SELECT k.*, u.Username 
                            FROM komentarfoto k 
                            JOIN user u ON k.UserID = u.UserID 
                            WHERE k.FotoID = ?";
                        $stmt_comments = $conn->prepare($sql_comments);
                        $stmt_comments->bind_param("i", $fotoID);
                        $stmt_comments->execute();
                        $result_comments = $stmt_comments->get_result();
                        $comments = $result_comments->fetch_all(MYSQLI_ASSOC);
                        ?>
                        <?php foreach ($comments as $comment): ?>
                            <div class="comment">
                                <strong><?= htmlspecialchars($comment['Username']); ?>:</strong>
                                <p><?= htmlspecialchars($comment['IsiKomentar']); ?></p>
                                <small class="text-gray-500"><?= htmlspecialchars($comment['TanggalKomentar']); ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Like, Comment, and Edit Buttons -->
                    <div class="mt-2 flex">
                    <form action="like.php" method="POST" class="inline">
                        <input type="hidden" name="foto_id" value="<?= $foto['FotoID']; ?>">
                        <input type="hidden" name="album_id" value="<?= $albumID; ?>">
                        <?php if ($foto['user_liked'] > 0): // If user has liked ?>
                            <button type="submit" class="like-btn text-red-500 hover:text-red-700 transition duration-200" name="action" value="unlike">
                                <i class="fas fa-heart"></i>
                            </button>
                        <?php else: // If user has not liked ?>
                            <button type="submit" class="like-btn text-white-500 hover:text-red-500 transition duration-200" name="action" value="like">
                                <i class="fas fa-heart"></i>
                            </button>
                        <?php endif; ?>
                    </form>
                    <form action="comment.php" method="POST" class="inline ml-2 w-full flex items-center">
                        <input type="hidden" name="foto_id" value="<?= $foto['FotoID']; ?>">
                        <input type="hidden" name="album_id" value="<?= $albumID; ?>">
                        <input type="text" name="komentar" placeholder="Tulis komentar..." class="border rounded p-1 flex-grow mr-2" required>
                        <button type="submit" class="comment-btn">Kirim</button>
                    </form>

                    <!-- Dropdown Button -->
                    <div class="relative inline-block text-left ml-2">
                        <div>
                            <button onclick="toggleDropdown('editDropdown_<?= $foto['FotoID']; ?>')" class="flex items-center bg-blue-500 text-white px-3 py-2 rounded hover:bg-blue-700 focus:outline-none">
                                <i class="fas fa-edit mr-1"></i> Actions
                                <i class="fas fa-caret-down ml-1"></i>
                            </button>
                        </div>

                        <!-- Dropdown Menu -->
                        <div id="editDropdown_<?= $foto['FotoID']; ?>" class="dropdown-menu absolute right-0 z-10 mt-2 w-48 bg-white border border-gray-200 rounded-md shadow-lg hidden">
                            <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
                                <a href="edit.php?foto=<?= htmlspecialchars($foto['FotoID']); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Edit Photo</a>
                                <a href="cetak.php?id_foto=<?= htmlspecialchars($foto['FotoID']); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Print Photo</a>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            <?php endforeach; ?>
        </div>

    <footer>
    <p>&copy; 2024 Fauziadnan XII PPLG 1</p>
    </footer>

    <script>
        function toggleDropdown(dropdownId) {
            // Menyembunyikan dropdown lainnya
            const dropdowns = document.querySelectorAll('.dropdown-menu');
            dropdowns.forEach(dropdown => {
                if (dropdown.id !== dropdownId) {
                    dropdown.style.display = 'none'; // Sembunyikan dropdown lain
                }
            });

            // Tampilkan atau sembunyikan dropdown yang dipilih
            var dropdown = document.getElementById(dropdownId);
            dropdown.style.display = (dropdown.style.display === 'block') ? 'none' : 'block';
        }

        // For the profile dropdown
        function toggleProfileDropdown() {
            var dropdown = document.getElementById('dropdown-menu');
            dropdown.style.display = (dropdown.style.display === 'block') ? 'none' : 'block';
        }
    </script>
</body>
</html>