<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include_once '../config.php';

// Handle aksi persetujuan atau penghapusan ulasan
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && isset($_GET['id'])) {
    $review_id = $_GET['id'];
    $action = $_GET['action'];

    try {
        if ($action === 'approve') {
            $stmt = $pdo->prepare("UPDATE reviews SET is_approved = TRUE WHERE id = ?");
            $stmt->execute([$review_id]);
            $_SESSION['status_message'] = "Ulasan berhasil disetujui.";
            $_SESSION['status_type'] = "success";
        } elseif ($action === 'delete') {
            $stmt = $pdo->prepare("DELETE FROM reviews WHERE id = ?");
            $stmt->execute([$review_id]);
            $_SESSION['status_message'] = "Ulasan berhasil dihapus.";
            $_SESSION['status_type'] = "success";
        }
    } catch (PDOException $e) {
        $_SESSION['status_message'] = "Error: " . $e->getMessage();
        $_SESSION['status_type'] = "error";
    }
    header("Location: reviews.php");
    exit();
}

// Ambil semua ulasan, urutkan yang belum disetujui di atas
$reviews = [];
try {
    $stmt = $pdo->query("SELECT r.*, t.tour_name FROM reviews r JOIN tours t ON r.tour_id = t.id ORDER BY r.is_approved ASC, r.review_date DESC");
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Handle error
    error_log("Error fetching reviews for admin: " . $e->getMessage());
}

$status_message = $_SESSION['status_message'] ?? '';
$status_type = $_SESSION['status_type'] ?? '';
unset($_SESSION['status_message']);
unset($_SESSION['status_type']);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Ulasan - Admin Panel</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* CSS Tambahan untuk halaman reviews (jika belum ada di style.css) */
        .admin-table-container {
            margin-top: 100px; /* Offset for fixed header */
        }
        .review-status-pending {
            color: orange;
            font-weight: bold;
        }
        .review-status-approved {
            color: green;
            font-weight: bold;
        }
        .action-btns .btn-approve {
            background-color: var(--accent-color);
            color: #fff;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.9em;
            transition: background-color 0.3s ease;
        }
        .action-btns .btn-approve:hover {
            background-color: #218838;
        }
        .action-btns .btn-delete {
            background-color: #dc3545;
            color: #fff;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.9em;
            transition: background-color 0.3s ease;
            margin-left: 10px;
        }
        .action-btns .btn-delete:hover {
            background-color: #c82333;
        }
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
<body class="admin-body">

    <header class="main-header">
        <div class="container header-content">
            <div class="logo">
                <a href="../index.php">JalanJalan Kuy!</a>
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="index.php">Kelola Tur</a></li>
                    <li><a href="bookings.php">Kelola Pemesanan</a></li>
                    <li><a href="reviews.php" class="active">Kelola Ulasan</a></li> <li><a href="logout.php" class="btn-login-admin">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="admin-table-container">
        <h1>Kelola Ulasan Pelanggan</h1>
        <?php if ($status_message): ?>
            <div class="status-message <?php echo $status_type; ?>">
                <?php echo htmlspecialchars($status_message); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($reviews)): ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama Pengguna</th>
                            <th>Tur</th>
                            <th>Rating</th>
                            <th>Komentar</th>
                            <th>Tanggal Ulasan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reviews as $review): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($review['id']); ?></td>
                                <td><?php echo htmlspecialchars($review['user_name']); ?></td>
                                <td><?php echo htmlspecialchars($review['tour_name']); ?></td>
                                <td>
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="<?php echo ($i <= $review['rating']) ? 'fas fa-star' : 'far fa-star'; ?>" style="color: #ffc107;"></i>
                                    <?php endfor; ?>
                                </td>
                                <td><?php echo htmlspecialchars(substr($review['comment'], 0, 50)); ?>...</td>
                                <td><?php echo date('d M Y H:i', strtotime($review['review_date'])); ?></td>
                                <td>
                                    <?php if ($review['is_approved']): ?>
                                        <span class="review-status-approved">Disetujui</span>
                                    <?php else: ?>
                                        <span class="review-status-pending">Menunggu</span>
                                    <?php endif; ?>
                                </td>
                                <td class="action-btns">
                                    <?php if (!$review['is_approved']): ?>
                                        <a href="reviews.php?action=approve&id=<?php echo htmlspecialchars($review['id']); ?>" class="btn-approve">Setujui</a>
                                    <?php endif; ?>
                                    <a href="reviews.php?action=delete&id=<?php echo htmlspecialchars($review['id']); ?>" class="btn-delete" onclick="return confirm('Apakah Anda yakin ingin menghapus ulasan ini?');">Hapus</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p style="text-align: center; margin-top: 30px; color: var(--light-text);">Belum ada ulasan yang masuk.</p>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> JalanJalan Kuy! Admin Panel. All rights reserved.</p>
        <p style="font-size: 0.8em; margin-top: 5px;">Dibuat Type-Spype</p>
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
            } else if (currentAdminPath === 'reviews.php') {
                $('nav.main-nav ul li a[href="reviews.php"]').addClass('active');
            }
        });
    </script>
</body>
</html>