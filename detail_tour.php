<?php
include_once 'config.php'; // Koneksi database PDO

$tour = null;
if (isset($_GET['id'])) {
    $tour_id = intval($_GET['id']);
    try {
        $stmt = $pdo->prepare("SELECT * FROM tours WHERE id = ?");
        $stmt->execute([$tour_id]);
        $tour = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Handle error database, misalnya redirect ke halaman error atau tampilkan pesan
        // error_log("Error fetching tour detail: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Tur Kita! 🗺️</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Style khusus detail_tour.php */
        body {
            padding-top: 70px; /* Offset untuk fixed header */
        }
        .tour-detail-card {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: 30px auto;
            text-align: left;
        }
        .tour-detail-card img {
            max-width: 100%;
            height: auto;
            display: block;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        .tour-detail-card h2 {
            color: #007bff;
            font-size: 2.2em;
            margin-bottom: 10px;
        }
        .tour-detail-card .price {
            font-size: 1.6em;
            font-weight: bold;
            color: #28a745;
            margin-bottom: 10px;
        }
        .tour-detail-card .duration {
            font-size: 1.1em;
            font-style: italic;
            color: #777;
            margin-bottom: 25px;
        }
        .tour-detail-card .duration i {
            margin-right: 5px;
            color: #007bff;
        }
        .tour-detail-card h3 {
            color: #333;
            margin-top: 30px;
            margin-bottom: 15px;
            font-size: 1.6em;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        .tour-detail-card p {
            color: #555;
            line-height: 1.7;
            margin-bottom: 15px;
        }

        .booking-form {
            background-color: #f9f9f9;
            padding: 25px;
            border-radius: 8px;
            border: 1px solid #eee;
            margin-top: 30px;
        }
        .booking-form h3 {
            text-align: center;
            margin-bottom: 25px;
            color: #007bff;
            font-size: 1.8em;
            border-bottom: none;
            padding-bottom: 0;
        }
        .booking-form .form-group {
            margin-bottom: 20px;
        }
        .booking-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #444;
        }
        .booking-form input[type="text"],
        .booking-form input[type="email"],
        .booking-form input[type="number"],
        .booking-form input[type="date"] {
            width: calc(100% - 22px); /* Sesuaikan untuk padding/border */
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
            box-sizing: border-box;
        }
        .booking-form button {
            background-color: #28a745;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            font-size: 1.1em;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 20px;
            display: block;
            width: 100%;
            text-align: center;
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
        .btn-back-to-list {
            display: inline-block;
            margin-top: 30px;
            padding: 12px 25px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .btn-back-to-list:hover {
            background-color: #0056b3;
        }
    </style>
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
                    <li><a href="index.php#paket-tur">Paket Tur</a></li>
                    <li><a href="index.php#tentang-kami">Tentang Kami</a></li>
                    <li><a href="index.php#kontak">Kontak</a></li>
                    <li><a href="admin/login.php" class="btn-login-admin">Login Admin</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <?php if ($tour) : ?>
            <div class="tour-detail-card">
                <?php if (!empty($tour['image']) && file_exists('uploads/' . $tour['image'])): ?>
                    <img src="uploads/<?php echo htmlspecialchars($tour['image']); ?>" alt="<?php echo htmlspecialchars($tour['tour_name']); ?>">
                <?php else: ?>
                    <img src="images/placeholder.jpg" alt="No Image Available">
                <?php endif; ?>

                <h2><?php echo htmlspecialchars($tour['tour_name']); ?></h2>
                <p class="price">Harga: Rp <?php echo number_format($tour['price'], 0, ',', '.'); ?></p>
                <p class="duration"><i class="far fa-clock"></i> Durasi: <?php echo htmlspecialchars($tour['duration']); ?></p>
                <h3>Deskripsi:</h3>
                <p><?php echo nl2br(htmlspecialchars($tour['description'])); ?></p>

                <hr style="margin: 40px 0; border: 0; border-top: 1px solid #eee;">

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
                            <label for="booking_date">Tanggal Keberangkatan:</label>
                            <input type="date" id="booking_date" name="booking_date" required>
                        </div>
                        <button type="submit" class="btn">Konfirmasi Pemesanan</button>
                    </form>
                </div>
            </div>
        <?php else : ?>
            <div class="status-message error tour-not-found">
                <h2>Oops! Tur tidak ditemukan. 😔</h2>
                <p>Sepertinya kamu nyasar atau ID tur-nya gak valid. Yuk, kembali ke halaman <a href="index.php">Daftar Tur</a>.</p>
            </div>
        <?php endif; ?>

        <div style="text-align: center; margin-bottom: 50px;">
            <a href="index.php" class="btn-back-to-list">Kembali Ke Daftar Tur</a>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> JalanJalan Kuy!. All rights reserved.</p>
    </footer>
</body>
</html>