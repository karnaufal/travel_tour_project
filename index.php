<?php // Pastikan ini adalah BARIS PERTAMA di file

include_once 'config.php'; // Pastikan path ini benar untuk koneksi DB

// Ambil tur dari database untuk ditampilkan di Hero Section sebagai slider
// Mengambil 10 tur terbaru untuk ditampilkan. Lo bisa sesuaikan limitnya.
$hero_slider_limit = 10;
try {
    $stmt = $pdo->prepare("SELECT id, tour_name, description, price, duration, image FROM tours ORDER BY id DESC LIMIT ?");
    $stmt->execute([$hero_slider_limit]);
    $tours = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Log error agar bisa dicek di server logs
    error_log("Database error fetching tours for hero slider: " . $e->getMessage());
    // Fallback: Jika ada error DB, pastikan $tours adalah array kosong
    $tours = [];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JalanJalan Kuy! - Jelajahi Dunia, Rasakan Petualangan!</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/swiper-bundle.min.css">
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

    <section class="hero-swiper-section">
        <div class="swiper-container hero-main-slider">
            <div class="swiper-wrapper">
                <?php
                // Cek apakah ada data tur dari database
                if (!empty($tours)) {
                    foreach ($tours as $tour) {
                        // Pastikan nama file gambar di database cocok dengan file fisik di folder images/
                        // Contoh: tour_60721a733bb.jpeg
                ?>
                        <div class="swiper-slide hero-slide" style="background-image: url('images/<?php echo htmlspecialchars($tour['image']); ?>');">
                            <div class="hero-overlay"></div>
                            <div class="hero-content">
                                <h1><?php echo htmlspecialchars($tour['tour_name']); ?></h1>
                                <p><?php echo htmlspecialchars(substr($tour['description'], 0, 150)); ?>...</p>
                                <div class="tour-meta">
                                    <span class="price">Rp <?php echo number_format($tour['price'], 0, ',', '.'); ?></span>
                                    <span class="duration"><i class="fas fa-clock"></i> <?php echo htmlspecialchars($tour['duration']); ?></span>
                                </div>
                                <a href="detail_tour.php?id=<?php echo htmlspecialchars($tour['id']); ?>" class="btn btn-primary">Lihat Detail & Pesan</a>
                            </div>
                        </div>
                <?php
                    }
                } else {
                    // Fallback jika tidak ada tur di database atau query gagal
                    // Ini akan menampilkan slide default dengan gambar fallback
                    // Pastikan default-hero-bg.jpg ada di folder images/
                ?>
                    <div class="swiper-slide hero-slide" style="background-image: url('images/default-hero-bg.jpg');">
                        <div class="hero-overlay"></div>
                        <div class="hero-content">
                            <h1>Jelajahi Dunia, Rasakan Petualangan!</h1>
                            <p>Temukan petualangan impian Anda bersama kami!</p>
                            <a href="paket_tur.php" class="btn btn-primary">Lihat Paket Tur Kami</a>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
            <div class="swiper-pagination hero-pagination"></div>
            <div class="swiper-button-next hero-button-next"></div>
            <div class="swiper-button-prev hero-button-prev"></div>
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
    <script src="js/swiper-bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi Swiper untuk Hero Section
            const heroSwiper = new Swiper('.hero-main-slider', {
                loop: true, // Untuk continuous looping (butuh minimal 3 slide unik agar efeknya mulus)
                autoplay: { // Auto-slide
                    delay: 5000, // Durasi setiap slide (ms)
                    disableOnInteraction: false, // Jangan berhenti autoplay saat diinteraksi user
                },
                speed: 1000, // Kecepatan transisi slide (ms)
                effect: 'fade', // Efek transisi antar slide (bisa 'slide', 'cube', 'coverflow', dll.)
                fadeEffect: {
                    crossFade: true,
                },
                slidesPerView: 1, // Hanya 1 slide yang terlihat penuh pada satu waktu

                pagination: {
                    el: '.hero-pagination',
                    clickable: true,
                    // Opsional: Custom rendering untuk dot pagination
                    renderBullet: function (index, className) {
                        return '<span class="' + className + ' hero-bullet"></span>';
                    },
                },
                navigation: {
                    nextEl: '.hero-button-next',
                    prevEl: '.hero-button-prev',
                },
            });
        });
    </script>
</body>
</html>