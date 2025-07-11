<?php
// PASTIKAN BARIS INI ADALAH BARIS PERTAMA DAN TIDAK ADA SPASI/KARAKTER LAIN DI DEPANNYA
ob_start(); // Mulai output buffering. Ini penting untuk mencegah masalah header redirect.

include '../config.php'; // Koneksi database

error_reporting(E_ALL); // Aktifkan semua laporan error
ini_set('display_errors', 1); // Tampilkan error di browser (untuk debugging)

$message = ''; // Untuk pesan sukses/error
$status_class = ''; // Untuk styling pesan
$tour = null; // Data tur yang akan diedit
$current_tour_id = 0; // Inisialisasi variabel untuk ID tur yang sedang diedit

// Tentukan ID tur yang sedang diedit, baik dari GET (saat pertama kali buka)
// atau dari POST (saat form disubmit)
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $current_tour_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
} elseif (isset($_POST['id']) && !empty($_POST['id'])) {
    $current_tour_id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
}

// Jika tidak ada ID yang valid, redirect atau tampilkan error
if ($current_tour_id <= 0) {
    header("Location: index.php?status=error&message=" . urlencode("ID tur tidak valid atau tidak ditemukan."));
    exit();
}

// Ambil data tur dari database HANYA JIKA INI BUKAN SUBMIT FORM DENGAN ERROR VALIDASI
// Atau jika ini adalah request GET awal
if ($_SERVER["REQUEST_METHOD"] == "GET" || ($_SERVER["REQUEST_METHOD"] == "POST" && $status_class == '')) { // Jika GET atau POST tapi belum ada error validasi
    try {
        $stmt = $pdo->prepare("SELECT * FROM tours WHERE id = :id");
        $stmt->bindParam(':id', $current_tour_id, PDO::PARAM_INT);
        $stmt->execute();
        $tour = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$tour) {
            header("Location: index.php?status=error&message=" . urlencode("Tur dengan ID {$current_tour_id} tidak ditemukan."));
            exit();
        }
    } catch (PDOException $e) {
        header("Location: index.php?status=error&message=" . urlencode("Error saat mengambil data tur: " . htmlspecialchars($e->getMessage())));
        exit();
    }
}

// Proses form jika ada data yang dikirim (saat user klik 'Simpan Perubahan')
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil dan sanitasi data dari form (termasuk dari POST)
    $tour_name = filter_var($_POST['tour_name'] ?? '', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $description = filter_var($_POST['description'] ?? '', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $price = filter_var($_POST['price'] ?? '', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $duration = filter_var($_POST['duration'] ?? '', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $image_url = filter_var($_POST['image_url'] ?? '', FILTER_SANITIZE_URL);

    // Validasi sederhana
    $errors = [];
    if (empty($tour_name)) $errors[] = "Nama Tur wajib diisi.";
    if (empty($description)) $errors[] = "Deskripsi wajib diisi.";
    if (empty($price) || !is_numeric($price) || $price <= 0) $errors[] = "Harga tidak valid atau kosong.";
    if (empty($duration)) $errors[] = "Durasi wajib diisi.";
    if (empty($image_url)) $errors[] = "URL Gambar wajib diisi.";
    if (!empty($image_url) && !filter_var($image_url, FILTER_VALIDATE_URL) && !preg_match('/^images\//', $image_url)) {
        $errors[] = "URL Gambar tidak valid. Harus URL lengkap (contoh: http://...) atau path relatif dari folder 'images/' (contoh: images/nama_gambar.jpg).";
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE tours SET tour_name = :tour_name, description = :description, price = :price, duration = :duration, image_url = :image_url WHERE id = :id");

            $stmt->bindParam(':tour_name', $tour_name);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':duration', $duration);
            $stmt->bindParam(':image_url', $image_url);
            $stmt->bindParam(':id', $current_tour_id, PDO::PARAM_INT); // Gunakan $current_tour_id di sini

            $stmt->execute();

            // Redirect ke halaman admin/index.php dengan pesan sukses
            header("Location: index.php?status=success&message=" . urlencode("Tur '{$tour_name}' berhasil diperbarui!"));
            exit();

        } catch (PDOException $e) {
            $message = "Error saat menyimpan perubahan tur: " . htmlspecialchars($e->getMessage());
            $status_class = 'error';
        }
    } else {
        $message = "Validasi Gagal: <br>" . implode("<br>", $errors);
        $status_class = 'error';
        // Jika ada error validasi, isi kembali $tour dengan data POST agar form tidak kosong
        // Ini penting agar data yang diinput user tidak hilang jika ada error
        $tour = [
            'id' => $current_tour_id, // Pastikan ID tetap mengacu pada yang dari POST/GET
            'tour_name' => $tour_name,
            'description' => $description,
            'price' => $price,
            'duration' => $duration,
            'image_url' => $image_url
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Edit Tur</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1>Panel Admin Travel Tour Gokil! ⚙️</h1>
        <p>Edit Tur</p>
    </header>

    <main class="admin-form-container">
        <h1>Edit Tur</h1>

        <?php if (!empty($message)): ?>
            <div class="form-status-message <?php echo $status_class; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form action="edit_tour.php" method="POST">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($tour['id'] ?? ''); ?>">
            <div class="form-group">
                <label for="tour_name">Nama Tur:</label>
                <input type="text" id="tour_name" name="tour_name" required value="<?php echo htmlspecialchars($tour['tour_name'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="description">Deskripsi:</label>
                <textarea id="description" name="description" required><?php echo htmlspecialchars($tour['description'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label for="price">Harga (Rp):</label>
                <input type="number" id="price" name="price" step="any" required value="<?php echo htmlspecialchars($tour['price'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="duration">Durasi (contoh: 3 Hari 2 Malam):</label>
                <input type="text" id="duration" name="duration" required value="<?php echo htmlspecialchars($tour['duration'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label for="image_url">URL Gambar (contoh: images/komodo.jpg atau https://example.com/image.jpg):</label>
                <input type="text" id="image_url" name="image_url" required value="<?php echo htmlspecialchars($tour['image_url'] ?? ''); ?>">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-submit-admin">Simpan Perubahan</button>
                <a href="index.php" class="btn-cancel-admin">Batal</a>
            </div>
        </form>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Travel Tour Gokil. Admin Panel.</p>
    </footer>
</body>
</html>
<?php
ob_end_flush(); // Akhiri output buffering
?>