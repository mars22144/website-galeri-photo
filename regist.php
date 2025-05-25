<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $username = filter_var($_POST["username"], FILTER_SANITIZE_STRING);
    $password = password_hash($_POST["password"], PASSWORD_BCRYPT); 
    $nama_lengkap = filter_var($_POST["nama_lengkap"], FILTER_SANITIZE_STRING);
    $alamat = filter_var($_POST["alamat"], FILTER_SANITIZE_STRING);
    $role = $_POST["role"]; 

    // Sambungkan ke database
    $conn = new mysqli("localhost", "root", "", "gallery");

    // Cek koneksi
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Cek apakah username sudah ada
    $stmt = $conn->prepare("SELECT * FROM user WHERE Username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>
                alert('Username sudah digunakan, silakan pilih username lain.');
                window.location.href = 'regist.php';
              </script>";
        $stmt->close();
        $conn->close();
        exit();
    }
    $stmt->close();

    // Cek apakah email sudah ada
    $stmt = $conn->prepare("SELECT * FROM user WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>
                alert('Email sudah digunakan, silakan pilih email lain.');
                window.location.href = 'regist.php';
            </script>";
        $stmt->close();
        $conn->close();
        exit();
    }
    $stmt->close();

    // Jika role adalah admin, cek admin code
    if ($role === 'admin') {
        $admin_code = $_POST["admin_code"];
        $valid_admin_code = "admin123"; 

        if ($admin_code !== $valid_admin_code) {
            echo "<script>
                    alert('Invalid Admin Code');
                    window.location.href = 'regist.php';
                  </script>";
            $conn->close();
            exit();
        }
    }

    // Buat dan jalankan SQL INSERT query dengan error checking
    $stmt = $conn->prepare("INSERT INTO user (Username, Password, Email, NamaLengkap, Alamat, Role) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt === false) {
        echo "<script>
                alert('Error preparing statement: " . $conn->error . "');
                window.location.href = 'regist.php';
              </script>";
        $conn->close();
        exit();
    }

    $stmt->bind_param("ssssss", $username, $password, $email, $nama_lengkap, $alamat, $role);

    if ($stmt->execute()) {
        header("Location: login.php");
        exit();
    } else {
        echo "<script>
                alert('Error: " . htmlspecialchars($stmt->error) . "');
                window.location.href = 'regist.php';
              </script>";
    }

    // Tutup koneksi database
    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Galeri Photo</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-image: url("foto/bg2.jpg");
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            padding-top: 100px; 
            color: #fff;
        }

        .container {
            background-color: #FFFFFF;
            border-radius: 20px;
            width: 440px;
            padding: 20px;
            box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333333;
            text-align: center;
        }

        p {
            margin-bottom: 0px;
            color: #C0C78C;
            text-align: center;
        }

        a {
            color: #C0C78C;
            text-decoration: none;
            font-weight: 600;
        }

        form {
            margin-top: 20px;
        }

        label {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 5px;
            display: block;
            color: #333333;
        }

        input[type="text"],
        input[type="password"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            color: #333333;
        }

        button {
            width: 100%;
            background-color: #C0C78C;
            color: #FFFFFF;
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
        }

        button:hover {
            background-color: #A6B37D;
        }

        #admin-code-container {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Register</h1>

        <form action="" method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="email">Email:</label>
            <input type="text" id="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="nama_lengkap">Nama Lengkap:</label>
            <input type="text" id="nama_lengkap" name="nama_lengkap" required>

            <label for="alamat">Alamat:</label>
            <input type="text" id="alamat" name="alamat" required>
            
            <label for="role">Role:</label>
            <select id="role" name="role" onchange="toggleAdminCode(this)">
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>

            <div id="admin-code-container">
                <label for="admin_code">Admin Code:</label>
                <input type="text" id="admin_code" name="admin_code">
            </div>
            
            <button type="submit">Register</button>
        </form><br>

        <p>Sudah punya akun? <a href="login.php">Login sekarang</a></p>
    </div>

    <script>
        function toggleAdminCode(select) {
            const adminCodeContainer = document.getElementById('admin-code-container');
            if (select.value === 'admin') {
                adminCodeContainer.style.display = 'block';
            } else {
                adminCodeContainer.style.display = 'none';
            }
        }
    </script>
</body>
</html>
