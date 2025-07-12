<?php
include 'db_connect.php'; // Path ke koneksi database (sesuaikan jika kamu pakai config.php dengan PDO)

$tour = null;
if (isset($_GET['id'])) {
    $tour_id = intval($_GET['id']);
    $sql = "SELECT * FROM tours WHERE id = $tour_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $tour = $result->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Tur Kita! 🗺️</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Perbaikan CSS untuk gambar agar responsif */
        .tour-detail-card img {
            max-width: 100%; /* Gambar akan mengikuti lebar parent */
            height: auto; /* Tinggi akan menyesuaikan secara proporsional */
            display: block; /* Menghilangkan spasi ekstra di bawah gambar */
            border-radius: 8px; /* Sudut membulat */
            margin-bottom: 20px; /* Jarak bawah gambar */
        }
        .tour-detail-card {
            background-color: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: 30px auto;
            text-align: left;
        }
        .tour-detail-card h2 {
            color: #333;
            margin-bottom: 10px;
        }
        .tour-detail-card p {
            color: #555;
            line-height: 1.6;
            margin-bottom: 10px;
        }
        .tour-detail-card .price {
            font-size: 1.5em;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }
        .tour-detail-card .duration {
            font-style: italic;
            color: #777;
            margin-bottom: 20px;
        }
        .booking-form h3 {
            color: #333;
            margin-bottom: 20px;
        }
        .booking-form .form-group {
            margin-bottom: 15px;
        }
        .booking-form label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #444;
        }
        .booking-form input[type="text"],
        .booking-form input[type="email"],
        .booking-form input[type="number"],
        .booking-form input[type="date"] {
            width: calc(100% - 20px);
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1em;
            box-sizing: border-box;
        }
        .booking-form button {
            background-color: #28a745; /* Warna hijau */
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            font-size: 1.1em;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 20px;
        }
        .booking-form button:hover {
            background-color: #218838;
        }
        .status-message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 1em;
            text-align: center;
        }
        .status-message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .status-message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <header>
        <h1>Detail Tur Kita! 🗺️</h1>
        <p>Selami lebih dalam petualangan impianmu!</p>
    </header>

    <main>
        <?php if ($tour) : ?>
            <div class="tour-detail-card">
                <?php if (!empty($tour['image']) && file_exists('uploads/' . $tour['image'])): ?>
                    <img src="uploads/<?php echo htmlspecialchars($tour['image']); ?>" alt="<?php echo htmlspecialchars($tour['tour_name']); ?>">
                <?php else: ?>
                    <p style="text-align: center; color: #999;">Gambar tidak tersedia.</p>
                <?php endif; ?>

                <h2><?php echo htmlspecialchars($tour['tour_name']); ?></h2>
                <p class="price">Harga: Rp <?php echo number_format($tour['price'], 0, ',', '.'); ?></p>
                <p class="duration">Durasi: <?php echo htmlspecialchars($tour['duration']); ?></p>
                <h3>Deskripsi:</h3>
                <p><?php echo nl2br(htmlspecialchars($tour['description'])); ?></p>

                <hr>

                <div class="booking-form">
                    <h3>Pesan Tur Ini!</h3>
                    <?php
                    // Ambil pesan status dari URL jika ada
                    $message = '';
                    $status_class = '';
                    if (isset($_GET['status'])) {
                        if ($_GET['status'] == 'success') {
                            $message = htmlspecialchars($_GET['message'] ?? 'Pemesanan berhasil!');
                            $status_class = 'success';
                        } elseif ($_GET['status'] == 'error') {
                            $message = htmlspecialchars($_GET['message'] ?? 'Terjadi kesalahan saat memesan!');
                            $status_class = 'error';
                        }
                    }
                    if (!empty($message)) : ?>
                        <div class="status-message <?php echo $status_class; ?>">
                            <?php echo $message; ?>
                        </div>
                    <?php endif; ?>

                    <form action="process_booking.php" method="POST">
                        <input type="hidden" name="tour_id" value="<?php echo htmlspecialchars($tour['id']); ?>">
                        <div class="form-group">
                            <label for="customer_name">Nama Lengkap:</label>
                            <input type="text" id="customer_name" name="customer_name" required>
                        </div>
                        <div class="form-group">
                            <label for="customer_email">Email:</label>
                            <input type="email" id="customer_email" name="customer_email" required>
                        </div>
                        <div class="form-group">
                            <label for="num_participants">Jumlah Peserta:</label>
                            <input type="number" id="num_participants" name="num_participants" min="1" required>
                        </div>
                        <div class="form-group">
                            <label for="booking_date">Tanggal Keberangkatan (YYYY-MM-DD):</label>
                            <input type="date" id="booking_date" name="booking_date" required>
                        </div>
                        <button type="submit">Konfirmasi Pemesanan</button>
                    </form>
                </div>
            </div>
        <?php else : ?>
            <div class="status-message error">
                <h2>Oops! Tur tidak ditemukan. 😔</h2>
                <p>Sepertinya kamu nyasar atau ID tur-nya gak valid. Yuk, kembali ke halaman <a href="index.php">Daftar Tur</a>.</p>
            </div>
        <?php endif; ?>

        <p style="text-align: center; margin-top: 30px;">
            <a href="index.php" class="btn-back">Kembali Ke Daftar Tur</a>
        </p>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Travel Tour Gokil. Dijamin anti-bosan!</p>
    </footer>
</body>
</html>