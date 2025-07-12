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
                                    <td data-label="ID Pemesanan"><?php echo htmlspecialchars($booking['id']); ?></td>
                                    <td data-label="Nama Pelanggan"><?php echo htmlspecialchars($booking['customer_name']); ?></td>
                                    <td data-label="Email"><?php echo htmlspecialchars($booking['customer_email']); ?></td>
                                    <td data-label="Nama Tur"><?php echo htmlspecialchars($booking['tour_name']); ?></td>
                                    <td data-label="Harga Tur">Rp <?php echo number_format($booking['price'], 0, ',', '.'); ?></td>
                                    <td data-label="Jumlah Peserta"><?php echo htmlspecialchars($booking['num_participants']); ?></td>
                                    <td data-label="Tanggal Keberangkatan"><?php echo htmlspecialchars($booking['booking_date']); ?></td>
                                    <td data-label="Waktu Pemesanan"><?php echo htmlspecialchars($booking['created_at']); ?></td>
                                    <td data-label="Aksi">
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