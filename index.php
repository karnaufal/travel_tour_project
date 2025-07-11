<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Tour Gokil - Temukan Petualanganmu!</title>
    <link rel="stylesheet" href="css/style.css"> </head>
<body>
    <header>
        <h1>Jelajahi Dunia Bareng Travel Tour Gokil! 🚀</h1>
        <p>Temukan paket liburan impianmu, anti-galau, anti-dompet nangis!</p>
    </header>

    <main>
        <h2>Daftar Tur Asyik Kita:</h2>
        <div class="tour-list">
            <?php
            // Panggil file konfigurasi koneksi database kita
            include 'config.php'; // Ini dia file config.php yang tadi kita buat!

            try {
                // Ambil semua data tur dari tabel 'tours'
                $stmt = $pdo->query("SELECT * FROM tours");
                $tours = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Cek, ada tur gak di database?
                if (count($tours) > 0) {
                    // Kalau ada, kita tampilkan satu per satu
                    foreach ($tours as $tour) {
                        echo '<div class="tour-card">';
                        // Penting: Pastikan path gambar benar! Contoh: images/nama_gambar.jpg
                        echo '<img src="' . htmlspecialchars($tour['image_url']) . '" alt="' . htmlspecialchars($tour['tour_name']) . '">';
                        echo '<h3>' . htmlspecialchars($tour['tour_name']) . '</h3>';
                        // Hanya tampil 100 karakter pertama deskripsi
                        echo '<p class="tour-description-short">' . htmlspecialchars(substr($tour['description'], 0, 100)) . (strlen($tour['description']) > 100 ? '...' : '') . '</p>';                        echo '<p class="price">Harga: Rp ' . number_format($tour['price'], 0, ',', '.') . '</p>';
                        echo '<p class="duration">Durasi: ' . htmlspecialchars($tour['duration']) . '</p>';
                        // Link ke halaman detail tur, kirim ID tur-nya biar bisa dicari
                        echo '<a href="detail_tour.php?id=' . htmlspecialchars($tour['id']) . '" class="btn-detail">Lihat Detail & Pesan</a>';
                        echo '</div>';
                    }
                } else {
                    // Kalau database masih kosong melompong
                    echo '<p>Waduh, belum ada tur yang tersedia nih! Kayaknya admin lagi sibuk liburan. Coba lagi nanti ya!</p>';
                }
            } catch (PDOException $e) {
                // Kalau ada error saat query, tampilkan pesan kece
                echo '<p>Ups, ada masalah saat mengambil data tur: ' . $e->getMessage() . '</p>';
            }
            ?>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Travel Tour Gokil. Dijamin anti-bosan!</p>
    </footer>
</body>
</html>
