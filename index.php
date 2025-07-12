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
    <title>JalanJalan Kuy! - Jelajahi Dunia, Rasakan Petualangan!</title>
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
                    <li><a href="#paket-tur">Paket Tur</a></li>
                    <li><a href="#tentang-kami">Tentang Kami</a></li>
                    <li><a href="#kontak">Kontak</a></li>
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
            <a href="#paket-tur" class="btn btn-primary">Lihat Paket Tur Kami</a>
        </div>
    </section>

    <main class="container">
        <section id="paket-tur" class="tour-listing-section">
            <h2 class="section-title">Paket Tur Pilihan Kami</h2>
            <p class="section-subtitle">Temukan destinasi favoritmu, mulai petualangan tak terlupakan!</p>

            <div class="tour-cards-grid" id="tour-grid">
                <?php if (!empty($tours)): ?>
                    <?php foreach ($tours as $tour): ?>
                        <div class="tour-card">
                            <?php if (!empty($tour['image']) && file_exists('uploads/' . $tour['image'])): ?>
                                <img src="uploads/<?php echo htmlspecialchars($tour['image']); ?>" alt="<?php echo htmlspecialchars($tour['tour_name']); ?>">
                            <?php else: ?>
                                <img src="images/placeholder.jpg" alt="No Image Available"> <?php endif; ?>
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

            <?php if (count($tours) < $total_tours): // Tampilkan tombol "Load More" jika masih ada tur lain ?>
                <div class="load-more-container">
                    <button id="loadMoreBtn" class="btn btn-secondary">Muat Lebih Banyak Tur</button>
                </div>
            <?php endif; ?>

        </section>

        <section id="tentang-kami" class="about-section">
            <h2 class="section-title">Tentang Kami</h2>
            <p>JalanJalan Kuy! adalah solusi terbaik untuk petualangan Anda. Kami menawarkan berbagai paket tur menarik ke destinasi impian di seluruh dunia. Dengan pelayanan terbaik dan harga terjangkau, kami siap membuat liburan Anda tak terlupakan.</p>
            <p>Didirikan pada tahun 2025, kami berkomitmen untuk memberikan pengalaman travel yang aman, nyaman, dan penuh kegembiraan. Tim kami terdiri dari para ahli perjalanan yang berdedikasi untuk memenuhi setiap kebutuhan perjalanan Anda.</p>
        </section>

        <section id="kontak" class="contact-section">
            <h2 class="section-title">Kontak Kami</h2>
            <p>Punya pertanyaan atau ingin kustomisasi tur? Jangan ragu untuk menghubungi kami!</p>
            <div class="contact-info">
                <p><i class="fas fa-map-marker-alt"></i> Alamat: Jl. Contoh No. 123, Kota Bandung, Indonesia</p>
                <p><i class="fas fa-envelope"></i> Email: info@jalanjalankuy.com</p>
                <p><i class="fas fa-phone"></i> Telepon: +62 812-3456-7890</p>
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
    </footer>

    <script src="js/jquery-3.6.0.min.js"></script>
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
                        if (response && response.trim() !== '') { // Cek respons tidak kosong
                            $('#tour-grid').append(response); // Tambahkan tur baru ke grid
                            offset += limit; // Perbarui offset
                            $('#loadMoreBtn').text('Muat Lebih Banyak Tur');
                            $('#loadMoreBtn').prop('disabled', false);

                            // Sembunyikan tombol jika semua tur sudah dimuat
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

            // Smooth scroll untuk navigasi
            $('nav.main-nav a').on('click', function(event) {
                if (this.hash !== "") {
                    event.preventDefault();
                    var hash = this.hash;
                    // Offset scroll top dengan tinggi header
                    $('html, body').animate({
                        scrollTop: $(hash).offset().top - $('header.main-header').outerHeight() - 20 // Tambah sedikit padding
                    }, 800, function(){
                        // Tambahkan hash ke URL setelah selesai scroll (opsional)
                        // window.location.hash = hash;
                    });
                }
            });
        });
    </script>
</body>
</html>