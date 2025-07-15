<?php
// Pastikan file koneksi database terhubung.
// Karena admin_inquiries.php sekarang di dalam folder 'admin',
// maka untuk mengakses config.php yang ada di folder root, kita perlu '..'
include_once '../config.php'; 

// Fungsi untuk mengecek apakah user sudah login sebagai admin (opsional tapi sangat direkomendasikan)
// Untuk saat ini kita abaikan dulu, tapi nanti bisa ditambahkan session/authentication di sini.
// Contoh sederhana:
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Redirect ke halaman login admin jika belum login
    // header('Location: admin_login.php'); // Buat file ini nanti jika belum ada
    // exit();
}

$inquiries = [];
$error_message = ''; // Inisialisasi variabel error_message

try {
    $stmt = $pdo->query("SELECT * FROM inquiries ORDER BY inquiry_date DESC");
    $inquiries = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error in admin_inquiries.php: " . $e->getMessage());
    $error_message = "Terjadi kesalahan saat mengambil data pemesanan. Silakan coba lagi nanti.";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Daftar Pemesanan Tour</title>
    <link rel="stylesheet" href="../css/style.css"> 
    <style>
        /* CSS khusus untuk halaman admin ini (bisa dipindahkan ke style.css jika mau) */
        .admin-container {
            max-width: 1200px;
            /* Sesuaikan margin-top agar tidak tertutup header fixed */
            margin: 120px auto 50px auto; 
            padding: 20px;
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-medium);
        }
        .admin-container h1 {
            text-align: center;
            color: var(--primary-color);
            margin-bottom: 30px;
        }
        .inquiry-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .inquiry-table th, .inquiry-table td {
            border: 1px solid var(--border-color);
            padding: 12px 15px;
            text-align: left;
            font-size: 0.95em;
        }
        .inquiry-table th {
            background-color: var(--secondary-color);
            color: white;
            font-weight: 600;
        }
        .inquiry-table tr:nth-child(even) {
            background-color: var(--light-bg);
        }
        .inquiry-table tr:hover {
            background-color: #f0f0f0;
        }
        .inquiry-table td.message-cell {
            max-width: 250px; /* Sesuaikan lebar kolom pesan */
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap; 
        }
        .inquiry-table td.message-cell:hover {
            white-space: normal; 
            overflow: visible;
            /* Tambahkan background agar lebih terlihat saat hover dan teks panjang */
            background-color: #e9e9e9; 
            position: relative; /* Penting untuk z-index jika ada konten di bawah */
            z-index: 10; /* Pastikan muncul di atas elemen lain */
        }
        .no-data {
            text-align: center;
            padding: 30px;
            color: var(--light-text-color);
            font-size: 1.1em;
        }
        .error-message {
            color: #d9534f; /* Merah untuk error */
            background-color: #f2dede;
            border: 1px solid #ebccd1;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: var(--border-radius-small);
            text-align: center;
        }

        /* Responsif Tabel */
        @media (max-width: 768px) {
            .inquiry-table, .inquiry-table thead, .inquiry-table tbody, .inquiry-table th, .inquiry-table td, .inquiry-table tr {
                display: block;
            }
            .inquiry-table thead tr {
                position: absolute;
                top: -9999px;
                left: -9999px;
            }
            .inquiry-table tr {
                margin-bottom: 15px;
                border: 1px solid var(--border-color);
                border-radius: var(--border-radius);
                box-shadow: var(--shadow-light);
            }
            .inquiry-table td {
                border: none;
                border-bottom: 1px solid #eee;
                position: relative;
                padding-left: 50%;
                text-align: right;
            }
            .inquiry-table td:before {
                content: attr(data-label);
                position: absolute;
                left: 6px;
                width: 45%;
                padding-right: 10px;
                white-space: nowrap;
                text-align: left;
                font-weight: 600;
                color: var(--primary-color);
            }
            .inquiry-table td.message-cell {
                max-width: none; /* Izinkan teks wrap penuh di mobile */
                white-space: normal;
                text-overflow: clip;
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
                    <li><a href="../index.php">Home</a></li>
                    <li><a href="../paket_tur.php">Paket Tour</a></li>
                    <li><a href="../tentang_kami.php">Tentang Kami</a></li>
                    <li><a href="../kontak.php">Kontak</a></li>
                    <!-- <li><a href="admin_inquiries.php" class="active">Admin</a></li>  -->
                </ul>
            </nav>
        </div>
    </header>

    <div class="admin-container">
        <h1>Daftar Pemesanan Tour Masuk</h1>

        <?php if (!empty($error_message)): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <?php if (empty($inquiries)): ?>
            <p class="no-data">Belum ada pemesanan tour masuk.</p>
        <?php else: ?>
            <table class="inquiry-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tour ID</th>
                        <th>Nama Tour</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th>Tanggal Diinginkan</th>
                        <th>Peserta</th>
                        <th>Pesan</th>
                        <th>Tanggal Pemesanan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inquiries as $inquiry): ?>
                        <tr>
                            <td data-label="ID"><?php echo htmlspecialchars($inquiry['id'] ?? ''); ?></td>
                            <td data-label="Tour ID"><?php echo htmlspecialchars($inquiry['tour_id'] ?? ''); ?></td>
                            <td data-label="Nama Tour"><?php echo htmlspecialchars($inquiry['tour_name'] ?? ''); ?></td>
                            <td data-label="Nama"><?php echo htmlspecialchars($inquiry['customer_name'] ?? ''); ?></td> <td data-label="Email"><?php echo htmlspecialchars($inquiry['customer_email'] ?? ''); ?></td> <td data-label="Telepon"><?php echo htmlspecialchars($inquiry['customer_phone'] ?? ''); ?></td> <td data-label="Tgl Diinginkan"><?php echo htmlspecialchars($inquiry['preferred_date'] ?? ''); ?></td>
                            <td data-label="Peserta"><?php echo htmlspecialchars($inquiry['participants'] ?? ''); ?></td>
                            <td data-label="Pesan" class="message-cell" title="<?php echo htmlspecialchars($inquiry['message'] ?? ''); ?>">
                    <?php echo htmlspecialchars($inquiry['message'] ?? ''); ?>
                    </td>
                            <td data-label="Tgl Pemesanan"><?php echo htmlspecialchars($inquiry['inquiry_date'] ?? ''); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        // Logika untuk menandai navigasi aktif di halaman admin
        $(document).ready(function() {
            $('nav.main-nav ul li a[href="admin_inquiries.php"]').addClass('active');
        });
    </script>
</body>
</html>