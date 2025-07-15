<?php
session_start();
// Pastikan admin sudah login, jika tidak, arahkan kembali ke halaman login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include_once '../config.php'; // Sesuaikan path jika config.php ada di luar folder admin

// Anda bisa menambahkan logika untuk mengambil data ringkasan di sini
// Contoh: Jumlah total tur, jumlah pemesanan, dll.
try {
    $total_tours_stmt = $pdo->query("SELECT COUNT(*) FROM tours");
    $total_tours = $total_tours_stmt->fetchColumn();

    $total_bookings_stmt = $pdo->query("SELECT COUNT(*) FROM bookings");
    $total_bookings = $total_bookings_stmt->fetchColumn();

    // Tambahkan data lain yang mungkin ingin ditampilkan
    // Misalnya, tur terbaru, pemesanan terbaru, dll.

} catch (PDOException $e) {
    // Tangani error database jika perlu
    $total_tours = 0;
    $total_bookings = 0;
    // echo "Error: " . $e->getMessage(); // Untuk debugging
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - JalanJalan Kuy!</title>
    <link rel="stylesheet" href="../css/style.css"> <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Gaya khusus untuk Dashboard Admin */
        body {
            background-color: #f4f7f6;
        }
        .admin-dashboard-container {
            max-width: 1200px;
            margin: 80px auto 40px auto; /* Sesuaikan margin atas agar tidak tertutup header */
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .dashboard-header {
            text-align: center;
            margin-bottom: 40px;
            color: #333;
        }
        .dashboard-header h1 {
            font-size: 2.8em;
            margin-bottom: 10px;
            color: #007bff;
        }
        .dashboard-header p {
            font-size: 1.1em;
            color: #555;
        }
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }
        .stat-card {
            background-color: #e9f5ff; /* Warna latar belakang kartu stat */
            border-left: 5px solid #007bff;
            border-radius: 8px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }
        .stat-card .icon {
            font-size: 3em;
            color: #007bff;
            margin-bottom: 15px;
        }
        .stat-card h3 {
            font-size: 2em;
            margin-bottom: 10px;
            color: #333;
        }
        .stat-card p {
            font-size: 1.1em;
            color: #666;
        }
        .dashboard-quick-links {
            text-align: center;
            margin-bottom: 50px;
        }
        .dashboard-quick-links h2 {
            font-size: 2em;
            margin-bottom: 30px;
            color: #007bff;
        }
        .quick-links-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }
        .quick-link-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 15px 30px;
            background-color: #28a745; /* Warna hijau untuk tombol aksi */
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
            font-size: 1.1em;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }
        .quick-link-btn i {
            margin-right: 10px;
            font-size: 1.2em;
        }
        .quick-link-btn:hover {
            background-color: #218838;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .admin-dashboard-container {
                margin: 60px 15px 30px 15px;
                padding: 15px;
            }
            .dashboard-header h1 {
                font-size: 2.2em;
            }
            .dashboard-stats {
                grid-template-columns: 1fr;
            }
            .stat-card {
                padding: 20px;
            }
            .stat-card .icon {
                font-size: 2.5em;
            }
            .stat-card h3 {
                font-size: 1.8em;
            }
            .quick-link-btn {
                width: 100%;
                margin-bottom: 15px;
            }
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
                    <li><a href="dashboard.php" class="active">Dashboard</a></li>
                    <li><a href="index.php">Kelola Tour</a></li>
                    <li><a href="bookings.php">Kelola Pemesanan</a></li>
                    <li><a href="logout.php" class="btn-login-admin">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="admin-dashboard-container">
        <div class="dashboard-header">
            <h1>Selamat Datang, Admin!</h1>
            <p>Ini adalah ringkasan aktivitas di website Anda.</p>
        </div>

        <div class="dashboard-stats">
            <div class="stat-card">
                <div class="icon"><i class="fas fa-plane-departure"></i></div>
                <h3><?php echo $total_tours; ?></h3>
                <p>Total Tour Aktif</p>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-ticket-alt"></i></div>
                <h3><?php echo $total_bookings; ?></h3>
                <p>Total Pemesanan</p>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-users"></i></div>
                <h3>2</h3> <p>Jumlah Admin</p>
            </div>
        </div>

        <div class="dashboard-quick-links">
            <h2>Aksi Cepat</h2>
            <div class="quick-links-grid">
                <a href="add_tour.php" class="quick-link-btn"><i class="fas fa-plus-circle"></i> Tambah Tour Baru</a>
                <a href="index.php" class="quick-link-btn"><i class="fas fa-map-marked-alt"></i> Lihat Semua Tour</a>
                <a href="bookings.php" class="quick-link-btn"><i class="fas fa-book"></i> Lihat Pemesanan</a>
                </div>
        </div>
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
            if (currentAdminPath === 'dashboard.php' || currentAdminPath === '') {
                $('nav.main-nav ul li a[href="dashboard.php"]').addClass('active');
            } else if (currentAdminPath === 'index.php' || currentAdminPath.startsWith('edit_tour.php')) { // index.php (kelola tur)
                $('nav.main-nav ul li a[href="index.php"]').addClass('active');
            } else if (currentAdminPath === 'bookings.php') {
                $('nav.main-nav ul li a[href="bookings.php"]').addClass('active');
            }
            // Tambahkan kondisi lain jika ada halaman navigasi admin baru
        });
    </script>
</body>
</html>