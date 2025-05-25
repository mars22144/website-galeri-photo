<?php
session_start();
include_once("config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Mengecek data pengguna dari database
    $sql = "SELECT UserID, Username, Password, Role FROM user WHERE Username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Debugging output
    if ($result) {
        // echo "<div>Query executed successfully.</div>"; // Hapus untuk produksi
    } else {
        echo "<script>alert('Error executing query: " . addslashes($conn->error) . "');</script>";
    }

    if ($result && $result->num_rows == 1) {
        $row = $result->fetch_assoc();
        // Verifikasi password menggunakan bcrypt
        if (password_verify($password, $row["Password"])) {
            // Login sukses, set session
            $_SESSION["user_id"] = $row["UserID"];
            $_SESSION["username"] = $row["Username"];
            $_SESSION["role"] = $row["Role"];

            // Debugging output
            // echo "<div>Login successful. Redirecting...</div>"; // Hapus untuk produksi

            // Redirect berdasarkan role
            if ($row["Role"] == 'admin') {
                header('Location: admin_dashboard.php');
            } else {
                header('Location: index.php');
            }
            exit();
        } else {
            echo "<script>alert('Login gagal. Password salah.');</script>";
        }
    } else {
        echo "<script>alert('Login gagal. Username tidak ditemukan.');</script>";
    }

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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
            color: #fff;
        }

        .container {
            background-color: #FFFFFF;
            border-radius: 20px;
            width: 800px;
            padding: 20px;
            display: flex;
            box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.1);
        }

        .left-section {
            background-color: #C0C78C;
            border-radius: 20px 0 0 20px;
            width: 50%;
            padding: 30px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: #FFFFFF;
            text-align: center;
        }

        .left-section h2 {
            font-size: 30px;
            margin: 20px 0;
        }

        .left-section p {
            font-size: 14px;
            margin-bottom: 10px;
        }

        .right-section {
            width: 50%;
            padding: 30px;
        }

        .right-section p {
            color: #C0C78C;
        }

        h3 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333333;
        }

        a {
            color: #FFFFFF;
            text-decoration: underline;
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
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            color: #333333;
        }

        input[type="text"]::placeholder,
        input[type="password"]::placeholder {
            color: #aaa;
        }

        input[type="submit"] {
            width: 100%;
            background-color: #C0C78C;
            color: #FFFFFF;
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
        }

        input[type="submit"]:hover {
            background-color: #A6B37D;
        }

        .password-container {
            position: relative;
        }

        .password-container input {
            padding-right: 40px; 
        }

        .password-container .toggle-password {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #3333;
        }

        .password-container .toggle-password .fas {
            font-size: 20px;
        }

        .remember-me,
        .forgot-password {
            font-size: 14px;
            display: inline-block; 
            margin-top: 10px;
        }

        .remember-me {
            float: left; 
        }

        .forgot-password {
            float: right; 
        }

        .remember-me label,
        .forgot-password a {
            color: #C0C78C; 
            text-decoration: none;
            font-weight: 600;
        }

        .social-login {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .social-login a {
            margin: 0 10px;
            font-size: 24px;
            color: #C0C78C; 
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left-section">
            <h2>Selamat datang di Galeri Kami</h2>
            <p>Belum punya akun? <a href="regist.php">Daftar sekarang</a></p>
        </div>
        <div class="right-section">
            <h3>Login</h3>
            <form action="" method="post">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required placeholder="Masukkan Username">

                <label for="password">Password:</label>
                <div class="password-container">
                    <input type="password" id="password" name="password" required placeholder="Masukkan Password">
                    <span class="toggle-password"><i class="fas fa-eye-slash"></i></span>
                </div>

                <div class="remember-me">
                    <label>
                        <input type="checkbox" name="remember"> Ingat saya
                    </label>
                </div>
                <div class="forgot-password">
                    <a href="forgot_password.php">Lupa kata sandi Anda?</a>
                </div>
                <input type="submit" value="Login">
            </form>
            
            <div class="social-login">
                <a href="https://www.google.com" target="_blank">
                    <i class="fab fa-google"></i>
                </a>
                <a href="https://www.facebook.com" target="_blank">
                    <i class="fab fa-facebook"></i>
                </a>
                <a href="https://twitter.com" target="_blank">
                    <i class="fab fa-twitter"></i>
                </a>
            </div>
        </div>
    </div>
    
    <script>
    $(document).ready(function() {
        // Fungsi untuk toggle password
        $('.toggle-password').click(function() {
            $(this).find('i').toggleClass('fa-eye fa-eye-slash');
            var input = $(this).siblings('input');
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
            } else {
                input.attr('type', 'password');
            }
        });
    });
    </script>
</body>
</html>