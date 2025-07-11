<?php
session_start();
ob_start(); // Pastikan ob_start() ada jika kamu pakai header/redirect di tengah kode

include_once 'config.php'; // Pastikan path ke config.php benar

$tour = null; // Inisialisasi variabel tur
$error_message = ''; // Variabel untuk pesan error

// 1. Ambil ID Tur dari URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $tour_id = (int)$_GET['id']; // Pastikan ID adalah integer
} else {
    // Jika ID tidak ada atau tidak valid, tampilkan pesan error
    $error_message = "ID tur tidak valid atau tidak diberikan.";
}

if (empty($error_message)) { // Hanya jalankan query jika tidak ada masalah ID
    try {
        // 2. Query Database untuk Mengambil Detail Tur
        $stmt = $pdo->prepare("SELECT * FROM tours WHERE id = ?");
        $stmt->execute([$tour_id]);
        $tour = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$tour) {
            // Jika tur tidak ditemukan di database
            $error_message = "Tur tidak ditemukan. Mungkin ID tur salah atau tur sudah dihapus.";
        }
    } catch (PDOException $e) {
        // Tangani error database
        $error_message = "Error saat mengambil data tur: " . htmlspecialchars($e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $tour ? htmlspecialchars($tour['tour_name']) : 'Tur Tidak Ditemukan'; ?> - Travel Tour Gokil</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Style tambahan jika diperlukan */
        .message-container {
            margin-top: 20px;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }
        .message-container.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .message-container.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>
<body>
    <header>
        <h1>Detail Tur Kita! 🗺️</h1>
        <p>Selami lebih dalam petualangan impianmu!</p>
    </header>

    <main>
        <?php
        // Tampilkan pesan sukses dari pemesanan (jika ada)
        if (isset($_GET['status']) && $_GET['status'] == 'success') {
            $success_msg = htmlspecialchars($_GET['message'] ?? 'Pemesanan Anda berhasil!');
            ?>
            <div class="message-container success">
                <p>🎉 Pemesanan Berhasil! 🎉</p>
                <p><?php echo $success_msg; ?></p>
            </div>
            <?php
        }

        // Tampilkan pesan error jika tur tidak ditemukan atau ada masalah lain
        if (!empty($error_message)): ?>
            <div class="message-container error">
                <p>Oops! Tur tidak ditemukan. 😞</p>
                <p>Sepertinya kamu nyasar atau ID tur-nya gak valid. Yuk, kembali ke halaman <a href="index.php">Daftar Tur</a>.</p>
                <?php if (isset($e)): // Tampilkan detail error database hanya untuk debug (jangan di production) ?>
                    <p style="font-size: 0.8em; color: #a13;">Detail Error: <?php echo $error_message; ?></p>
                <?php endif; ?>
            </div>
        <?php elseif ($tour): // Jika tur ditemukan, tampilkan detail tur dan form ?>
            <section class="tour-detail-card">
                <img src="<?php echo htmlspecialchars($tour['image_url']); ?>" alt="<?php echo htmlspecialchars($tour['tour_name']); ?>" class="tour-image">
                <h2><?php echo htmlspecialchars($tour['tour_name']); ?></h2>
                <p>Harga: Rp <?php echo number_format($tour['price'], 0, ',', '.'); ?></p>
                <p>Durasi: <?php echo htmlspecialchars($tour['duration']); ?></p>
                <h3>Deskripsi:</h3>
                <p><?php echo nl2br(htmlspecialchars($tour['description'])); ?></p>

                <h3>Pesan Tur Ini!</h3>
                <form action="process_booking.php" method="POST" class="booking-form" onsubmit="return validateForm()">
                    <input type="hidden" name="tour_id" value="<?php echo htmlspecialchars($tour['id']); ?>">
                    <label for="customer_name">Nama Lengkap:</label>
                    <input type="text" id="customer_name" name="customer_name" required>

                    <label for="customer_email">Email:</label>
                    <input type="email" id="customer_email" name="customer_email" required>

                    <label for="num_participants">Jumlah Peserta:</label>
                    <input type="number" id="num_participants" name="num_participants" min="1" value="1" required>

                    <label for="booking_date">Tanggal Keberangkatan (YYYY-MM-DD):</label>
                    <input type="date" id="booking_date" name="booking_date" required>

                    <button type="submit" class="btn-confirm-booking">Konfirmasi Pemesanan</button>
                </form>
            </section>
        <?php endif; ?>
        
        <div class="back-to-list-container">
            <a href="index.php" class="btn-back-to-list">Kembali ke Daftar Tur</a>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Travel Tour Gokil. Dijamin anti-bosan!</p>
    </footer>

    <script>
        function validateForm() {
            let messages = [];
            const name = document.getElementById('customer_name').value;
            const email = document.getElementById('customer_email').value;
            const participants = document.getElementById('num_participants').value;
            const date = document.getElementById('booking_date').value;

            if (name.trim() === '') {
                messages.push('Nama Lengkap wajib diisi.');
            }
            if (email.trim() === '') {
                messages.push('Email wajib diisi.');
            } else if (!/\S+@\S+\.\S+/.test(email)) {
                messages.push('Format Email tidak valid.');
            }
            if (participants <= 0) {
                messages.push('Jumlah Peserta harus lebih dari 0.');
            }
            if (date.trim() === '') {
                messages.push('Tanggal keberangkatan wajib diisi.');
            }

            if (messages.length > 0) {
                alert('Mohon perbaiki kesalahan berikut:\n' + messages.join('\n'));
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
<?php
ob_end_flush();
?>