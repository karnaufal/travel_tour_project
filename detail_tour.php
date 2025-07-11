<?php
include 'config.php'; // Panggil file koneksi database kita

// Jangan tampilkan error_reporting di produksi
// error_reporting(E_ALL); // Aktifkan semua laporan error
// ini_set('display_errors', 1); // Tampilkan error di browser

?>
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
        // >>>>>>>>> KODE UNTUK MENAMPILKAN PESAN STATUS (DARI process_booking.php) <<<<<<<<<
        if (isset($_GET['status'])) {
            echo '<div class="message-container">';
            if ($_GET['status'] == 'success') {
                $customer_name = htmlspecialchars($_GET['customer_name'] ?? 'Anda');
                $tour_name_booked = htmlspecialchars($_GET['tour_name'] ?? 'tur ini');
                echo '<div class="success-message">';
                echo '<h2>🎉 Pemesanan Berhasil! 🎉</h2>';
                echo '<p>Terima kasih, <strong>' . $customer_name . '</strong>! Pesanan untuk tur <strong>' . $tour_name_booked . '</strong> telah berhasil kami terima. Kami akan segera menghubungi Anda melalui email.</p>';
                echo '</div>';
            } elseif ($_GET['status'] == 'error' || $_GET['status'] == 'error_validation') {
                $errorMessage = htmlspecialchars($_GET['message'] ?? 'Terjadi kesalahan tidak dikenal saat memproses pesanan Anda.');
                echo '<div class="error-message">';
                echo '<h2>Ops! Terjadi Masalah Saat Memesan. 😔</h2>';
                echo '<p>' . nl2br($errorMessage) . '</p>'; // nl2br agar <br> di pesan error_validation tampil
                echo '</div>';
            }
            echo '</div>';
        }
        // >>>>>>>>> AKHIR KODE UNTUK MENAMPILKAN PESAN STATUS <<<<<<<<<

        // Cek dulu, ada gak parameter 'id' di URL?
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            echo '<div class="error-message">';
            echo '<h2>Oops! Tur tidak ditemukan. 😞</h2>';
            echo '<p>Sepertinya kamu nyasar atau ID tur-nya gak valid. Yuk, kembali ke halaman <a href="index.php">Daftar Tur</a>.</p>';
            echo '</div>';
        } else {
            // Ambil ID tur dari URL, dan pastikan itu angka (biar aman)
            $tour_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

            try {
                $stmt = $pdo->prepare("SELECT * FROM tours WHERE id = :id");
                $stmt->bindParam(':id', $tour_id, PDO::PARAM_INT);
                $stmt->execute();
                $tour = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($tour) {
                    echo '<div class="tour-detail-card">';
                    echo '<img src="' . htmlspecialchars($tour['image_url']) . '" alt="' . htmlspecialchars($tour['tour_name']) . '" class="detail-img">';
                    echo '<h2>' . htmlspecialchars($tour['tour_name']) . '</h2>';
                    echo '<p class="detail-price">Harga: Rp ' . number_format($tour['price'], 0, ',', '.') . '</p>';
                    echo '<p class="detail-duration">Durasi: ' . htmlspecialchars($tour['duration']) . '</p>';
                    echo '<h3>Deskripsi Lengkap:</h3>';
                    echo '<p class="detail-description">' . nl2br(htmlspecialchars($tour['description'])) . '</p>';
                    echo '<a href="index.php" class="btn-back">⬅️ Kembali ke Daftar Tur</a>';
                    echo '</div>';

                    // >>>>>>>>>>>>> KODE FORM PEMESANAN <<<<<<<<<<<<<
        ?>
                    <div class="booking-form-container">
                        <h2>Pesan Tur Ini Sekarang!</h2>
                        <form action="process_booking.php" method="POST" class="booking-form">
                            <input type="hidden" name="tour_id" value="<?php echo htmlspecialchars($tour['id']); ?>">
                            <input type="hidden" name="tour_name" value="<?php echo htmlspecialchars($tour['tour_name']); ?>">

                            <div class="form-group">
                                <label for="name">Nama Lengkap:</label>
                                <input type="text" id="name" name="customer_name" required>
                            </div>

                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="email" id="email" name="customer_email" required>
                            </div>

                            <div class="form-group">
                                <label for="participants">Jumlah Peserta:</label>
                                <input type="number" id="participants" name="num_participants" min="1" value="1" required>
                            </div>

                            <button type="submit" class="btn-submit-booking">Konfirmasi Pemesanan</button>
                        </form>
                    </div>
        <?php
                    // >>>>>>>>>>>>> AKHIR KODE FORM <<<<<<<<<<<<<

                } else {
                    echo '<div class="error-message">';
                    echo '<h2>Tur yang kamu cari tidak ditemukan. 😔</h2>';
                    echo '<p>Mungkin tur ini sudah tidak tersedia atau ID-nya salah. Yuk, kembali ke halaman <a href="index.php">Daftar Tur</a>.</p>';
                    echo '</div>';
                }
            } catch (PDOException $e) {
                echo '<div class="error-message">';
                echo '<h2>Terjadi Kesalahan Teknis! 🚨</h2>';
                echo '<p>Mohon maaf, ada masalah saat mengambil data tur: ' . htmlspecialchars($e->getMessage()) . '</p>';
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