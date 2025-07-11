<?php
include 'config.php'; // Panggil file koneksi database kita

// Ambil semua data tur dari database
try {
    $stmt = $pdo->query("SELECT * FROM tours ORDER BY id DESC"); // Urutkan dari ID terbaru
    $tours = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    $tours = []; // Pastikan $tours tetap array kosong jika ada error
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Tour Gokil!</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Travel Tour Gokil! 🚀</h1>
        <p>Destinasi impian menantimu!</p>
    </header>

    <main class="container">
        <h2>Daftar Tur Asyik Kita:</h2>
        <?php if (!empty($tours)): ?>
            <div class="tour-list">
                <?php foreach ($tours as $tour): ?>
                    <div class="tour-card">
                        <img src="<?php echo htmlspecialchars($tour['image_url']); ?>" alt="<?php echo htmlspecialchars($tour['tour_name']); ?>">
                        <div class="tour-content">
                            <h3><?php echo htmlspecialchars($tour['tour_name']); ?></h3>
                            <p class="tour-description-short"><?php echo nl2br(htmlspecialchars(mb_strimwidth($tour['description'], 0, 100, "..."))); ?></p>
                            <p class="price">Harga: Rp <?php echo number_format($tour['price'], 0, ',', '.'); ?></p>
                            <p class="duration">Durasi: <?php echo htmlspecialchars($tour['duration']); ?></p>
                            <a href="detail_tour.php?id=<?php echo htmlspecialchars($tour['id']); ?>" class="btn-detail">Lihat Detail & Pesan</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="text-align: center; padding: 50px;">Maaf, belum ada tur yang tersedia saat ini. Coba lagi nanti ya!</p>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Travel Tour Gokil. Dijamin anti-bosan!</p>
    </footer>
</body>
</html>