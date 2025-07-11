<?php
session_start(); // Wajib ada di awal setiap file yang menggunakan session
ob_start(); // Mulai output buffering

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php"); // Redirect ke halaman login jika belum login
    exit();
}

include '../config.php'; // Koneksi database

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
    <style>
        /* CSS tambahan untuk merapikan layout admin action */
        .admin-actions {
            display: flex;
            gap: 10px; /* Jarak antar tombol */
            justify-content: flex-end; /* Taruh tombol ke kanan */
            margin-top: 15px; /* Jarak dari teks di atasnya */
        }
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .btn-admin { /* Ini dari bookings.php, kita pakai juga di sini */
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
            background-color: #007bff; /* Biru */
        }
        .btn-admin.btn-primary:hover {
            background-color: #0056b3;
        }
        .btn-cancel-admin { /* Warna merah untuk Logout */
            background-color: #dc3545;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
            font-weight: bold;
        }
        .btn-cancel-admin:hover {
            background-color: #c82333;
        }
        .btn-add-tour { /* Warna hijau untuk Tambah Tur Baru */
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .btn-add-tour:hover {
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
        <h1>Panel Admin Travel Tour Gokil! ⚙️</h1>
        <p>Kelola semua tur dengan mudah.</p>
        <div class="admin-actions">
            <a href="add_tour.php" class="btn-add-tour">➕ Tambah Tur Baru</a>
            <a href="bookings.php" class="btn-admin btn-primary">Lihat Pemesanan</a>
            <a href="logout.php" class="btn-cancel-admin">Logout</a>
        </div>
    </header>

    <main class="admin-container">
        <div class="admin-header">
            <h2>Daftar Tur</h2> 
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
                        <a href="delete_tour.php?id=<?php echo htmlspecialchars($tour['id']); ?>" class="btn-delete" onclick="return confirm('Apakah Anda yakin ingin menghapus tur <?php echo htmlspecialchars($tour['tour_name']); ?>?');">Hapus</a>
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
<?php
ob_end_flush(); // Akhiri output buffering
?>