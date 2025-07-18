<?php
include_once 'config.php';

$tour = null;
if (isset($_GET['id'])) {
    $tour_id = $_GET['id'];
    try {
        // Mengambil semua kolom yang diperlukan
        $stmt = $pdo->prepare("SELECT id, tour_name, description, price, duration, image, location, itinerary, included, excluded FROM tours WHERE id = ?");
        $stmt->execute([$tour_id]);
        $tour = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in detail_tour.php: " . $e->getMessage());
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

// --- BARIS PENTING UNTUK GAMBAR (yang sebelumnya kita benerin) ---
$actual_image_src_detail = 'images/default_tour.jpg'; // Default jika tidak ada gambar
if ($tour && !empty($tour['image'])) {
    $image_filename = htmlspecialchars($tour['image']);
    $actual_image_src_detail = 'images/' . $image_filename; // Pastikan path ini benar!
}
// -----------------------------------------------------------------
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

                    <div class="terms-conditions-detail">
                        <h3>Syarat & Ketentuan Tour Ini</h3>
                        <p>Dengan melakukan pemesanan tour ini, Anda dianggap telah membaca dan menyetujui syarat & ketentuan berikut:</p>
                        <ul>
                            <li><strong>Kebijakan Pembayaran:</strong> Balance harus Lunas dibayarkan 45 hari sebelum keberangkatan.</li>
                            <li><strong>Pembatalan:</strong> Di dalam Perjalanan waktu; (dan telah terjadi pembayaran DP serta angsuran); apabila ada peserta yang berhalangan, maka semua uang yang telah dibayarkan <strong>Tidak hangus</strong>, NAMUN peserta yang berhalangan tersebut Wajib mencarikan Pengganti, SEBELUM Ticket pesawat Diterbitkan, dengan dikenakan biaya Admin sebesar 1 JUTA ROBUX dan hanya diperkenankan 1X Pergantian nama.</li>
                            <li>Apabila Peserta yang berhalangan tersebut tidak dapat mencari pengganti, maka semua uang yang telah dibayarkan hanya akan dikembalikan 10% dari jumlah total yang telah dibayarkan 60-90 Hari terhitung sejak tanggal Pulang.</li>
                            <li>Apabila Peserta berhalangan Setelah Penerbitan Tiket Pesawat; maka semua uang yang telah dibayarkan, akan Hangus.</li>
                            <li>Harga berpatokan pada 1 USD = Rp. 16,100</li>
                            <li>Apabila Harga Rupiah terus melemah diatas 16,100 per US dollarnya, maka Peserta Wajib membayar beda harga nilai tukar.</li>
                            <li>Harga untuk Peserta PESERTA per orang setiap keberangkatan / Periode Tanggal.</li>
                            <li>Apabila Peserta tidak mencapai 6 orang minimum; maka ikut keberangkatan Periode Minggu atau Bulan berikutnya.</li>
                        </ul>
                    </div>

                    <div class="payment-ticket-schedule">
                        <h3>Skedul Pembayaran & Penerbitan Tiket</h3>
                        <ul>
                            <li>Dp1: 50% dan nama beserta KTP harus di-infokan.</li>
                            <li>Balance bisa di-angsur sebanyak 3x, 30 Hari Setelah pembayaran dp1 dan dibayarkan per bulan.</li>
                            <li>Ticket Pesawat di terbitkan Setelah Pelunasan.</li>
                            <li>Series Periode Tour : Minggu Kedua dan Minggu Ke-empat setiap bulan-nya.</li>
                            <li>Blank Period : Christmas & New Year Period dan Periode Imlek.</li>
                        </ul>
                    </div>

                </div> <aside class="tour-action-sidebar">
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

    </div>

    <footer>
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