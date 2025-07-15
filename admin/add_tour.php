<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include_once '../config.php'; // Pastikan path ke config.php benar

$message = '';
$message_type = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tour_name = trim($_POST['tour_name']);
    $description = trim($_POST['description']);
    $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);
    $duration = trim($_POST['duration']);

    // --- BAGIAN BARU UNTUK UPLOAD GAMBAR ---
    $image_filename = ''; // Default jika tidak ada upload atau ada masalah

    // Cek apakah ada file gambar yang diupload dan tidak ada error
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        // Pastikan nama file aman dari karakter aneh
        $fileName = preg_replace("/[^a-zA-Z0-9.-]/", "_", $fileName);

        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Buat nama file unik untuk mencegah konflik
        $newFileName = uniqid('tour_', true) . '.' . $fileExtension;

        // Direktori tempat file akan disimpan, relatif dari add_tour.php (yang ada di folder admin/)
        $uploadFileDir = '../images/'; 
        $destPath = $uploadFileDir . $newFileName;

        // Pastikan direktori upload ada dan bisa ditulis
        if (!is_dir($uploadFileDir)) {
            if (!mkdir($uploadFileDir, 0777, true)) { // Buat direktori jika tidak ada
                $message = "Gagal membuat direktori upload: " . $uploadFileDir;
                $message_type = "error";
                goto end_process; // Langsung ke akhir jika gagal membuat direktori
            }
        }

        // Pindahkan file yang diupload dari lokasi sementara ke lokasi tujuan
        if (move_uploaded_file($fileTmpPath, $destPath)) {
            $image_filename = $newFileName; // Simpan nama file yang berhasil diupload
        } else {
            $message = "Gagal mengunggah gambar. Pastikan folder 'images' memiliki izin tulis (write permissions).";
            $message_type = "error";
            goto end_process; // Langsung ke akhir jika upload gagal
        }
    } else if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        // Tangani error upload selain 'tidak ada file' (misal: ukuran file terlalu besar)
        $message = "Terjadi kesalahan saat mengunggah gambar. Kode error: " . $_FILES['image']['error'];
        $message_type = "error";
        goto end_process;
    } else {
        // Jika tidak ada file yang dipilih sama sekali, atau form disubmit tanpa file
        // Karena gambar wajib, ini dianggap error
        $message = "Gambar tur harus diunggah.";
        $message_type = "error";
        goto end_process;
    }
    // ----------------------------------------------------

    // Validasi data input lainnya dan nama file gambar yang sudah didapat
    if (empty($tour_name) || empty($description) || $price === false || empty($duration) || empty($image_filename)) {
        $message = "Semua kolom harus diisi dengan benar, termasuk gambar.";
        $message_type = "error";
    } else {
        try {
            // Persiapkan statement SQL untuk memasukkan data tur
            $stmt = $pdo->prepare("INSERT INTO tours (tour_name, description, price, duration, image) VALUES (?, ?, ?, ?, ?)");
            // Eksekusi statement dengan data yang sudah divalidasi dan nama file gambar
            $stmt->execute([$tour_name, $description, $price, $duration, $image_filename]);

            // Set pesan sukses dan redirect ke halaman index admin
            $_SESSION['status_message'] = "Tur baru berhasil ditambahkan!";
            $_SESSION['status_type'] = "success";
            header("Location: index.php");
            exit();
        } catch (PDOException $e) {
            // Tangani error database
            $message = "Gagal menambahkan tur: " . $e->getMessage();
            $message_type = "error";
            // Opsional: Hapus file yang sudah diupload jika database error
            if (!empty($image_filename) && file_exists($destPath)) {
                unlink($destPath);
            }
        }
    }
}
end_process: // Label untuk goto, digunakan untuk melompat jika ada error diawal
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Tour Baru - Admin Panel</title>
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
        .form-group textarea,
        .form-group input[type="file"] { /* Tambahkan input[type="file"] di sini */
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
                    <li><a href="index.php" class="active">Kelola Tour</a></li>
                    <li><a href="bookings.php">Kelola Pemesanan</a></li>
                    <li><a href="logout.php" class="btn-login-admin">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="admin-form-container">
        <h1>Tambah Tour Baru</h1>
        <?php if (!empty($message)): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        <form action="add_tour.php" method="POST" enctype="multipart/form-data"> <div class="form-group">
                <label for="tour_name">Nama Tour:</label>
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
                <label for="image">Gambar Tour:</label> <input type="file" id="image" name="image" accept="image/*" required> </div>
            <div class="form-actions">
                <button type="submit" class="btn-primary">Simpan Tour</button>
                <a href="index.php" class="btn-secondary">Batal</a>
            </div>
        </form>
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