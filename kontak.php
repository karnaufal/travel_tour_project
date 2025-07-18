<?php
include_once 'config.php'; // Path ini tetap kita pertahankan ya, Bray!
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontak Kami - JalanJalan Kuy!</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
                    <li><a href="paket_tur.php">Paket Tour</a></li> <li><a href="tentang_kami.php">Tentang Kami</a></li>
                    <li><a href="kontak.php" class="active">Kontak</a></li>
                    <?php
                    // Pastikan session dimulai jika Anda menggunakan ini untuk login admin
                    if (session_status() == PHP_SESSION_NONE) {
                        session_start();
                    }
                    if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
                        <li><a href="admin/dashboard.php">Admin Panel</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container content">

        <section class="contact-hero-section">
            <div class="contact-hero-overlay"></div>
            <div class="container contact-hero-content">
                <h1>Hubungi Kami</h1>
                <p>Kami siap bantu kamu rencanain petualangan paling seru!</p>
            </div>
        </section>

        <section class="contact-info-section">
            <div class="container">
                <div class="section-title-wrapper">
                    <h2 class="section-title">Informasi Kontak</h2>
                    <p class="section-subtitle">Kami selalu ada buat kamu.</p>
                </div>
                <div class="info-grid">
                    <div class="info-card">
                        <div class="icon-wrapper"><i class="fas fa-map-marker-alt"></i></div>
                        <h3>Alamat Kantor</h3>
                        <p>Jl. Contoh No. 123,<br>Kota Bandung, Jawa Barat, Indonesia</p>
                    </div>
                    <div class="info-card">
                        <div class="icon-wrapper"><i class="fas fa-phone-alt"></i></div>
                        <h3>Telepon</h3>
                        <p>+62 812-3456-7890</p>
                    </div>
                    <div class="info-card">
                        <div class="icon-wrapper"><i class="fas fa-envelope"></i></div>
                        <h3>Email</h3>
                        <p>info@jalanjalankuy.com</p>
                    </div>
                     <div class="info-card">
                        <div class="icon-wrapper"><i class="fas fa-university"></i></div>
                        <h3>Kampus ITB Ganesha</h3>
                        <p>Jl. Ganesha No. 10,<br>Bandung, Jawa Barat, Indonesia</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="contact-form-section">
            <div class="container">
                <div class="section-title-wrapper">
                    <h2 class="section-title">Kirim Pesan Kepada Kami</h2>
                    <p class="section-subtitle">Kami bakal segera hubungi kamu balik.</p>
                </div>
                <form action="process_contact.php" method="POST" class="contact-form">
                    <div class="form-group">
                        <label for="name">Nama Lengkap:</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Kamu:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="subject">Subjek:</label>
                        <input type="text" id="subject" name="subject" required>
                    </div>
                    <div class="form-group">
                        <label for="message">Pesan Kamu:</label>
                        <textarea id="message" name="message" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn-submit">Kirim Pesan</button> </form>
            </div>
        </section>

        <section class="map-section">
            <div class="container">
                <div class="section-title-wrapper">
                    <h2 class="section-title">Lokasi Kami</h2>
                </div>
                <div class="map-container">
                    <iframe src="http://maps.google.com/maps?q=Institut%20Teknologi%20Bandung&t=&z=13&ie=UTF8&iwloc=&output=embed"
                            width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </section>

    </div> <footer>
        <p>© <?php echo date("Y"); ?> JalanJalan Kuy!. All rights reserved.</p> <p style="font-size: 0.8em; margin-top: 5px;">Dibuat Karnaufal</p>
    </footer>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        // Skrip buat nge-highlight navigasi aktif
        $(document).ready(function() {
            const currentPath = window.location.pathname.split('/').pop();
            $('nav.main-nav ul li a').each(function() {
                const linkPath = $(this).attr('href');
                // Tambahan ini biar 'Home' atau 'index.php' juga aktif kalau di halaman utama
                if (linkPath === currentPath || (currentPath === '' && (linkPath === 'index.php' || linkPath === 'home'))) {
                    $(this).addClass('active');
                }
            });
        });
    </script>
</body>
</html>