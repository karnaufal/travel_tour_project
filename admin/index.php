<?php
session_start();
ob_start();

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php"); // Redirect ke halaman login jika belum login
    exit();
}

include_once '../config.php'; // Koneksi database

// Inisialisasi variabel untuk paginasi
$limit = 5; // Jumlah tur per halaman
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Ambil total jumlah tur untuk paginasi
try {
    $total_tours_stmt = $pdo->query("SELECT COUNT(*) FROM tours");
    $total_tours_count = $total_tours_stmt->fetchColumn(); // Nama variabel diubah agar tidak bentrok dengan $total_tours statistik
    $total_pages = ceil($total_tours_count / $limit);

    // Ambil semua data tur dari database dengan paginasi
    $stmt = $pdo->prepare("SELECT * FROM tours ORDER BY id DESC LIMIT :limit OFFSET :offset");
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $tours = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Ambil total jumlah tur (statistik)
    $total_tours = $total_tours_count; // Menggunakan hasil count dari atas

    // Ambil total jumlah pemesanan (statistik)
    $total_bookings_stmt = $pdo->query("SELECT COUNT(*) FROM bookings");
    $total_bookings = $total_bookings_stmt->fetchColumn();

} catch (PDOException $e) {
    echo "Error: " . htmlspecialchars($e->getMessage()); // Sanitasi pesan error
    $tours = []; // Pastikan $tours kosong jika ada error
    $total_tours = 0; // Default jika error
    $total_bookings = 0; // Default jika error
    $total_pages = 1; // Default jika error
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Kelola Tur</title>
    <link rel="stylesheet" href="../css/style.css">
    </head>
<body>
    <header>
        <h1>Panel Admin</h1>
    </header>

    <main class="admin-container">
        <?php
        // >>>>>>>>> KODE UNTUK MENAMPILKAN PESAN STATUS (dari add/edit/delete) <<<<<<<<<
        if (isset($_GET['status'])) {
            if ($_GET['status'] == 'success') {
                $message = htmlspecialchars($_GET['message'] ?? 'Operasi berhasil!');
                echo '<div class="status-message success">' . $message . '</div>';
            } elseif ($_GET['status'] == 'error' || $_GET['status'] == 'error_validation' || $_GET['status'] == 'error_auth') {
                $message = htmlspecialchars($_GET['message'] ?? 'Terjadi kesalahan tidak dikenal.');
                echo '<div class="status-message error">' . $message . '</div>';
            }
        }
        // >>>>>>>>> AKHIR KODE UNTUK MENAMPILKAN PESAN STATUS <<<<<<<<<
        ?>

        <div class="admin-navigation-top"> <h2>Kelola Tur <span class="subtitle">Daftar tur yang terdaftar</span></h2>
            <div class="admin-actions-group">
                <a href="bookings.php" class="btn-admin btn-primary">Lihat Pemesanan</a>
                <a href="logout.php" class="btn-admin btn-cancel">Logout</a>
            </div>
        </div>

        <div class="admin-stats">
            <div class="stat-card">
                <h3>Total Tur</h3>
                <p><?php echo $total_tours; ?></p>
            </div>
            <div class="stat-card">
                <h3>Total Pemesanan</h3>
                <p><?php echo $total_bookings; ?></p>
            </div>
        </div>

        <div class="admin-section-header"> <h2>Daftar Tur</h2>
            <a href="add_tour.php" class="btn-add-tour">Tambah Tur Baru</a>
        </div>
        
        <div class="admin-table-wrapper"> <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Gambar</th>
                        <th>Nama Tur</th>
                        <th>Harga</th>
                        <th>Durasi</th>
                        <th>Deskripsi Singkat</th> <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($tours)): ?>
                        <?php foreach ($tours as $tour): ?>
                        <tr>
                            <td data-label="ID"><?php echo htmlspecialchars($tour['id']); ?></td>
                            <td data-label="Gambar"><img src="<?php echo htmlspecialchars($tour['image_url']); ?>" alt="<?php echo htmlspecialchars($tour['tour_name']); ?>"></td>
                            <td data-label="Nama Tur"><?php echo htmlspecialchars($tour['tour_name']); ?></td>
                            <td data-label="Harga">Rp <?php echo number_format($tour['price'], 0, ',', '.'); ?></td>
                            <td data-label="Durasi"><?php echo htmlspecialchars($tour['duration']); ?></td>
                            <td data-label="Deskripsi Singkat"><?php echo nl2br(htmlspecialchars(substr($tour['description'], 0, 80))) . (strlen($tour['description']) > 80 ? '...' : ''); ?></td>
                            <td data-label="Aksi" class="actions">
                                <a href="edit_tour.php?id=<?php echo htmlspecialchars($tour['id']); ?>" class="btn-edit">Edit</a>
                                <a href="delete_tour.php?id=<?php echo htmlspecialchars($tour['id']); ?>" class="btn-delete" onclick="return confirm('Yakin ingin menghapus tur <?php echo htmlspecialchars($tour['tour_name']); ?>?');">Hapus</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">Belum ada tur yang ditambahkan. Yuk, <a href="add_tour.php">tambah tur baru</a>!</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php // Pagination ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>" class="pagination-btn">Sebelumnya</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <span <?php if ($i == $page) echo 'class="current-page"'; ?>>
                    <?php if ($i == $page): ?>
                        <?php echo $i; ?>
                    <?php else: ?>
                        <a href="?page=<?php echo $i; ?>" class="pagination-btn"><?php echo $i; ?></a>
                    <?php endif; ?>
                </span>
            <?php endfor; ?>
            <?php if ($page < $total_pages): ?>
                <a href="?page=<?php echo $page + 1; ?>" class="pagination-btn">Berikutnya</a>
            <?php endif; ?>
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