<?php
include_once 'config.php'; // Pastikan path ini benar untuk koneksi DB

// Ambil beberapa tur awal untuk tampilan pertama
$initial_limit = 6; // Misalnya, tampilkan 6 tur pertama
try {
    $stmt = $pdo->prepare("SELECT id, tour_name, description, price, duration, image FROM tours ORDER BY id DESC LIMIT ?");
    $stmt->execute([$initial_limit]);
    $tours = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Hitung total tur untuk menentukan apakah tombol "Load More" perlu ditampilkan
    $total_tours_stmt = $pdo->query("SELECT COUNT(*) FROM tours");
    $total_tours = $total_tours_stmt->fetchColumn();

} catch (PDOException $e) {
    // Tangani error database jika perlu
    $tours = [];
    $total_tours = 0;
    // echo "Error: " . $e->getMessage(); // Untuk debugging, hapus di produksi
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paket Tur - JalanJalan Kuy!</title>
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
                    <li><a href="paket_tur.php" class="active">Paket Tur</a></li>
                    <li><a href="tentang_kami.php">Tentang Kami</a></li>
                    <li><a href="kontak.php">Kontak</a></li>
                    <li><a href="admin/login.php" class="btn-login-admin">Login Admin</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <section id="paket-tur" class="tour-listing-section" style="padding-top: 120px;"> <h2 class="section-title">Daftar Paket Tur Kami</h2>
            <p class="section-subtitle">Temukan destinasi favoritmu, mulai petualangan tak terlupakan!</p>

            <div class="tour-cards-grid" id="tour-grid">
                <?php if (!empty($tours)): ?>
                    <?php foreach ($tours as $tour): ?>
                        <div class="tour-card">
                            <?php if (!empty($tour['image']) && file_exists('uploads/' . $tour['image'])): ?>
                                <img src="uploads/<?php echo htmlspecialchars($tour['image']); ?>" alt="<?php echo htmlspecialchars($tour['tour_name']); ?>">
                            <?php else: ?>
                                <img src="images/placeholder.jpg" alt="No Image Available">
                            <?php endif; ?>
                            <div class="card-content">
                                <h3><?php echo htmlspecialchars($tour['tour_name']); ?></h3>
                                <p class="duration"><i class="far fa-clock"></i> <?php echo htmlspecialchars($tour['duration']); ?></p>
                                <p class="price">Harga: Rp <?php echo number_format($tour['price'], 0, ',', '.'); ?></p>
                                <p class="description"><?php echo htmlspecialchars(substr($tour['description'], 0, 80)); ?>...</p>
                                <a href="detail_tour.php?id=<?php echo htmlspecialchars($tour['id']); ?>" class="btn btn-detail">Lihat Detail & Pesan</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-tour-message">Belum ada paket tur tersedia saat ini. Segera hadir!</p>
                <?php endif; ?>
            </div>

            <?php if (count($tours) < $total_tours): ?>
                <div class="load-more-container">
                    <button id="loadMoreBtn" class="btn btn-secondary">Muat Lebih Banyak Tur</button>
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
            let offset = <?php echo $initial_limit; ?>; // Offset awal
            const limit = 6; // Jumlah tur yang dimuat setiap kali klik "Load More"
            const totalTours = <?php echo $total_tours; ?>;

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
                        }
                    },
                    error: function() {
                        $('#loadMoreBtn').text('Gagal Memuat');
                        $('#loadMoreBtn').prop('disabled', false);
                    }
                });
            });
        });
    </script>
</body>
</html>