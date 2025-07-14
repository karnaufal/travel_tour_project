<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include_once '../config.php'; // Pastikan path ke config.php benar

$tour = null;
$message = '';
$message_type = '';

// Ambil data tur yang akan diedit berdasarkan ID dari URL
if (isset($_GET['id'])) {
    $tour_id = intval($_GET['id']); // Pastikan ID adalah integer
    try {
        $stmt = $pdo->prepare("SELECT * FROM tours WHERE id = ?");
        $stmt->execute([$tour_id]);
        $tour = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$tour) {
            $_SESSION['status_message'] = "Tur tidak ditemukan!";
            $_SESSION['status_type'] = "error";
            header("Location: index.php");
            exit();
        }
    } catch (PDOException $e) {
        $message = "Error mengambil data tur: " . $e->getMessage();
        $message_type = "error";
    }
} else {
    // Jika tidak ada ID di URL, redirect
    header("Location: index.php");
    exit();
}

// Proses update tur saat form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST" && $tour) {
    $tour_name = trim($_POST['tour_name']);
    $description = trim($_POST['description']);
    $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);
    $duration = trim($_POST['duration']);
    $current_image = trim($_POST['current_image']); // Ambil nama gambar lama dari hidden input

    $image_filename_to_db = $current_image; // Default: gunakan gambar lama

    // --- BAGIAN BARU UNTUK UPLOAD GAMBAR ---
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        // Pastikan nama file aman dari karakter aneh
        $fileName = preg_replace("/[^a-zA-Z0-9.-]/", "_", $fileName);

        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Buat nama file unik
        $newFileName = uniqid('tour_', true) . '.' . $fileExtension;

        $uploadFileDir = '../images/'; // Direktori upload relatif dari admin/
        $destPath = $uploadFileDir . $newFileName;

        // Pastikan direktori ada dan bisa ditulis
        if (!is_dir($uploadFileDir)) {
            if (!mkdir($uploadFileDir, 0777, true)) {
                $message = "Gagal membuat direktori upload: " . $uploadFileDir;
                $message_type = "error";
                goto end_process;
            }
        }

        if (move_uploaded_file($fileTmpPath, $destPath)) {
            $image_filename_to_db = $newFileName; // Gunakan nama file baru
            // Hapus gambar lama jika ada dan bukan gambar default
            if (!empty($current_image) && file_exists($uploadFileDir . $current_image) && $current_image !== 'default-hero-bg.jpg') { // Sesuaikan nama default jika ada
                unlink($uploadFileDir . $current_image);
            }
        } else {
            $message = "Gagal mengunggah gambar baru. Pastikan folder 'images' memiliki izin tulis.";
            $message_type = "error";
            // Jika upload gambar baru gagal, tetap lanjutkan dengan gambar lama
        }
    } else if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        // Tangani error upload lainnya (selain tidak ada file yang dipilih)
        $message = "Terjadi kesalahan saat mengunggah gambar baru. Kode error: " . $_FILES['image']['error'];
        $message_type = "error";
        // Tetap lanjutkan dengan gambar lama
    }
    // ----------------------------------------------------

    if (empty($tour_name) || empty($description) || $price === false || empty($duration)) {
        $message = "Semua kolom harus diisi dengan benar.";
        $message_type = "error";
    } else {
        try {
            // Update data tur dengan nama gambar yang sesuai (lama atau baru)
            $stmt = $pdo->prepare("UPDATE tours SET tour_name = ?, description = ?, price = ?, duration = ?, image = ? WHERE id = ?");
            $stmt->execute([$tour_name, $description, $price, $duration, $image_filename_to_db, $tour['id']]);

            $_SESSION['status_message'] = "Tur berhasil diperbarui!";
            $_SESSION['status_type'] = "success";
            header("Location: index.php");
            exit();
        } catch (PDOException $e) {
            $message = "Gagal memperbarui tur: " . $e->getMessage();
            $message_type = "error";
            // Jika database error, dan ada gambar baru yang terupload, hapus gambar baru tersebut
            if ($image_filename_to_db !== $current_image && file_exists($destPath)) {
                unlink($destPath);
            }
        }
    }
}
end_process: // Label untuk goto
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Tur - Admin Panel</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Gaya khusus untuk form */
        .admin-form-container {
            max-width: 800px;
            margin: 80px auto 40px auto;
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
            width: calc(100% - 22px);
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
            text-decoration: none;
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
                    <li><a href="index.php" class="active">Kelola Tur</a></li>
                    <li><a href="bookings.php">Kelola Pemesanan</a></li>
                    <li><a href="logout.php" class="btn-login-admin">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="admin-form-container">
        <h1>Edit Tur</h1>
        <?php if (!empty($message)): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        <?php if ($tour): ?>
        <form action="edit_tour.php?id=<?php echo htmlspecialchars($tour['id']); ?>" method="POST" enctype="multipart/form-data"> <div class="form-group">
                <label for="tour_name">Nama Tur:</label>
                <input type="text" id="tour_name" name="tour_name" value="<?php echo htmlspecialchars($tour['tour_name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Deskripsi:</label>
                <textarea id="description" name="description" rows="5" required><?php echo htmlspecialchars($tour['description']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="price">Harga (Rp):</label>
                <input type="number" id="price" name="price" step="0.01" value="<?php echo htmlspecialchars($tour['price']); ?>" required>
            </div>
            <div class="form-group">
                <label for="duration">Durasi (contoh: 3 Hari 2 Malam):</label>
                <input type="text" id="duration" name="duration" value="<?php echo htmlspecialchars($tour['duration']); ?>" required>
            </div>
            <div class="form-group">
                <label>Gambar Saat Ini:</label><br>
                <?php if (!empty($tour['image'])): ?>
                    <img src="../images/<?php echo htmlspecialchars($tour['image']); ?>" alt="Gambar Tur" style="max-width: 200px; height: auto; display: block; margin-bottom: 10px;"> <p style="font-size: 0.9em; color: #666;">File: <?php echo htmlspecialchars($tour['image']); ?></p>
                <?php else: ?>
                    <p>Tidak ada gambar saat ini.</p>
                <?php endif; ?>
                <label for="image" style="margin-top: 15px;">Unggah Gambar Baru (biarkan kosong jika tidak ingin mengubah):</label>
                <input type="file" id="image" name="image" accept="image/*"> <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($tour['image']); ?>"> </div>
            <div class="form-actions">
                <button type="submit" class="btn-primary">Perbarui Tur</button>
                <a href="index.php" class="btn-secondary">Batal</a>
            </div>
        </form>
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
            }
        });
    </script>
</body>
</html>