<?php
include_once 'config.php';

// Inisialisasi variabel
$initial_limit = 6;
$tours = [];
$total_tours = 0;

try {
    // Query TANPA memilih kolom 'location'
    $stmt = $pdo->prepare("SELECT id, tour_name, description, price, duration, image FROM tours ORDER BY id DESC LIMIT ?");
    $stmt->execute([$initial_limit]);
    $tours = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Hitung total tur untuk fitur "Load More"
    $total_tours_stmt = $pdo->query("SELECT COUNT(*) FROM tours");
    $total_tours = $total_tours_stmt->fetchColumn();

} catch (PDOException $e) {
    error_log("Database Error in paket_tur.php: " . $e->getMessage());
    // Tetap tampilkan pesan error jika koneksi DB bermasalah
    // echo "<p style='color: red;'>Terjadi kesalahan database: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paket Tour - JalanJalan Kuy!</title>
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
                    <li><a href="paket_tur.php" class="active">Paket Tour</a></li>
                    <li><a href="tentang_kami.php">Tentang Kami</a></li>
                    <li><a href="kontak.php">Kontak</a></li>
                    <li><a href="admin/login.php" class="btn-login-admin">Login Admin</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <section id="paket-tur" class="tour-listing-section" style="padding-top: 120px;">
            <h2 class="section-title">Daftar Paket Tour Kami</h2>
            <p class="section-subtitle">Temukan destinasi favoritmu, mulai petualangan tak terlupakan!</p>

            <div class="tour-cards-grid" id="tour-grid">
                <?php if (!empty($tours)): ?>
                    <?php foreach ($tours as $tour): ?>
                        <div class="tour-card">
                            <?php
                            $image_name = $tour['image'] ?? '';
                            $image_path = 'images/' . $image_name;
                            $actual_image_src = (file_exists($image_path) && !empty($image_name)) ? htmlspecialchars($image_path) : 'images/placeholder.jpg';
                            $tour_name_alt = htmlspecialchars($tour['tour_name'] ?? 'Gambar Tur');
                            ?>
                            <img src="<?php echo $actual_image_src; ?>" alt="<?php echo $tour_name_alt; ?>">
                            <div class="card-content">
                                <h3><?php echo htmlspecialchars($tour['tour_name'] ?? 'Nama Tur Tidak Tersedia'); ?></h3>
                                <p class="duration"><i class="far fa-clock"></i> <?php echo htmlspecialchars($tour['duration'] ?? 'Durasi Tidak Tersedia'); ?></p>
                                <p class="price">Mulai dari IDR <?php echo number_format($tour['price'] ?? 0, 0, ',', '.'); ?></p>
                                <p class="description">
                                    <?php
                                    $description = $tour['description'] ?? '';
                                    echo htmlspecialchars(strlen($description) > 80 ? substr($description, 0, 80) . '...' : $description);
                                    ?>
                                </p>
                                <a href="detail_tour.php?id=<?php echo htmlspecialchars($tour['id'] ?? ''); ?>" class="btn btn-detail">Lihat Details</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-tour-message">Belum ada paket tour tersedia saat ini. Silakan tambahkan tour melalui halaman admin atau periksa koneksi database Anda.</p>
                <?php endif; ?>
            </div>

            <?php if (count($tours) < $total_tours): ?>
                <div class="load-more-container">
                    <button id="loadMoreBtn" class="btn btn-secondary">Muat Lebih Banyak Tour</button>
                </div>
            <?php endif; ?>

        </section>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> JalanJalan Kuy!. All rights reserved.</p>
        <p style="font-size: 0.8em; margin-top: 5px;">Dibuat Type-Spype</p>
    </footer>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            let offset = <?php echo $initial_limit; ?>;
            const limit = 6;
            const totalTours = <?php echo $total_tours; ?>;

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

            $('#loadMoreBtn').on('click', function() {
                $.ajax({
                    url: 'load_tours.php',
                    type: 'POST',
                    data: { offset: offset, limit: limit },
                    beforeSend: function() {
                        $('#loadMoreBtn').text('Memuat...');
                        $('#loadMoreBtn').prop('disabled', true);
                    },
                    success: function(response) {
                        if (response && response.trim() !== '') {
                            $('#tour-grid').append(response);
                            offset += limit;
                            $('#loadMoreBtn').text('Muat Lebih Banyak Tur');
                            $('#loadMoreBtn').prop('disabled', false);

                            if (offset >= totalTours) {
                                $('#loadMoreBtn').hide();
                            }
                        } else {
                            $('#loadMoreBtn').text('Tidak Ada Lagi Tur');
                            $('#loadMoreBtn').prop('disabled', true);
                            $('#loadMoreBtn').hide();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", status, error);
                        $('#loadMoreBtn').text('Gagal Memuat');
                        $('#loadMoreBtn').prop('disabled', false);
                    }
                });
            });
        });
    </script>
</body>
</html>