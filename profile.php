<?php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Sambungkan ke database menggunakan file konfigurasi
require_once "config.php";

// Ambil username pengguna dari sesi
$username = $_SESSION['username'];

// Periksa apakah data pengguna ditemukan dalam database
$sql = "SELECT * FROM user WHERE Username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $userId = $row['UserID'];
    $email = $row['Email'];
    $namaLengkap = $row['NamaLengkap'];
    $alamat = $row['Alamat'];
    $role = $row['Role'];
    $profile_picture = $row['profile_picture'];
} else {
    header("Location: login.php");
    exit();
}

if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

if (isset($_FILES['profile_picture'])) {
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
    if ($check !== false) {
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            $sql = "UPDATE user SET profile_picture = ? WHERE UserID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $target_file, $userId);
            $stmt->execute();
            $profile_picture = $target_file;
        } else {
            echo "Maaf, terjadi kesalahan saat mengunggah file Anda.";
        }
    } else {
        echo "File bukan gambar.";
    }
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Gallery Photo.</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #FEFFD2;
            margin: 0;
        }
        .container {
            max-width: 500px;
            margin: 50px auto;
            padding: 30px;
            background-color: #FFEEA9;
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            border: 1px solid black; 
        }
        h2 {
            text-align: center;
            color: black; 
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            color: black;
        }
        table th,
        table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid grey;
        }
        
        .logout-btn {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #C0C78C;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            font-size: 16px;
            margin-top: 20px;
            transition: background-color 0.3s ease;
        }
        .logout-btn:hover {
            background-color: #A6B37D; 
        }
        .return-link {
            text-align: center;
            margin-top: 20px;
        }
        .return-link a {
            color: #fff;
            text-decoration: none;
            padding: 8px 16px;
            background-color: black;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .return-link a:hover {
            background-color: white;
            color: black;
        }
        .profile-picture-container {
            position: relative;
            width: 150px;
            height: 150px;
            margin: 0 auto 20px;
        }
        .profile-picture {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }
        .profile-picture-upload {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
            font-size: 32px;
            cursor: pointer;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .profile-picture-container:hover .profile-picture-upload {
            opacity: 1;
        }
        .profile-picture-upload input[type="file"] {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Profile Detail</h2>
        <div class="profile-picture-container">
            <?php if (!empty($profile_picture)): ?>
                <img src="<?php echo $profile_picture; ?>" alt="Profile Picture" class="profile-picture">
            <?php else: ?>
                <img src="default-profile.png" alt="Profile Picture" class="profile-picture">
            <?php endif; ?>
            <div class="profile-picture-upload">
                <form method="post" enctype="multipart/form-data" action="">
                    <label for="profilePictureUpload">+</label>
                    <input type="file" name="profile_picture" id="profilePictureUpload" onchange="this.form.submit()">
                </form>
            </div>
        </div>
        <table>
            <tr>
                <th>User ID</th>
                <td><?php echo $userId; ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?php echo $email; ?></td>
            </tr>
            <tr>
                <th>Nama Lengkap</th>
                <td><?php echo $namaLengkap; ?></td>
            </tr>
            <tr>
                <th>Alamat</th>
                <td><?php echo $alamat; ?></td>
            </tr>
            <tr>
                <th>Role</th>
                <td><?php echo $role; ?></td>
            </tr>
        </table>
        <br>
        <div class="return-link">
            <a href="index.php">Back Home</a>
        </div>
        <br>
    </div>
</body>
</html>
