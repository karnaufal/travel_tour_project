<?php
// Pastikan ini diatur di awal file untuk menangani error (untuk debugging)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Pastikan file koneksi database terhubung.
// Path ke config.php dari folder admin adalah '../config.php'
include_once '../config.php';

session_start(); // Wajib: Mulai session di setiap halaman yang menggunakan session

// --- Pengecekan Session Login Admin ---
// Jika user belum login sebagai admin, redirect ke halaman login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php'); // Arahkan ke halaman login di folder admin
    exit();
}
// --- Akhir Pengecekan Session ---

// Ambil username admin dari session jika ada, default ke 'Admin'
$admin_name = $_SESSION['admin_username'] ?? 'Admin';

// --- Ambil Data Statistik untuk Dashboard ---
// Contoh: Hitung Total Tour Aktif (Sekarang kolom 'status' sudah ada)
$total_tours_aktif = 0;
try {
    $stmt_tours = $pdo->query("SELECT COUNT(*) FROM tours WHERE status = 'active'");
    $total_tours_aktif = $stmt_tours->fetchColumn();
} catch (PDOException $e) {
    error_log("Error getting total active tours: " . $e->getMessage());
}

// Contoh: Hitung Total Pemesanan (Ini sudah benar dari tabel 'bookings')
$total_pemesanan = 0;
try {
    $stmt_bookings = $pdo->query("SELECT COUNT(*) FROM bookings");
    $total_pemesanan = $stmt_bookings->fetchColumn();
} catch (PDOException $e) {
    error_log("Error getting total bookings: " . $e->getMessage());
}

// Contoh: Hitung Jumlah Admin (Sekarang dari tabel 'admins' dengan kolom 'role')
$jumlah_admin = 0;
try {
    // SESUAIKAN: Tabel dari 'users' menjadi 'admins'
    $stmt_admins = $pdo->query("SELECT COUNT(*) FROM admins WHERE role = 'admin'");
    $jumlah_admin = $stmt_admins->fetchColumn();
} catch (PDOException $e) {
    error_log("Error getting total admins: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* CSS Khusus untuk Dashboard Admin - Direvisi untuk ukuran lebih standar */
    .dashboard-container {
    max-width: 950px; /* Ubah nilai ini */
    margin: 120px auto 50px auto;
    padding: 30px;
    background-color: var(--card-bg);
    border-radius: 12px;
    box-shadow: var(--shadow-medium);
    text-align: center;
    }
    .dashboard-container h1 {
        color: var(--primary-color);
        margin-bottom: 15px;
        font-size: 2.5em; /* Dikecilkan sedikit dari 2.8em */
    }
    .dashboard-container p {
        color: var(--text-color);
        margin-bottom: 40px;
        font-size: 1em; /* Dikecilkan sedikit dari 1.1em */
    }
    .dashboard-stats {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 15px;
        margin-bottom: 60px;
    }
    .stat-card {
        background-color: #fff;
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        padding: 25px; /* Dikecilkan dari 30px */
        text-align: center;
        flex: 1 1 calc(33% - 60px);
        min-width: 260px; /* Lebar minimum disesuaikan */
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }
    .stat-card .icon {
        font-size: 3em; /* Dikecilkan dari 3.5em */
        color: var(--primary-color);
        margin-bottom: 10px; /* Dikecilkan dari 15px */
    }
    .stat-card .value {
        font-size: 2.2em; /* Dikecilkan dari 2.8em */
        font-weight: 700;
        color: var(--accent-color);
        margin-bottom: 5px; /* Dikecilkan dari 8px */
    }
    .stat-card .label {
        font-size: 1em; /* Dikecilkan dari 1.1em */
        color: var(--text-color);
        font-weight: 500;
    }

    .dashboard-container h2 {
        color: var(--secondary-color);
        margin-bottom: 30px;
        font-size: 2em; /* Dikecilkan dari 2.2em */
    }

    .dashboard-links {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 20px;
        margin-top: 30px;
    }
    .dashboard-link-item {
        background-color: var(--primary-color);
        color: white;
        padding: 18px 22px; /* Dikecilkan dari 20px 25px */
        border-radius: 8px;
        text-decoration: none;
        font-size: 1em; /* Dikecilkan dari 1.1em */
        font-weight: 600;
        transition: background-color 0.3s ease, transform 0.2s ease;
        min-width: 200px; /* Lebar minimum disesuaikan */
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .dashboard-link-item:hover {
        background-color: var(--secondary-color);
        transform: translateY(-3px);
    }

    /* Responsif untuk card statistik dan link */
    @media (max-width: 992px) {
        .stat-card {
            flex: 1 1 calc(50% - 30px);
        }
        .dashboard-link-item {
            flex: 1 1 calc(50% - 20px);
        }
        .stat-card .icon {
            font-size: 2.8em; /* Disesuaikan untuk tablet */
        }
        .stat-card .value {
            font-size: 2em; /* Disesuaikan untuk tablet */
        }
    }
    @media (max-width: 600px) {
        .stat-card {
            flex: 1 1 100%;
        }
        .dashboard-link-item {
            flex: 1 1 100%;
            font-size: 0.9em; /* Dikecilkan untuk mobile */
            padding: 15px 18px; /* Dikecilkan untuk mobile */
        }
        .dashboard-container {
            padding: 15px; /* Dikecilkan untuk mobile */
            margin: 90px auto 25px auto;
        }
        .dashboard-container h1 {
            font-size: 2em; /* Dikecilkan untuk mobile */
        }
        .dashboard-container p {
            font-size: 0.9em; /* Dikecilkan untuk mobile */
            margin-bottom: 25px;
        }
        .stat-card .icon {
            font-size: 2.5em; /* Disesuaikan untuk mobile */
        }
        .stat-card .value {
            font-size: 1.8em; /* Disesuaikan untuk mobile */
        }
        .stat-card .label {
            font-size: 0.9em; /* Disesuaikan untuk mobile */
        }
        .dashboard-container h2 {
            font-size: 1.8em; /* Disesuaikan untuk mobile */
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
                    <li><a href="edit_tour.php">Kelola Tour</a></li>
                    <li><a href="bookings.php">Kelola Pemesanan</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="dashboard-container">
        <h1>Selamat Datang, <?php echo htmlspecialchars($admin_name); ?>!</h1>
        <p>Ini adalah ringkasan aktivitas website Anda.</p>

        <div class="dashboard-stats">
            <div class="stat-card">
                <i class="fas fa-plane-departure icon"></i>
                <div class="value"><?php echo $total_tours_aktif; ?></div>
                <div class="label">Total Tour Aktif</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-book-open icon"></i>
                <div class="value"><?php echo $total_pemesanan; ?></div>
                <div class="label">Total Pemesanan</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-users-cog icon"></i>
                <div class="value"><?php echo $jumlah_admin; ?></div>
                <div class="label">Jumlah Admin</div>
            </div>
        </div>

        <h2>Aksi Cepat</h2>
        <div class="dashboard-links">
            <a href="add_tour.php" class="dashboard-link-item">
                <i class="fas fa-plus-circle"></i> &nbsp; Tambah Tour Baru
            </a>
            <a href="edit_tour.php" class="dashboard-link-item">
                <i class="fas fa-edit"></i> &nbsp; Kelola Tour
            </a>
            <a href="bookings.php" class="dashboard-link-item">
                <i class="fas fa-receipt"></i> &nbsp; Lihat Pemesanan
            </a>
        </div>
    </div>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> JalanJalan Kuy! Admin Panel. All rights reserved.</p>
        <p style="font-size: 0.8em; margin-top: 5px;">Dibuat Karnaufal</p>
    </footer>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $('nav.main-nav ul li a[href="dashboard.php"]').addClass('active');
            $('nav.main-nav ul li a').not('[href="dashboard.php"]').removeClass('active');
        });
    </script>
</body>
</html>