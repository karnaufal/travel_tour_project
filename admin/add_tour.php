<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include_once '../config.php';

$message = '';
$message_type = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tour_name = trim($_POST['tour_name']);
    $description = trim($_POST['description']);
    $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);
    $duration = trim($_POST['duration']);
    $image = trim($_POST['image']); // Untuk URL gambar

    if (empty($tour_name) || empty($description) || $price === false || empty($duration)) {
        $message = "Semua kolom harus diisi dengan benar.";
        $message_type = "error";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO tours (tour_name, description, price, duration, image) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$tour_name, $description, $price, $duration, $image]);
            $_SESSION['status_message'] = "Tur baru berhasil ditambahkan!";
            $_SESSION['status_type'] = "success";
            header("Location: index.php"); // Redirect ke halaman kelola tur
            exit();
        } catch (PDOException $e) {
            $message = "Gagal menambahkan tur: " . $e->getMessage();
            $message_type = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Tur Baru - Admin Panel</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Gaya khusus untuk form */
        .admin-form-container {
            max-width: 800px;
            margin: 80px auto 40px auto; /* Margin atas disesuaikan untuk header */
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .admin-form-container h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #007bff;
            font-size: 2.5em;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }
        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group textarea {
            width: calc(100% - 22px); /* Kurangi padding dan border */
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
            box-sizing: border-box;
        }
        .form-actions {
            text-align: center;
            margin-top: 30px;
        }
        .form-actions button {
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.1em;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }
        .form-actions .btn-primary {
            background-color: #007bff;
            color: #fff;
            margin-right: 15px;
        }
        .form-actions .btn-primary:hover {
            background-color: #0056b3;
        }
        .form-actions .btn-secondary {
            background-color: #6c757d;
            color: #fff;
            text-decoration: none; /* Untuk link 'Batal' */
        }
        .form-actions .btn-secondary:hover {
            background-color: #5a6268;
        }
        /* Message Styling */
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
        }
        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
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
                    <li><a href="index.php" class="active">Kelola Tur</a></li> <li><a href="bookings.php">Kelola Pemesanan</a></li>
                    <li><a href="logout.php" class="btn-login-admin">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="admin-form-container">
        <h1>Tambah Tur Baru</h1>
        <?php if (!empty($message)): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        <form action="add_tour.php" method="POST">
            <div class="form-group">
                <label for="tour_name">Nama Tur:</label>
                <input type="text" id="tour_name" name="tour_name" required>
            </div>
            <div class="form-group">
                <label for="description">Deskripsi:</label>
                <textarea id="description" name="description" rows="5" required></textarea>
            </div>
            <div class="form-group">
                <label for="price">Harga (Rp):</label>
                <input type="number" id="price" name="price" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="duration">Durasi (contoh: 3 Hari 2 Malam):</label>
                <input type="text" id="duration" name="duration" required>
            </div>
            <div class="form-group">
                <label for="image">URL Gambar (contoh: komodo.jpg atau https://example.com/image.jpg):</label>
                <input type="text" id="image" name="image" placeholder="nama_gambar.jpg atau link_gambar_online">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-primary">Simpan Tur</button>
                <a href="index.php" class="btn-secondary">Batal</a>
            </div>
        </form>
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
            }
        });
    </script>
</body>
</html>