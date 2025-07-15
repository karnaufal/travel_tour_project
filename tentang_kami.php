<?php
include_once 'config.php'; // Pastikan path ini benar untuk koneksi DB
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami - JalanJalan Kuy!</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>

    <header class="main-header">
        <div class="container header-content">
            <div class="logo">
                <a href="index.php">JalanJalan Kuy!</a>
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="paket_tur.php">Paket Tour</a></li>
                    <li><a href="tentang_kami.php" class="active">Tentang Kami</a></li>
                    <li><a href="kontak.php">Kontak</a></li>
                    <!-- <li><a href="admin/login.php" class="btn-login-admin">Login Admin</a></li> -->
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <section id="tentang-kami" class="about-section" style="padding-top: 120px;"> <h2 class="section-title">Tentang Kami</h2>
            <p>JalanJalan Kuy! adalah solusi terbaik untuk petualangan Anda. Kami menawarkan berbagai paket tur menarik ke destinasi impian di seluruh dunia. Dengan pelayanan terbaik dan harga terjangkau, kami siap membuat liburan Anda tak terlupakan.</p>
            <p>Didirikan pada tahun 2025, kami berkomitmen untuk memberikan pengalaman travel yang aman, nyaman, dan penuh kegembiraan. Tim kami terdiri dari para ahli perjalanan yang berdedikasi untuk memenuhi setiap kebutuhan perjalanan Anda.</p>
        </section>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> JalanJalan Kuy!. All rights reserved.</p>
        <p style="font-size: 0.8em; margin-top: 5px;">Dibuat Karnaufal</p>
    </footer>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    </body>
</html>