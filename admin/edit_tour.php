<?php
session_start();
ob_start();

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include_once '../config.php'; // Koneksi database PDO

$message = '';
$status_class = '';
$tour_data = null; // Variabel untuk menyimpan data tur yang akan diedit

// Ambil data tur yang akan diedit
if (isset($_GET['id'])) {
    $tour_id = intval($_GET['id']);
    try {
        $stmt = $pdo->prepare("SELECT * FROM tours WHERE id = ?");
        $stmt->execute([$tour_id]);
        $tour_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$tour_data) {
            header("Location: index.php?status=error&message=" . urlencode("Tur tidak ditemukan."));
            exit();
        }
    } catch (PDOException $e) {
        header("Location: index.php?status=error&message=" . urlencode("Error saat mengambil data tur: " . $e->getMessage()));
        exit();
    }
} else {
    header("Location: index.php?status=error&message=" . urlencode("ID tur tidak ditemukan untuk diedit."));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tour_name = trim($_POST['tour_name']);
    $description = trim($_POST['description']);
    $price = str_replace(['Rp', '.', ','], '', trim($_POST['price']));
    $duration = trim($_POST['duration']);
    $current_image = $_POST['current_image'] ?? ''; // Nama gambar yang sudah ada

    // Validasi input dasar
    if (empty($tour_name) || empty($description) || empty($price) || empty($duration)) {
        $message = "Semua field harus diisi!";
        $status_class = 'error';
    } elseif (!is_numeric($price) || $price <= 0) {
        $message = "Harga harus berupa angka positif.";
        $status_class = 'error';
    } else {
        $new_image_name = $current_image; // Defaultnya tetap gambar lama

        // LOGIKA UPLOAD GAMBAR BARU (JIKA ADA)
        if (isset($_FILES['tour_image']) && $_FILES['tour_image']['error'] == UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['tour_image']['tmp_name'];
            $file_name = $_FILES['tour_image']['name'];
            $file_size = $_FILES['tour_image']['size'];
            $file_type = $_FILES['tour_image']['type'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            $max_file_size = 5 * 1024 * 1024; // 5 MB

            if (!in_array($file_ext, $allowed_extensions)) {
                $message = "Ekstensi file tidak diizinkan. Hanya JPG, JPEG, PNG, GIF yang diperbolehkan.";
                $status_class = 'error';
            } elseif ($file_size > $max_file_size) {
                $message = "Ukuran file terlalu besar. Maksimal 5MB.";
                $status_class = 'error';
            } else {
                $upload_dir = '../uploads/';
                $new_image_name = uniqid('tour_') . '.' . $file_ext;
                $upload_path = $upload_dir . $new_image_name;

                if (move_uploaded_file($file_tmp, $upload_path)) {
                    // Berhasil upload gambar baru, hapus gambar lama jika ada
                    if (!empty($current_image) && file_exists($upload_dir . $current_image)) {
                        unlink($upload_dir . $current_image);
                    }
                } else {
                    $message = "Gagal mengupload gambar baru. Cek izin folder 'uploads'.";
                    $status_class = 'error';
                }
            }
        }

        // Lanjutkan update ke database hanya jika tidak ada error upload dan validasi input berhasil
        if ($status_class == '') {
            try {
                $stmt = $pdo->prepare("UPDATE tours SET tour_name = ?, description = ?, price = ?, duration = ?, image = ? WHERE id = ?");
                $stmt->execute([$tour_name, $description, $price, $duration, $new_image_name, $tour_id]);

                $message = "Tur berhasil diperbarui! ✨";
                $status_class = 'success';
                // Update tour_data agar form menampilkan data terbaru
                $tour_data['tour_name'] = $tour_name;
                $tour_data['description'] = $description;
                $tour_data['price'] = $price;
                $tour_data['duration'] = $duration;
                $tour_data['image'] = $new_image_name;

            } catch (PDOException $e) {
                $message = "Error saat memperbarui tur: " . htmlspecialchars($e->getMessage());
                $status_class = 'error';
                // Jika update database gagal tapi gambar baru sudah terupload, hapus gambar baru tersebut (rollback)
                if (!empty($new_image_name) && $new_image_name != $current_image && file_exists($upload_path)) {
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
    <title>Edit Tur - Admin Panel</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header class="admin-header">
        <div class="admin-navigation-top">
            <h2>Panel Admin Travel Tour Gokil! <span class="subtitle">Edit Tur.</span></h2>
            <div class="admin-actions-group">
                <a href="index.php" class="btn-admin btn-primary">Kelola Tur</a>
                <a href="bookings.php" class="btn-admin btn-primary">Kelola Pemesanan</a>
                <a href="logout.php" class="btn-admin btn-cancel">Logout</a>
            </div>
        </div>
    </header>

    <main>
        <div class="form-container">
            <h1>Edit Tur: <?php echo htmlspecialchars($tour_data['tour_name'] ?? 'Tur Tidak Ditemukan'); ?></h1>

            <?php if (!empty($message)) : ?>
                <div class="status-message <?php echo $status_class; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <?php if ($tour_data) : ?>
            <form action="edit_tour.php?id=<?php echo htmlspecialchars($tour_data['id']); ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($tour_data['image'] ?? ''); ?>">

                <div class="form-group">
                    <label for="tour_name">Nama Tur:</label>
                    <input type="text" id="tour_name" name="tour_name" required value="<?php echo htmlspecialchars($tour_data['tour_name'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="description">Deskripsi:</label>
                    <textarea id="description" name="description" required><?php echo htmlspecialchars($tour_data['description'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="price">Harga (Rp):</label>
                    <input type="text" id="price" name="price" required value="<?php echo number_format($tour_data['price'] ?? 0, 0, ',', '.'); ?>" placeholder="Contoh: 1.500.000">
                </div>
                <div class="form-group">
                    <label for="duration">Durasi (Contoh: 3 Hari 2 Malam):</label>
                    <input type="text" id="duration" name="duration" required value="<?php echo htmlspecialchars($tour_data['duration'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="tour_image">Gambar Tur Saat Ini:</label>
                    <?php if (!empty($tour_data['image']) && file_exists('../uploads/' . $tour_data['image'])): ?>
                        <img src="../uploads/<?php echo htmlspecialchars($tour_data['image']); ?>" alt="Gambar Tur" style="max-width: 200px; height: auto; display: block; margin-bottom: 10px; border-radius: 5px;">
                    <?php else: ?>
                        <p>Belum ada gambar tur.</p>
                    <?php endif; ?>
                    <label for="tour_image" style="margin-top: 15px;">Ubah Gambar Tur (Biarkan kosong jika tidak diubah):</label>
                    <input type="file" id="tour_image" name="tour_image" accept="image/*">
                    <small>Maksimal 5MB (JPG, JPEG, PNG, GIF)</small>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn-submit">Perbarui Tur</button>
                    <a href="index.php" class="btn-admin btn-cancel">Batal</a>
                </div>
            </form>
            <?php endif; ?>
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
ob_end_flush();
?>