<?php
include_once 'config.php'; // Pastikan path ini benar untuk koneksi DB

// Ambil beberapa tur awal untuk tampilan pertama (ini tidak lagi relevan untuk index.php saja, tapi biarkan dulu untuk sementara)
$initial_limit = 6;
try {
    $stmt = $pdo->prepare("SELECT id, tour_name, description, price, duration, image FROM tours ORDER BY id DESC LIMIT ?");
    $stmt->execute([$initial_limit]);
    $tours = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $total_tours_stmt = $pdo->query("SELECT COUNT(*) FROM tours");
    $total_tours = $total_tours_stmt->fetchColumn();

} catch (PDOException $e) {
    $tours = [];
    $total_tours = 0;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JalanJalan Kuy! - Jelajahi Dunia, Rasakan Petualangan!</title>
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
                    <li><a href="paket_tur.php">Paket Tur</a></li>
                    <li><a href="tentang_kami.php">Tentang Kami</a></li>
                    <li><a href="kontak.php">Kontak</a></li>
                    <li><a href="admin/login.php" class="btn-login-admin">Login Admin</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="hero-section">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1>Jelajahi Dunia, Rasakan Petualangan!</h1>
            <p>Temukan petualangan impian Anda bersama kami!</p>
            <a href="paket_tur.php" class="btn btn-primary">Lihat Paket Tur Kami</a>
        </div>
    </section>

    <section class="why-choose-us">
        <div class="container">
            <h2>Mengapa Memilih Kami?</h2>
            <p class="section-description">Kami menawarkan pengalaman wisata tak terlupakan dengan pemandu lokal berpengalaman, pilihan paket yang beragam, dan harga terbaik. Keamanan dan kenyamanan Anda adalah prioritas utama kami.</p>
            <div class="benefits-grid">
                <div class="benefit-card">
                    <div class="icon-circle time-icon"><i class="fas fa-clock"></i></div>
                    <h3>Pemandu Berpengalaman</h3>
                    <p>Jelajahi setiap sudut destinasi dengan pemandu lokal yang berpengalaman luas.</p>
                </div>
                <div class="benefit-card">
                    <div class="icon-circle check-icon"><i class="fas fa-check-circle"></i></div>
                    <h3>Pelayanan Prima</h3>
                    <p>Kepuasan Anda adalah prioritas kami. Nikmati layanan yang ramah dan responsif.</p>
                </div>
                <div class="benefit-card">
                    <div class="icon-circle package-icon"><i class="fas fa-box-open"></i></div>
                    <h3>Pilihan Paket Beragam</h3>
                    <p>Temukan paket tur yang sesuai dengan minat dan anggaran Anda.</p>
                </div>
            </div>
        </div>
    </section>
    <footer>
        <p>&copy; <?php echo date("Y"); ?> JalanJalan Kuy!. All rights reserved.</p>
        <p style="font-size: 0.8em; margin-top: 5px;">Dibuat Type-Spype</p>
    </footer>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            // Karena tidak ada lagi 'Load More' di index.php, bagian ini bisa dihapus atau dipindahkan ke paket_tur.php
            // let offset = <?php echo $initial_limit; ?>;
            // const limit = 6;
            // const totalTours = <?php echo $total_tours; ?>;
            // $('#loadMoreBtn').on('click', function() { /* ... */ });

            // Bagian smooth scroll juga bisa dihapus jika tidak ada lagi anchor internal di index.php
            // $('nav.main-nav a').on('click', function(event) { /* ... */ });
        });
    </script>
</body>
</html>