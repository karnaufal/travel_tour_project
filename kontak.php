<?php
include_once 'config.php'; // Pastikan path ini benar untuk koneksi DB
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontak Kami - JalanJalan Kuy!</title>
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
                    <li><a href="tentang_kami.php">Tentang Kami</a></li>
                    <li><a href="kontak.php" class="active">Kontak</a></li>
                    <li><a href="admin/login.php" class="btn-login-admin">Login Admin</a></li>
                </ul>
            </nav>
        </div>
    </header>
    
    <section id="kontak" class="contact-section" style="padding-top: 120px;">
    <h2 class="section-title">Kontak Kami</h2>
    <p>Punya pertanyaan atau ingin kustomisasi tour? Jangan ragu untuk menghubungi kami!</p>
    <div class="contact-info">
        <p><i class="fas fa-map-marker-alt"></i> Alamat: Jl. Contoh No. 123, Kota Bandung, Indonesia</p>
        <p><i class="fas fa-envelope"></i> Email: info@jalanjalankuy.com</p>
        <p><i class="fas fa-phone"></i> Telepon: +62 812-3456-7890</p>
        <p><i class="fas fa-university"></i> Kampus ITB Ganesha: Jl. Ganesha No. 10, Bandung</p>
    </div>
    <div class="map-container">
        <h3 class="map-title">Lokasi Kami</h3>
        <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.916788880652!2d107.60724737500001!3d-6.890903393108605!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68e64c525f3851%3A0x1d4791e3e7f67858!2sInstitut%20Teknologi%20Bandung!5e0!3m2!1sid!2sid!4v1700000000000!5m2!1sid!2sid"
            width="100%"
            height="450"
            style="border:0;"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade">
        </iframe>
    </div>

    <form action="#" method="POST" class="contact-form">
        <div class="form-group">
            <label for="name">Nama:</label>
            <input type="text" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="message">Pesan:</label>
            <textarea id="message" name="message" rows="5" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Kirim Pesan</button>
    </form>
</section>

    </main>
    <footer>
        <p>&copy; <?php echo date("Y"); ?> JalanJalan Kuy!. All rights reserved.</p>
        <p style="font-size: 0.8em; margin-top: 5px;">Dibuat Karnaufal</p>
    </footer>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    </body>
</html>