<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

ob_start(); // Kalau kamu pakai ob_start()
include_once '../config.php';
// ... sisa kode add_tour.php ...

include_once '../config.php'; // Koneksi database

$message = ''; // Untuk pesan sukses/error
$status_class = ''; // Untuk styling pesan

// Default values for form fields (useful if validation fails)
$tour_name = $_POST['tour_name'] ?? '';
$description = $_POST['description'] ?? '';
$price = $_POST['price'] ?? '';
$duration = $_POST['duration'] ?? '';
$image_url = $_POST['image_url'] ?? '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil dan sanitasi data dari form
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
    // Tambahan validasi URL gambar: harus URL lengkap atau path relatif dari folder 'images/'
    if (!empty($image_url) && !filter_var($image_url, FILTER_VALIDATE_URL) && !preg_match('/^images\//', $image_url)) {
        $errors[] = "URL Gambar tidak valid. Harus URL lengkap (contoh: http://...) atau path relatif dari folder 'images/' (contoh: images/nama_gambar.jpg).";
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO tours (tour_name, description, price, duration, image_url) VALUES (:tour_name, :description, :price, :duration, :image_url)");

            $stmt->bindParam(':tour_name', $tour_name);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':duration', $duration);
            $stmt->bindParam(':image_url', $image_url);

            $stmt->execute();

            header("Location: index.php?status=success&message=" . urlencode("Tur '{$tour_name}' berhasil ditambahkan!"));
            exit();

        } catch (PDOException $e) {
            $message = "Error saat menyimpan tur: " . htmlspecialchars($e->getMessage());
            $status_class = 'error';
        }
    } else {
        $message = "Validasi Gagal: <br>" . implode("<br>", $errors);
        $status_class = 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Tambah Tur Baru</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <h1>Panel Admin Travel Tour Gokil! ⚙️</h1>
        <p>Tambah Tur Baru</p>
    </header>

    <main class="admin-form-container">
        <h1>Tambah Tur Baru</h1>

        <?php if (!empty($message)): ?>
            <div class="form-status-message <?php echo $status_class; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form action="add_tour.php" method="POST">
            <div class="form-group">
                <label for="tour_name">Nama Tur:</label>
                <input type="text" id="tour_name" name="tour_name" required value="<?php echo htmlspecialchars($tour_name); ?>">
            </div>
            <div class="form-group">
                <label for="description">Deskripsi:</label>
                <textarea id="description" name="description" required><?php echo htmlspecialchars($description); ?></textarea>
            </div>
            <div class="form-group">
                <label for="price">Harga (Rp):</label>
                <input type="number" id="price" name="price" step="any" required value="<?php echo htmlspecialchars($price); ?>">
            </div>
            <div class="form-group">
                <label for="duration">Durasi (contoh: 3 Hari 2 Malam):</label>
                <input type="text" id="duration" name="duration" required value="<?php echo htmlspecialchars($duration); ?>">
            </div>
            <div class="form-group">
                <label for="image_url">URL Gambar (contoh: images/komodo.jpg atau https://example.com/image.jpg):</label>
                <input type="text" id="image_url" name="image_url" required value="<?php echo htmlspecialchars($image_url); ?>">
            </div>
            <div class="form-actions">
                <button type="submit" class="btn-submit-admin">Simpan Tur</button>
                <a href="index.php" class="btn-cancel-admin">Batal</a>
            </div>
        </form>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Travel Tour Gokil. Admin Panel.</p>
    </footer>
</body>
</html>