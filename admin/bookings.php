<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include_once '../config.php';

// Logic untuk mengambil dan menampilkan daftar pemesanan
try {
    // Ambil SEMUA kolom dari tabel bookings (b.*)
    // Sekarang tour_name, customer_phone, preferred_date, message, dan total_price
    // sudah ada di tabel bookings.
    // Kita TIDAK PERLU lagi JOIN ke tabel 'tours' hanya untuk tour_name dan price,
    // karena semua data penting pemesanan (termasuk nama tour dan total harga)
    // sudah tersimpan di tabel 'bookings'.
    $stmt = $pdo->query("SELECT * FROM bookings ORDER BY booking_date DESC"); // Urutkan berdasarkan waktu pemesanan terbaru
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    $bookings = [];
}

// Logic untuk menghapus pemesanan
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $booking_id = $_GET['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM bookings WHERE id = ?");
        $stmt->execute([$booking_id]);
        $_SESSION['status_message'] = "Pemesanan berhasil dihapus!";
        $_SESSION['status_type'] = "success";
        header("Location: bookings.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['status_message'] = "Gagal menghapus pemesanan: " . $e->getMessage();
        $_SESSION['status_type'] = "error";
        header("Location: bookings.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pemesanan - Admin Panel</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Gaya khusus untuk tabel admin (JANGAN DIHAPUS, ini penting) */
        .admin-table-container {
            max-width: 1200px;
            margin: 80px auto 40px auto; /* Margin atas disesuaikan untuk header */
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .admin-table-container h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #007bff;
            font-size: 2.5em;
        }
        .table-responsive {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
            vertical-align: middle;
            white-space: nowrap; /* Mencegah teks wrapping di kolom tertentu, sesuaikan jika perlu */
        }
        th {
            background-color: #f2f2f2;
            font-weight: 600;
            color: #333;
        }
        .action-buttons a {
            display: inline-block;
            padding: 8px 12px;
            margin-right: 5px;
            border-radius: 5px;
            text-decoration: none;
            color: #fff;
            font-size: 0.9em;
            transition: background-color 0.3s ease;
        }
        .btn-delete {
            background-color: #dc3545; /* Merah */
        }
        .btn-delete:hover {
            background-color: #c82333;
        }
        .no-bookings {
            text-align: center;
            padding: 30px;
            color: #666;
            font-style: italic;
        }
        /* Status Message */
        .status-message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: bold;
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
                    <li><a href="logout.php" class="btn-login-admin">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="admin-table-container">
        <h1>Kelola Pemesanan</h1>
        <?php if (isset($_SESSION['status_message'])): ?>
            <div class="status-message <?php echo $_SESSION['status_type']; ?>">
                <?php echo htmlspecialchars($_SESSION['status_message']); ?>
            </div>
            <?php
            unset($_SESSION['status_message']);
            unset($_SESSION['status_type']);
            ?>
        <?php endif; ?>

        <?php if (!empty($bookings)): ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID Pemesanan</th>
                        <th>Nama Pelanggan</th>
                        <th>Email</th>
                        <th>Telepon</th> <th>Nama Tour</th>
                        <th>Jumlah Peserta</th>
                        <th>Total Harga</th> <th>Tanggal Keberangkatan</th>
                        <th>Pesan Tambahan</th> <th>Waktu Pemesanan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($booking['id'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($booking['customer_name'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($booking['customer_email'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($booking['customer_phone'] ?? ''); ?></td> <td><?php echo htmlspecialchars($booking['tour_name'] ?? ''); ?></td> <td><?php echo htmlspecialchars($booking['num_participants'] ?? 0); ?></td>
                        <td>Rp <?php echo number_format($booking['total_price'] ?? 0, 0, ',', '.'); ?></td> <td><?php echo htmlspecialchars($booking['preferred_date'] ?? ''); ?></td> <td><?php echo htmlspecialchars($booking['message'] ?? ''); ?></td> <td><?php echo htmlspecialchars($booking['booking_date'] ?? ''); ?></td> <td class="action-buttons">
                            <a href="bookings.php?action=delete&id=<?php echo htmlspecialchars($booking['id'] ?? ''); ?>" class="btn-delete" onclick="return confirm('Apakah Anda yakin ingin menghapus pemesanan ini?');">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <p class="no-bookings">Belum ada pemesanan yang masuk.</p>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> JalanJalan Kuy! Admin Panel. All rights reserved.</p>
        <p style="font-size: 0.8em; margin-top: 5px;">Dibuat Karnaufal</p>
    </footer>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            // Highlight navigasi aktif di admin panel
            const currentAdminPath = window.location.pathname.split('/').pop();
            $('nav.main-nav ul li a').removeClass('active');
            if (currentAdminPath === 'dashboard.php') {
                $('nav.main-nav ul li a[href="dashboard.php"]').addClass('active');
            } else if (currentAdminPath === 'index.php' || currentAdminPath.startsWith('edit_tour.php') || currentAdminPath.startsWith('add_tour.php')) {
                $('nav.main-nav ul li a[href="index.php"]').addClass('active');
            } else if (currentAdminPath === 'bookings.php') {
                $('nav.main-nav ul li a[href="bookings.php"]').addClass('active');
            }
        });
    </script>
</body>
</html>