    <?php
    session_start();
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header("Location: login.php");
        exit();
    }

    include_once '../config.php';

    // Logic untuk mengambil dan menampilkan daftar pemesanan
    try {
        // Ambil SEMUA kolom dari tabel bookings
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
        <link rel="stylesheet" href="../css/style.css"> <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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

        <div class="container content"> <h1>Kelola Pemesanan</h1>
            <?php if (isset($_SESSION['status_message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['status_type']; ?>"> <?php echo htmlspecialchars($_SESSION['status_message']); ?>
                </div>
                <?php
                unset($_SESSION['status_message']);
                unset($_SESSION['status_type']);
                ?>
            <?php endif; ?>

            <div style="text-align: right; margin-bottom: 20px;">
                <a href="export_bookings.php" class="btn btn-success"> <i class="fas fa-file-excel"></i> Export Data Pemesanan
                </a>
            </div>

            <?php if (!empty($bookings)): ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th> <th>Nama Pelanggan</th>
                            <th>Email</th>
                            <th>Telepon</th>
                            <th>Nama Tour</th>
                            <th>Peserta</th> <th>Harga</th> <th>Tgl Keberangkatan</th> <th>Pesan</th> <th>Waktu Pemesanan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($booking['id'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($booking['customer_name'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($booking['customer_email'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($booking['customer_phone'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($booking['tour_name'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($booking['num_participants'] ?? 0); ?></td>
                            <td>Rp <?php echo number_format($booking['total_price'] ?? 0, 0, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars($booking['preferred_date'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($booking['message'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($booking['booking_date'] ?? ''); ?></td>
                            <td class="action-buttons">
                                <td class="action-buttons">
                                    <a href="view_booking.php?id=<?php echo htmlspecialchars($booking['id'] ?? ''); ?>" class="btn-detail">
                                        <i class="fas fa-eye"></i> <span class="btn-text">Detail</span>
                                    </a>
                                    <a href="bookings.php?action=delete&id=<?php echo htmlspecialchars($booking['id'] ?? ''); ?>" class="btn-delete" onclick="return confirm('Apakah Anda yakin ingin menghapus pemesanan ini?');">
                                        <i class="fas fa-trash-alt"></i> <span class="btn-text">Hapus</span>
                                    </a>
                                </td>
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
                } else if (currentAdminPath === 'bookings.php' || currentAdminPath.startsWith('export_bookings.php')) { // Tambahkan export_bookings.php
                    $('nav.main-nav ul li a[href="bookings.php"]').addClass('active');
                }
            });
        </script>
    </body>
    </html>