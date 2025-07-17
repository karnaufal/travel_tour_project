<?php
session_start();
// Pastikan admin sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include_once '../config.php'; // Sesuaikan path ke config.php

$booking = null; // Inisialisasi variabel booking

// Pastikan ID pemesanan ada di URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $booking_id = $_GET['id'];

    try {
        // Ambil semua detail pemesanan berdasarkan ID
        $stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ?");
        $stmt->execute([$booking_id]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        // Jika pemesanan tidak ditemukan, arahkan kembali
        if (!$booking) {
            $_SESSION['status_message'] = "Pemesanan tidak ditemukan.";
            $_SESSION['status_type'] = "error";
            header("Location: bookings.php");
            exit();
        }

    } catch (PDOException $e) {
        $_SESSION['status_message'] = "Error mengambil detail pemesanan: " . $e->getMessage();
        $_SESSION['status_type'] = "error";
        header("Location: bookings.php");
        exit();
    }
} else {
    // Jika ID tidak ada, arahkan kembali
    $_SESSION['status_message'] = "ID Pemesanan tidak diberikan.";
    $_SESSION['status_type'] = "error";
    header("Location: bookings.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pemesanan #<?php echo htmlspecialchars($booking['id'] ?? ''); ?> - Admin Panel</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Tambahan CSS untuk halaman detail */
        .detail-card {
            background-color: var(--card-bg);
            padding: 30px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-small);
            margin-bottom: 20px;
        }
        .detail-card h2 {
            color: var(--primary-color);
            margin-top: 0;
            margin-bottom: 25px;
            border-bottom: 2px solid var(--border-color);
            padding-bottom: 15px;
        }
        .detail-item {
            display: flex;
            flex-wrap: wrap; /* Izinkan wrap pada layar kecil */
            margin-bottom: 15px;
            border-bottom: 1px dotted var(--border-color);
            padding-bottom: 10px;
        }
        .detail-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .detail-label {
            font-weight: 600;
            color: var(--text-color);
            flex: 0 0 180px; /* Lebar tetap untuk label */
            margin-right: 20px;
        }
        .detail-value {
            flex: 1; /* Ambil sisa ruang */
            color: var(--text-color);
        }
        .detail-item.message .detail-value {
            white-space: pre-wrap; /* Mempertahankan spasi dan baris baru di pesan */
        }
        .back-button {
            display: inline-block;
            margin-top: 20px;
            background-color: var(--secondary-color); /* Bisa diganti ke primary-color */
            color: white;
            padding: 10px 20px;
            border-radius: var(--border-radius-small);
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .back-button:hover {
            background-color: var(--secondary-color-dark);
        }

        /* Responsive adjustments for detail page */
        @media (max-width: 768px) {
            .detail-label {
                flex: 0 0 100%; /* Label ambil 100% lebar */
                margin-right: 0;
                margin-bottom: 5px; /* Jarak antara label dan value */
            }
            .detail-value {
                flex: 0 0 100%; /* Value ambil 100% lebar */
            }
        }
    </style>
</head>
<body>

    <header class="main-header">
        <div class="container header-content">
            <div class="logo">
                <a href="../index.php">JalanJalan Kuy!</a>
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="index.php">Kelola Tour</a></li>
                    <li><a href="bookings.php" class="active">Kelola Pemesanan</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container content">
        <?php if (isset($_SESSION['status_message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['status_type']; ?>">
                <?php echo htmlspecialchars($_SESSION['status_message']); ?>
            </div>
            <?php
            unset($_SESSION['status_message']);
            unset($_SESSION['status_type']);
            ?>
        <?php endif; ?>

        <?php if ($booking): ?>
            <div class="detail-card">
                <h2>Detail Pemesanan #<?php echo htmlspecialchars($booking['id']); ?></h2>

                <div class="detail-item">
                    <span class="detail-label">Nama Pelanggan:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($booking['customer_name']); ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Email Pelanggan:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($booking['customer_email']); ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Telepon Pelanggan:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($booking['customer_phone']); ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Nama Tour:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($booking['tour_name']); ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Jumlah Peserta:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($booking['num_participants']); ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Total Harga:</span>
                    <span class="detail-value">Rp <?php echo number_format($booking['total_price'], 0, ',', '.'); ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Tanggal Keberangkatan:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($booking['preferred_date']); ?></span>
                </div>
                <div class="detail-item message">
                    <span class="detail-label">Pesan:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($booking['message']); ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Waktu Pemesanan:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($booking['booking_date']); ?></span>
                </div>

                <a href="bookings.php" class="back-button">
                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar Pemesanan
                </a>
            </div>
        <?php else: ?>
            <p>Detail pemesanan tidak dapat dimuat.</p>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> JalanJalan Kuy! Admin Panel. All rights reserved.</p>
        <p style="font-size: 0.8em; margin-top: 5px;">Dibuat Karnaufal</p>
    </footer>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        // Skrip highlight navigasi tetap sama
        $(document).ready(function() {
            const currentAdminPath = window.location.pathname.split('/').pop();
            $('nav.main-nav ul li a').removeClass('active');
            if (currentAdminPath === 'dashboard.php') {
                $('nav.main-nav ul li a[href="dashboard.php"]').addClass('active');
            } else if (currentAdminPath === 'index.php' || currentAdminPath.startsWith('edit_tour.php') || currentAdminPath.startsWith('add_tour.php')) {
                $('nav.main-nav ul li a[href="index.php"]').addClass('active');
            } else if (currentAdminPath === 'bookings.php' || currentAdminPath.startsWith('export_bookings.php') || currentAdminPath.startsWith('view_bookings.php')) { // Tambahkan view_bookings.php
                $('nav.main-nav ul li a[href="bookings.php"]').addClass('active');
            }
        });
    </script>
</body>
</html>