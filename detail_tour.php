<?php
include_once 'config.php';

$tour = null;
if (isset($_GET['id'])) {
    $tour_id = $_GET['id'];
    try {
        // Mengambil semua kolom yang diperlukan, termasuk yang baru ditambahkan
        $stmt = $pdo->prepare("SELECT id, tour_name, description, price, duration, image, location, itinerary, included, excluded FROM tours WHERE id = ?");
        $stmt->execute([$tour_id]);
        $tour = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in detail_tour.php: " . $e->getMessage());
    }
    // Bagian ini yang krusial untuk menentukan path gambar
    $actual_image_src_detail = 'images/default_tour.jpg'; // Default jika tidak ada gambar atau data kosong
if ($tour && !empty($tour['image'])) {
    $image_filename = htmlspecialchars($tour['image']);
    // Ini adalah path yang harus benar! Pastikan 'images/' adalah path relatif yang tepat.
    $actual_image_src_detail = 'images/' . $image_filename;
}
}

if (!$tour) {
    // HTML untuk halaman "Tur Tidak Ditemukan"
    echo "<!DOCTYPE html><html lang='id'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width, initial-scale=1.0'><title>Tur Tidak Ditemukan</title><link rel='stylesheet' href='css/style.css'></head><body>";
    echo "<header class='main-header'><div class='container header-content'><div class='logo'><a href='index.php'>JalanJalan Kuy!</a></div><nav class='main-nav'><ul><li><a href='index.php'>Home</a></li><li><a href='paket_tur.php' class='active'>Paket Tour</a></li><li><a href='tentang_kami.php'>Tentang Kami</a></li><li><a href='kontak.php'>Kontak</a></li></ul></nav></div></header>";
    echo "<section class='section-common' style='padding-top: 100px;'>";
    echo "<div class='container'><h1 style='text-align: center; color: var(--primary-color);'>Tur Tidak Ditemukan</h1><p style='text-align: center;'>Maaf, tur yang Anda cari tidak tersedia.</p><p style='text-align: center;'><a href='paket_tur.php' class='btn-primary'>Kembali ke Daftar Tur</a></p></div>";
    echo "</section>";
    echo "</body></html>";
    exit();
}

// Pastikan session dimulai untuk header
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($tour['tour_name']); ?> - Detail Tour</title>
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
                    <li><a href="paket_tur.php" class="active">Paket Tour</a></li>
                    <li><a href="tentang_kami.php">Tentang Kami</a></li>
                    <li><a href="kontak.php">Kontak</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container content">

        <?php
        // Menampilkan pesan status dari redirect (misalnya setelah booking berhasil)
        if (isset($_GET['status']) && isset($_GET['msg'])) {
            $status_class = ($_GET['status'] == 'success') ? 'success-message' : 'error-message';
            $message_text = htmlspecialchars(urldecode($_GET['msg'])); // Tambahkan urldecode
            echo '<div class="message-container ' . $status_class . '">' . $message_text . '</div>';
        }
        ?>

       <section class="tour-detail-hero">
            <div class="hero-image-container">
                <img src="<?php echo $actual_image_src_detail; ?>" alt="<?php echo htmlspecialchars($tour['tour_name']); ?>" class="tour-image-main">
                <div class="tour-info-overlay">
                <h1><?php echo htmlspecialchars($tour['tour_name']); ?></h1>
            <div class="tour-meta">
                <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($tour['location']); ?></span>
                <span><i class="fas fa-clock"></i> <?php echo htmlspecialchars($tour['duration']); ?></span>
            </div>
                <p class="short-description"><?php echo nl2br(htmlspecialchars($tour['description'])); ?></p>
            </div>
        </div>
    </section>
        <section class="tour-details-section">
            <div class="detail-content-wrapper">
                <div class="main-detail-info">
                    <?php if (!empty($tour['itinerary'])): ?>
                        <h3>Tour Itinerary</h3>
                        <div class="itinerary-list">
                            <?php
                            $itinerary_lines = explode("\n", $tour['itinerary']);
                            foreach ($itinerary_lines as $line) {
                                $line = trim($line);
                                if (!empty($line)) {
                                    // Cek apakah baris dimulai dengan "Day X:" atau serupa untuk styling khusus
                                    if (preg_match('/^Hari \d+:/i', $line) || preg_match('/^Day \d+:/i', $line)) {
                                        echo "<p class='itinerary-day'>" . htmlspecialchars($line) . "</p>";
                                    } else {
                                        echo "<p class='itinerary-item'>" . htmlspecialchars($line) . "</p>";
                                    }
                                }
                            }
                            ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($tour['included'])): ?>
                        <h3>Termasuk</h3>
                        <ul class="included-list">
                            <?php
                            $included_items = explode("\n", $tour['included']);
                            foreach ($included_items as $item) {
                                if (!empty(trim($item))) {
                                    echo "<li><i class='fas fa-check-circle'></i> " . htmlspecialchars(trim($item)) . "</li>";
                                }
                            }
                            ?>
                        </ul>
                    <?php endif; ?>

                    <?php if (!empty($tour['excluded'])): ?>
                        <h3>Tidak Termasuk</h3>
                        <ul class="excluded-list">
                            <?php
                            $excluded_items = explode("\n", $tour['excluded']);
                            foreach ($excluded_items as $item) {
                                if (!empty(trim($item))) {
                                    echo "<li><i class='fas fa-times-circle'></i> " . htmlspecialchars(trim($item)) . "</li>";
                                }
                            }
                            ?>
                        </ul>
                    <?php endif; ?>
                </div>

                <aside class="tour-action-sidebar">
                    <div class="price-box">
                        Harga Mulai Dari:
                        <span class="price-amount">Rp <?php echo number_format($tour['price'], 0, ',', '.'); ?></span>
                        <span class="per-person">/ orang</span>
                    </div>
                    <a href="booking_form.php?tour_id=<?php echo htmlspecialchars($tour['id']); ?>" class="btn-primary btn-book-now">Pesan Sekarang</a>
                    <p class="sidebar-info">Punya pertanyaan lebih lanjut? Hubungi kami!</p>
                    <a href="kontak.php" class="btn-secondary btn-contact">Hubungi Kami</a>
                </aside>
            </div>
        </section>

    </div> <footer>
        <p>&copy; <?php echo date("Y"); ?> JalanJalan Kuy!. All rights reserved.</p>
        <p style="font-size: 0.8em; margin-top: 5px;">Dibuat Karnaufal</p>
    </footer>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        // Skrip untuk highlight navigasi aktif
        $(document).ready(function() {
            const currentPath = window.location.pathname.split('/').pop();
            $('nav.main-nav ul li a').removeClass('active');
            if (currentPath === '' || currentPath === 'index.php') {
                $('nav.main-nav ul li a[href="index.php"]').addClass('active');
            } else if (currentPath === 'paket_tur.php' || currentPath.startsWith('detail_tour.php')) {
                $('nav.main-nav ul li a[href="paket_tur.php"]').addClass('active');
            } else if (currentPath === 'tentang_kami.php') {
                $('nav.main-nav ul li a[href="tentang_kami.php"]').addClass('active');
            } else if (currentPath === 'kontak.php') {
                $('nav.main-nav ul li a[href="kontak.php"]').addClass('active');
            }
        });
    </script>
</body>
</html>