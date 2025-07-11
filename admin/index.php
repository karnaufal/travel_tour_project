<?php
include '../config.php'; // Perhatikan: '../' karena kita di dalam folder admin/

// Ambil semua data tur dari database
try {
    $stmt = $pdo->query("SELECT * FROM tours ORDER BY id DESC"); // Urutkan dari ID terbaru
    $tours = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . htmlspecialchars($e->getMessage()); // Sanitasi pesan error
    $tours = [];
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
        <h1>Panel Admin Travel Tour Gokil! ⚙️</h1>
        <p>Kelola semua tur dengan mudah.</p>
    </header>

    <main class="admin-container">
        <div class="admin-header">
            <h1>Daftar Tur</h1>
            <a href="add_tour.php" class="btn-add-tour">➕ Tambah Tur Baru</a>
        </div>

        <?php
        // Tampilkan pesan status jika ada (dari add/edit/delete)
        if (isset($_GET['status'])) {
            $status_class = ($_GET['status'] == 'success') ? 'success' : 'error';
            $message = htmlspecialchars($_GET['message'] ?? 'Operasi berhasil.');
            echo '<div class="status-message ' . $status_class . '">' . $message . '</div>';
        }
        ?>

        <?php if (!empty($tours)): ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Gambar</th>
                    <th>Nama Tur</th>
                    <th>Harga</th>
                    <th>Durasi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tours as $tour): ?>
                <tr>
                    <td data-label="ID"><?php echo htmlspecialchars($tour['id']); ?></td>
                    <td data-label="Gambar"><img src="../<?php echo htmlspecialchars($tour['image_url']); ?>" alt="<?php echo htmlspecialchars($tour['tour_name']); ?>"></td>
                    <td data-label="Nama Tur"><?php echo htmlspecialchars($tour['tour_name']); ?></td>
                    <td data-label="Harga">Rp <?php echo number_format($tour['price'], 0, ',', '.'); ?></td>
                    <td data-label="Durasi"><?php echo htmlspecialchars($tour['duration']); ?></td>
                    <td class="actions" data-label="Aksi">
                        <a href="edit_tour.php?id=<?php echo htmlspecialchars($tour['id']); ?>" class="btn-edit">Edit</a>
                        <a href="delete_tour.php?id=<?php echo htmlspecialchars($tour['id']); ?>" class="btn-delete" onclick="return confirm('Yakin mau hapus tur ini? Aksi ini tidak bisa dibatalkan!');">Hapus</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p class="no-tours-message">Belum ada tur yang terdaftar. Yuk, <a href="add_tour.php">tambah tur baru</a>!</p>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Travel Tour Gokil. Admin Panel.</p>
    </footer>
</body>
</html>