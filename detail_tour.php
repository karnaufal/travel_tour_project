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
                    ?>
                    <div class="tour-detail-card">
                        <img src="<?php echo htmlspecialchars($tour['image_url']); ?>" alt="<?php echo htmlspecialchars($tour['tour_name']); ?>" class="detail-img">
                        <h2><?php echo htmlspecialchars($tour['tour_name']); ?></h2>
                        <p class="detail-price">Harga: Rp <?php echo number_format($tour['price'], 0, ',', '.'); ?></p>
                        <p class="detail-duration">Durasi: <?php echo htmlspecialchars($tour['duration']); ?></p>
                        <h3>Deskripsi Lengkap:</h3>
                        <p class="detail-description"><?php echo nl2br(htmlspecialchars($tour['description'])); ?></p>
                    </div>

                    <section class="booking-section">
                        <h2>Pesan Tur Ini!</h2>
                        <form action="process_booking.php" method="POST" class="booking-form">
                            <input type="hidden" name="tour_id" value="<?php echo htmlspecialchars($tour['id']); ?>">
                            <input type="hidden" name="tour_name" value="<?php echo htmlspecialchars($tour['tour_name']); ?>">

                            <div class="form-group">
                                <label for="customer_name">Nama Lengkap:</label>
                                <input type="text" id="customer_name" name="customer_name" 
                            </div>
                            <div class="form-group">
                                <label for="customer_email">Email:</label>
                                <input type="email" id="customer_email" name="customer_email" 
                            </div>
                            <div class="form-group">
                                <label for="num_participants">Jumlah Peserta:</label>
                                <input type="number" id="num_participants" name="num_participants" min="1" value="1" 
                            </div>
                            <div class="form-group">
                                <label for="booking_date">Tanggal Keberangkatan (Contoh: DD/MM/YYYY):</label>
                                <input type="date" id="booking_date" name="booking_date" 
                            </div>
                            <button type="submit" class="btn-submit">Konfirmasi Pemesanan</button>
                        </form>
                    </section>
                    <?php
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
        <a href="index.php" class="btn-back-list">Kembali ke Daftar Tur</a>
    </main>
</main>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const bookingForm = document.querySelector('.booking-form'); // Ambil form pemesanan

            if (bookingForm) { // Pastikan formnya ada
                bookingForm.addEventListener('submit', function(event) {
                    // Ambil elemen input
                    const customerNameInput = document.getElementById('customer_name');
                    const customerEmailInput = document.getElementById('customer_email');
                    const numParticipantsInput = document.getElementById('num_participants');
                    const bookingDateInput = document.getElementById('booking_date');

                    // Ambil nilai input
                    const customerName = customerNameInput.value.trim();
                    const customerEmail = customerEmailInput.value.trim();
                    const numParticipants = parseInt(numParticipantsInput.value);
                    const bookingDate = bookingDateInput.value; // Format YYYY-MM-DD dari input type="date"

                    let isValid = true;
                    let messages = []; // Untuk mengumpulkan pesan error

                    // Reset styling error sebelumnya
                    customerNameInput.classList.remove('input-error');
                    customerEmailInput.classList.remove('input-error');
                    numParticipantsInput.classList.remove('input-error');
                    bookingDateInput.classList.remove('input-error');

                    // Validasi Nama Lengkap
                    if (customerName === '') {
                        messages.push('Nama Lengkap wajib diisi.');
                        customerNameInput.classList.add('input-error');
                        isValid = false;
                    }

                    // Validasi Email
                    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (customerEmail === '') {
                        messages.push('Email wajib diisi.');
                        customerEmailInput.classList.add('input-error');
                        isValid = false;
                    } else if (!emailPattern.test(customerEmail)) {
                        messages.push('Format email tidak valid.');
                        customerEmailInput.classList.add('input-error');
                        isValid = false;
                    }

                    // Validasi Jumlah Peserta
                    if (isNaN(numParticipants) || numParticipants <= 0) {
                        messages.push('Jumlah peserta harus angka positif.');
                        numParticipantsInput.classList.add('input-error');
                        isValid = false;
                    }

                    // Validasi Tanggal Keberangkatan
                    if (bookingDate === '') {
                        messages.push('Tanggal keberangkatan wajib diisi.');
                        bookingDateInput.classList.add('input-error');
                        isValid = false;
                    } else {
                        // Opsional: Validasi tanggal tidak boleh di masa lalu
                        const today = new Date();
                        today.setHours(0,0,0,0); // Set to start of today
                        const selectedDate = new Date(bookingDate); // date from input is YYYY-MM-DD
                        selectedDate.setHours(0,0,0,0);

                        if (selectedDate < today) {
                            messages.push('Tanggal keberangkatan tidak boleh di masa lalu.');
                            bookingDateInput.classList.add('input-error');
                            isValid = false;
                        }
                    }

                    // Jika tidak valid, cegah pengiriman form dan tampilkan pesan
                    if (!isValid) {
                        event.preventDefault(); // Mencegah form terkirim
                        alert('Mohon perbaiki kesalahan berikut:\n\n' + messages.join('\n'));
                        // Atau bisa tampilkan pesan error di dalam div khusus di HTML
                        // Contoh: tampilkanError(messages.join('<br>'));
                    }
                });
            }
        });
    </script>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Travel Tour Gokil. Dijamin anti-bosan!</p>
    </footer>
</body>
</html>
    <footer>
        <p>&copy; <?php echo date("Y"); ?> Travel Tour Gokil. Dijamin anti-bosan!</p>
    </footer>
</body>
</html>