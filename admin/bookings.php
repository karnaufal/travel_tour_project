<?php
session_start(); // Mulai sesi untuk memeriksa login
ob_start(); // Mulai output buffering

// Cek apakah user sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php"); // Redirect ke halaman login jika belum login
    exit();
}

include_once '../config.php'; // Pastikan path ini benar untuk koneksi DB (menggunakan PDO)

$message = '';
$status_class = '';

// Ambil pesan status dari URL jika ada (misal setelah aksi delete booking)
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'success') {
        $message = htmlspecialchars($_GET['message'] ?? 'Aksi berhasil!');
        $status_class = 'success';
    } elseif ($_GET['status'] == 'error') {
        $message = htmlspecialchars($_GET['message'] ?? 'Terjadi kesalahan!');
        $status_class = 'error';
    }
}

try {
    // Ambil semua data pemesanan dari database, gabungkan dengan data tur
    $stmt = $pdo->prepare("SELECT b.*, t.tour_name, t.price FROM bookings b JOIN tours t ON b.tour_id = t.id ORDER BY b.created_at DESC");
    $stmt->execute();
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "Error saat mengambil data pemesanan: " . htmlspecialchars($e->getMessage());
    $status_class = 'error';
    $bookings = []; // Pastikan $bookings kosong jika ada error
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pemesanan - Admin Panel</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* Tambahan style khusus untuk halaman ini jika diperlukan */
        .admin-table th, .admin-table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .admin-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .admin-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .admin-table tr:hover {
            background-color: #f1f1f1;
        }
        .admin-actions {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }
        .btn-admin {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
            font-weight: bold;
            transition: background-color 0.3s ease;
            text-align: center;
        }
        .btn-admin.btn-primary {
            background-color: #007bff;
        }
        .btn-admin.btn-primary:hover {
            background-color: #0056b3;
        }
        .btn-admin.btn-danger {
            background-color: #dc3545;
        }
        .btn-admin.btn-danger:hover {
            background-color: #c82333;
        }
        .message-status {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 1em;
            text-align: center;
        }
        .message-status.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message-status.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="admin-navigation-top">
            <h2>Panel Admin Travel Tour Gokil! <span class="subtitle">Kelola semua pemesanan.</span></h2>
            <div class="admin-actions-group">
                <a href="index.php" class="btn-admin btn-primary">Kelola Tur</a>
                <a href="add_tour.php" class="btn-admin btn-primary">Tambah Tur</a>
                <a href="logout.php" class="btn-admin btn-cancel">Logout</a>
            </div>
        </div>
    </header>

    <main>
        <div class="admin-container">
            <h1>Kelola Pemesanan <span class="subtitle">Daftar semua pemesanan tur yang masuk</span></h1>

            <?php if (!empty($message)): ?>
                <div class="message-status <?php echo $status_class; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <div class="admin-table-wrapper">
                <?php if (!empty($bookings)): ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID Pemesanan</th>
                                <th>Nama Pelanggan</th>
                                <th>Email</th>
                                <th>Nama Tur</th>
                                <th>Harga Tur</th>
                                <th>Jumlah Peserta</th>
                                <th>Tanggal Keberangkatan</th>
                                <th>Waktu Pemesanan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bookings as $booking): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($booking['id']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['customer_name']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['customer_email']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['tour_name']); ?></td>
                                    <td>Rp <?php echo number_format($booking['price'], 0, ',', '.'); ?></td>
                                    <td><?php echo htmlspecialchars($booking['num_participants']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['booking_date']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['created_at']); ?></td>
                                    <td>
                                        <a href="delete_booking.php?id=<?php echo htmlspecialchars($booking['id']); ?>" class="btn-admin btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus pemesanan ID #<?php echo htmlspecialchars($booking['id']); ?> dari <?php echo htmlspecialchars($booking['customer_name']); ?>?');">Hapus</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-tours-message">
                        <p>Belum ada pemesanan yang masuk saat ini. Mari sebarkan tur-tur menarikmu!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Travel Tour Gokil. Admin Panel.</p>
    </footer>
</body>
</html>
<?php
ob_end_flush();
?>