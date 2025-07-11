<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Tur - Travel Tour Gokil!</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Detail Tur Kita! 🗺️</h1>
        <p>Selami lebih dalam petualangan impianmu!</p>
    </header>

    <main>
        <?php
        // Panggil file koneksi database kita
        include 'config.php';

        // Cek dulu, ada gak parameter 'id' di URL?
        // Kalau gak ada, berarti ada yang nyasar atau iseng nih!
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            echo '<div class="error-message">';
            echo '<h2>Oops! Tur tidak ditemukan. 😞</h2>';
            echo '<p>Sepertinya kamu nyasar atau ID tur-nya gak valid. Yuk, kembali ke halaman <a href="index.php">Daftar Tur</a>.</p>';
            echo '</div>';
        } else {
            // Ambil ID tur dari URL, dan pastikan itu angka (biar aman dari hacker iseng!)
            $tour_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT); // Bersihin dari karakter aneh

            try {
                // Siapin query untuk ngambil data satu tur berdasarkan ID
                // PAKAI PREPARED STATEMENT! Ini penting banget buat keamanan (SQL Injection)
                $stmt = $pdo->prepare("SELECT * FROM tours WHERE id = :id");
                $stmt->bindParam(':id', $tour_id, PDO::PARAM_INT); // Ikat parameter ID sebagai integer
                $stmt->execute(); // Jalankan query-nya

                $tour = $stmt->fetch(PDO::FETCH_ASSOC); // Ambil satu baris data tur

                // Cek, tur dengan ID itu ada gak di database?
                if ($tour) {
                    // Kalau ada, tampilkan detailnya dengan bangga!
                    echo '<div class="tour-detail-card">';
                    echo '<img src="' . htmlspecialchars($tour['image_url']) . '" alt="' . htmlspecialchars($tour['tour_name']) . '" class="detail-img">';
                    echo '<h2>' . htmlspecialchars($tour['tour_name']) . '</h2>';
                    echo '<p class="detail-price">Harga: Rp ' . number_format($tour['price'], 0, ',', '.') . '</p>';
                    echo '<p class="detail-duration">Durasi: ' . htmlspecialchars($tour['duration']) . '</p>';
                    echo '<h3>Deskripsi Lengkap:</h3>';
                    echo '<p class="detail-description">' . nl2br(htmlspecialchars($tour['description'])) . '</p>'; // nl2br buat baris baru di deskripsi
                    echo '<a href="index.php" class="btn-back">⬅️ Kembali ke Daftar Tur</a>';
                    echo '</div>';
                } else {
                    // Kalau ID-nya ada tapi tur-nya gak ketemu (mungkin udah dihapus?)
                    echo '<div class="error-message">';
                    echo '<h2>Tur yang kamu cari tidak ditemukan. 😔</h2>';
                    echo '<p>Mungkin tur ini sudah tidak tersedia atau ID-nya salah. Yuk, kembali ke halaman <a href="index.php">Daftar Tur</a>.</p>';
                    echo '</div>';
                }
            } catch (PDOException $e) {
                // Kalau ada error saat query database
                echo '<div class="error-message">';
                echo '<h2>Terjadi Kesalahan Teknis! 🚨</h2>';
                echo '<p>Mohon maaf, ada masalah saat mengambil data tur: ' . $e->getMessage() . '</p>';
                echo '</div>';
            }
        }
        ?>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Travel Tour Gokil. Dijamin anti-bosan!</p>
    </footer>
</body>
</html>