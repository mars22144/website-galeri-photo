<?php
session_start();
include 'config.php';

// Ambil ID foto dari URL
if (isset($_GET['id_foto'])) {
    $id_foto = $_GET['id_foto'];

    // Query untuk mengambil data foto berdasarkan ID
    $sql = "SELECT f.*, a.NamaAlbum FROM foto f LEFT JOIN album a ON f.AlbumID = a.AlbumID WHERE f.FotoID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_foto);
    $stmt->execute();
    $result = $stmt->get_result();

    // Jika data ditemukan
    if ($result->num_rows > 0) {
        $foto = $result->fetch_assoc();
    } else {
        echo "<script>
                alert('Foto tidak ditemukan.');
                window.location.href = 'index.php'; // Ganti dengan halaman yang sesuai
              </script>";
        exit;
    }

    $stmt->close();
} else {
    echo "<script>
            alert('ID foto tidak diberikan.');
            window.location.href = 'index.php'; // Ganti dengan halaman yang sesuai
          </script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Galeri Photo</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #fff;
        }

        .foto-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        .foto-container img {
            display: block;
            max-width: 100%;
            margin-bottom: 20px;
        }

        .foto-info {
            margin-bottom: 10px;
        }

        .foto-info strong {
            display: inline-block;
            width: 150px;
        }

        .foto-info span {
            color: #555;
        }

        @media print {
            body {
                background-color: white;
                color: black;
            }

            /* Sembunyikan elemen yang tidak ingin dicetak */
            .no-print {
                display: none;
            }
            
             /* Hapus URL file yang muncul di cetakan */
            @page {
                margin: 0;
            }

            /* Sembunyikan URL yang muncul di bagian bawah */
            a[href]:after {
                content: none !important;
            }

            h1 {
                margin-bottom: 20px;
            }

            .foto-container {
                padding: 0;
                border: none;
            }
        }
    </style>
</head>
<body>
    <div class="foto-container">
        <div class="foto-info">
            <span><?php echo htmlspecialchars($foto['JudulFoto']); ?></span>
        </div>
        <div class="foto-info">
            <span><?php echo htmlspecialchars($foto['DeskripsiFoto']); ?></span>
        </div>
        <div class="foto-info">
            <img src="<?php echo htmlspecialchars($foto['LokasiFile']); ?>" alt="<?php echo htmlspecialchars($foto['JudulFoto']); ?>">
        </div>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print();" style="padding: 10px 20px; background-color: #A6B37D; color: white; border: none; border-radius: 5px; cursor: pointer;">Cetak</button>
    </div>

</body>
</html>
