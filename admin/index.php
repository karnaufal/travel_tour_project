<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include_once '../config.php';

// Logic untuk mengambil dan menampilkan daftar tur
try {
    $stmt = $pdo->query("SELECT id, tour_name, description, price, duration, image FROM tours ORDER BY id DESC");
    $tours = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    $tours = [];
}

// Logic untuk menghapus tur
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $tour_id = intval($_GET['id']); // Pastikan ID adalah integer

    // Opsional: Hapus file gambar terkait sebelum menghapus entri database
    try {
        $stmt_select_image = $pdo->prepare("SELECT image FROM tours WHERE id = ?");
        $stmt_select_image->execute([$tour_id]);
        $image_to_delete = $stmt_select_image->fetchColumn();

        if ($image_to_delete && file_exists('../images/' . $image_to_delete) && $image_to_delete !== 'placeholder.jpg' && $image_to_delete !== 'default-hero-bg.jpg') {
            unlink('../images/' . $image_to_delete);
        }
    } catch (PDOException $e) {
        // Log error, tapi jangan hentikan proses delete tur di DB
        error_log("Error deleting image file: " . $e->getMessage());
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM tours WHERE id = ?");
        $stmt->execute([$tour_id]);
        $_SESSION['status_message'] = "Tur berhasil dihapus!";
        $_SESSION['status_type'] = "success";
        header("Location: index.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['status_message'] = "Gagal menghapus tur: " . $e->getMessage();
        $_SESSION['status_type'] = "error";
        header("Location: index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Tour - Admin Panel</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Gaya khusus untuk tabel admin */
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
        }
        th {
            background-color: #f2f2f2;
            font-weight: 600;
            color: #333;
        }
        td img {
            width: 80px;
            height: auto;
            border-radius: 4px;
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
        .btn-edit {
            background-color: #ffc107; /* Kuning */
        }
        .btn-edit:hover {
            background-color: #e0a800;
        }
        .btn-delete {
            background-color: #dc3545; /* Merah */
        }
        .btn-delete:hover {
            background-color: #c82333;
        }
        .add-new-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #28a745; /* Hijau */
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
            margin-bottom: 20px;
            font-size: 1em;
            transition: background-color 0.3s ease;
        }
        .add-new-btn:hover {
            background-color: #218838;
        }
        .no-tours {
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
                    <li><a href="index.php" class="active">Kelola Tour</a></li>
                    <li><a href="bookings.php">Kelola Pemesanan</a></li>
                    <li><a href="logout.php" class="btn-login-admin">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="admin-table-container">
        <h1>Daftar Tour</h1>
        <a href="add_tour.php" class="add-new-btn"><i class="fas fa-plus"></i> Tambah Tour Baru</a>

        <?php if (isset($_SESSION['status_message'])): ?>
            <div class="status-message <?php echo $_SESSION['status_type']; ?>">
                <?php echo $_SESSION['status_message']; ?>
            </div>
            <?php
            unset($_SESSION['status_message']);
            unset($_SESSION['status_type']);
            ?>
        <?php endif; ?>

        <?php if (!empty($tours)): ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Gambar</th>
                        <th>Nama Tour</th>
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
                            <?php 
                            // Pastikan path ke gambar benar. Folder images/
                            $imagePath = '../images/' . $tour['image']; 
                            if (!empty($tour['image']) && file_exists($imagePath)): 
                            ?>
                                <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($tour['tour_name']); ?>">
                            <?php else: ?>
                                <img src="../images/placeholder.jpg" alt="No Image">
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($tour['tour_name']); ?></td>
                        <td>Rp <?php echo number_format($tour['price'], 0, ',', '.'); ?></td>
                        <td><?php echo htmlspecialchars($tour['duration']); ?></td>
                        <td><?php echo htmlspecialchars(substr($tour['description'], 0, 50)); ?>...</td>
                        <td class="action-buttons">
                            <a href="edit_tour.php?id=<?php echo htmlspecialchars($tour['id']); ?>" class="btn-edit">Edit</a>
                            <a href="index.php?action=delete&id=<?php echo htmlspecialchars($tour['id']); ?>" class="btn-delete" onclick="return confirm('Apakah Anda yakin ingin menghapus tur ini?');">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <p class="no-tours">Belum ada tour yang ditambahkan.</p>
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