<?php
session_start();
ob_start();

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include_once '../config.php'; // Koneksi database PDO

$message = '';
$status_class = '';

// Ambil pesan status dari URL jika ada
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
    // Ambil semua data tur dari database
    $stmt = $pdo->prepare("SELECT * FROM tours ORDER BY id DESC"); // Order by ID terbaru
    $stmt->execute();
    $tours = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "Error saat mengambil data tur: " . htmlspecialchars($e->getMessage());
    $status_class = 'error';
    $tours = []; // Pastikan $tours kosong jika ada error
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Tur - Admin Panel</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* Tambahan style khusus untuk halaman ini */
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
        .btn-admin.btn-warning {
            background-color: #ffc107;
            color: #333;
        }
        .btn-admin.btn-warning:hover {
            background-color: #e0a800;
        }
        .btn-admin.btn-danger {
            background-color: #dc3545;
        }
        .btn-admin.btn-danger:hover {
            background-color: #c82333;
        }
        .btn-admin.btn-cancel { /* Untuk logout */
            background-color: #6c757d;
        }
        .btn-admin.btn-cancel:hover {
            background-color: #5a6268;
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
        .tour-image-thumb {
            width: 80px; /* Atur lebar thumbnail */
            height: 60px; /* Atur tinggi thumbnail */
            object-fit: cover; /* Pastikan gambar tidak terdistorsi */
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="admin-navigation-top">
            <h2>Panel Admin Travel Tour Gokil! <span class="subtitle">Kelola semua tur dengan mudah.</span></h2>
            <div class="admin-actions-group">
                <a href="add_tour.php" class="btn-admin btn-primary">Tambah Tur Baru</a>
                <a href="bookings.php" class="btn-admin btn-primary">Kelola Pemesanan</a>
                <a href="logout.php" class="btn-admin btn-cancel">Logout</a>
            </div>
        </div>
    </header>

    <main>
        <div class="admin-container">
            <h1>Daftar Tur <span class="subtitle">Semua paket tur yang tersedia</span></h1>

            <?php if (!empty($message)): ?>
                <div class="message-status <?php echo $status_class; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <div class="admin-table-wrapper">
                <?php if (!empty($tours)): ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Gambar</th>
                                <th>Nama Tur</th>
                                <th>Harga</th>
                                <th>Durasi</th>
                                <th>Deskripsi Singkat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tours as $tour): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($tour['id']); ?></td>
                                    <td>
                                        <?php if (!empty($tour['image']) && file_exists('../uploads/' . $tour['image'])): ?>
                                            <img src="../uploads/<?php echo htmlspecialchars($tour['image']); ?>" alt="<?php echo htmlspecialchars($tour['tour_name']); ?>" class="tour-image-thumb">
                                        <?php else: ?>
                                            <span style="color: #666; font-size: 0.8em;">No Image</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($tour['tour_name']); ?></td>
                                    <td>Rp <?php echo number_format($tour['price'], 0, ',', '.'); ?></td>
                                    <td><?php echo htmlspecialchars($tour['duration']); ?></td>
                                    <td><?php echo htmlspecialchars(substr($tour['description'], 0, 50)); ?>...</td>
                                    <td class="actions">
                                        <a href="edit_tour.php?id=<?php echo htmlspecialchars($tour['id']); ?>" class="btn-admin btn-warning">Edit</a>
                                        <a href="delete_tour.php?id=<?php echo htmlspecialchars($tour['id']); ?>" class="btn-admin btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus tur <?php echo htmlspecialchars($tour['tour_name']); ?>?');">Hapus</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-tours-message">
                        <p>Belum ada tur yang ditambahkan. Ayo <a href="add_tour.php">tambahkan tur baru</a>!</p>
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