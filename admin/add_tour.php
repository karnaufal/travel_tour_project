<?php
session_start();
ob_start(); // Mulai output buffering

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include_once '../config.php'; // Koneksi database PDO

$message = '';
$status_class = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tour_name = trim($_POST['tour_name']);
    $description = trim($_POST['description']);
    $price = str_replace(['Rp', '.', ','], '', trim($_POST['price'])); // Hapus format Rp dan titik/koma
    $duration = trim($_POST['duration']);
    $image_name = ''; // Inisialisasi nama gambar

    // Validasi input dasar
    if (empty($tour_name) || empty($description) || empty($price) || empty($duration)) {
        $message = "Semua field harus diisi!";
        $status_class = 'error';
    } elseif (!is_numeric($price) || $price <= 0) {
        $message = "Harga harus berupa angka positif.";
        $status_class = 'error';
    } else {
        // === LOGIKA UPLOAD GAMBAR BARU ===
        if (isset($_FILES['tour_image']) && $_FILES['tour_image']['error'] == UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['tour_image']['tmp_name'];
            $file_name = $_FILES['tour_image']['name'];
            $file_size = $_FILES['tour_image']['size'];
            $file_type = $_FILES['tour_image']['type'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif']; // Ekstensi yang diizinkan
            $max_file_size = 5 * 1024 * 1024; // 5 MB

            if (!in_array($file_ext, $allowed_extensions)) {
                $message = "Ekstensi file tidak diizinkan. Hanya JPG, JPEG, PNG, GIF yang diperbolehkan.";
                $status_class = 'error';
            } elseif ($file_size > $max_file_size) {
                $message = "Ukuran file terlalu besar. Maksimal 5MB.";
                $status_class = 'error';
            } else {
                // Buat nama file unik untuk menghindari konflik
                $image_name = uniqid('tour_') . '.' . $file_ext;
                $upload_dir = '../uploads/'; // Pastikan folder 'uploads' ada di root proyek

                // Buat folder uploads jika belum ada
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true); // Izin 0777 untuk development, ganti 0755/0775 di produksi
                }

                $upload_path = $upload_dir . $image_name;

                if (move_uploaded_file($file_tmp, $upload_path)) {
                    // File berhasil di-upload, lanjutkan simpan ke database
                } else {
                    $message = "Gagal mengupload gambar. Cek izin folder 'uploads'.";
                    $status_class = 'error';
                }
            }
        } else {
            // Jika tidak ada file di-upload, ini bisa jadi error atau memang tidak wajib (tergantung kebutuhan)
            // Untuk saat ini, kita anggap kalau error_ok berarti ada file yg diupload.
            // Jika gambar wajib, bisa di tambahkan:
            // $message = "Gambar tur wajib diupload.";
            // $status_class = 'error';
        }

        // Lanjutkan simpan ke database hanya jika tidak ada error upload dan validasi input berhasil
        if ($status_class == '') { // Jika tidak ada error dari validasi input atau upload
            try {
                $stmt = $pdo->prepare("INSERT INTO tours (tour_name, description, price, duration, image) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$tour_name, $description, $price, $duration, $image_name]);

                $message = "Tur baru berhasil ditambahkan! 🎉";
                $status_class = 'success';
                // Redirect ke index.php setelah sukses
                header("Location: index.php?status=success&message=" . urlencode($message));
                exit();
            } catch (PDOException $e) {
                $message = "Error saat menambahkan tur: " . htmlspecialchars($e->getMessage());
                $status_class = 'error';
                // Jika ada error database, hapus file yang sudah terupload (opsional tapi bagus)
                if (!empty($image_name) && file_exists($upload_path)) {
                    unlink($upload_path);
                }
            }
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
    <style>
        /* Tambahan style khusus untuk form jika diperlukan */
        .form-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 30px auto;
        }
        .form-container h1 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }
        .form-group input[type="text"],
        .form-group textarea,
        .form-group input[type="number"],
        .form-group input[type="file"] { /* Tambahkan input[type="file"] */
            width: calc(100% - 20px); /* Sesuaikan padding */
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
            box-sizing: border-box; /* Penting agar padding tidak menambah lebar */
        }
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 25px;
        }
        .btn-submit, .btn-cancel {
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            font-size: 1em;
            cursor: pointer;
            transition: background-color 0.3s ease;
            text-decoration: none; /* Untuk btn-cancel */
            text-align: center;
        }
        .btn-submit {
            background-color: #007bff;
            color: white;
        }
        .btn-submit:hover {
            background-color: #0056b3;
        }
        .btn-cancel {
            background-color: #6c757d;
            color: white;
        }
        .btn-cancel:hover {
            background-color: #5a6268;
        }
        .status-message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 1em;
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
    <header class="admin-header">
        <div class="admin-navigation-top">
            <h2>Panel Admin Travel Tour Gokil! <span class="subtitle">Tambah Tur Baru.</span></h2>
            <div class="admin-actions-group">
                <a href="index.php" class="btn-admin btn-primary">Kelola Tur</a>
                <a href="bookings.php" class="btn-admin btn-primary">Kelola Pemesanan</a>
                <a href="logout.php" class="btn-admin btn-cancel">Logout</a>
            </div>
        </div>
    </header>

    <main>
        <div class="form-container">
            <h1>Tambah Tur Baru</h1>

            <?php if (!empty($message)) : ?>
                <div class="status-message <?php echo $status_class; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form action="add_tour.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="tour_name">Nama Tur:</label>
                    <input type="text" id="tour_name" name="tour_name" required value="<?php echo htmlspecialchars($_POST['tour_name'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="description">Deskripsi:</label>
                    <textarea id="description" name="description" required><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="price">Harga (Rp):</label>
                    <input type="text" id="price" name="price" required value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>" placeholder="Contoh: 1.500.000">
                </div>
                <div class="form-group">
                    <label for="duration">Durasi (Contoh: 3 Hari 2 Malam):</label>
                    <input type="text" id="duration" name="duration" required value="<?php echo htmlspecialchars($_POST['duration'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="tour_image">Gambar Tur:</label>
                    <input type="file" id="tour_image" name="tour_image" accept="image/*">
                    <small>Maksimal 5MB (JPG, JPEG, PNG, GIF)</small>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-submit">Simpan Tur</button>
                    <a href="index.php" class="btn-cancel">Batal</a>
                </div>
            </form>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Travel Tour Gokil. Admin Panel.</p>
    </footer>

    <script>
        // Script untuk format harga (opsional, bisa dipindahkan ke file JS terpisah)
        document.addEventListener('DOMContentLoaded', function() {
            var priceInput = document.getElementById('price');
            priceInput.addEventListener('input', function(e) {
                var value = e.target.value.replace(/\D/g, ''); // Hapus semua non-digit
                if (value) {
                    e.target.value = new Intl.NumberFormat('id-ID').format(value); // Format sebagai mata uang ID
                }
            });
        });
    </script>
</body>
</html>
<?php
ob_end_flush(); // Akhiri output buffering
?>