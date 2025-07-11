<?php
include_once 'config.php'; // Panggil file koneksi database kita

// Dapatkan halaman saat ini dari URL, default ke 1
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) {
    $current_page = 1;
}

// Hitung offset untuk query database
$offset = ($current_page - 1) * ITEMS_PER_PAGE;

try {
    // 1. Hitung total jumlah tur (untuk menentukan berapa banyak halaman)
    $total_tours_stmt = $pdo->query("SELECT COUNT(*) FROM tours");
    $total_tours = $total_tours_stmt->fetchColumn();

    // Hitung total halaman
    $total_pages = ceil($total_tours / ITEMS_PER_PAGE);

    // 2. Ambil data tur untuk halaman saat ini
    $stmt = $pdo->prepare("SELECT * FROM tours ORDER BY id DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', ITEMS_PER_PAGE, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $tours = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Jika terjadi error, tampilkan pesan
    echo '<div class="error-message"><h2>Error!</h2><p>Tidak dapat mengambil data tur: ' . htmlspecialchars($e->getMessage()) . '</p></div>';
    $tours = []; // Pastikan $tours kosong agar tidak ada error foreach
    $total_pages = 1; // Default ke 1 halaman
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Tour Gokil! - Beranda</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* CSS untuk Pagination */
        .pagination {
            margin-top: 30px;
            text-align: center;
        }
        .pagination a, .pagination span {
            display: inline-block;
            padding: 8px 16px;
            border: 1px solid #ddd;
            text-decoration: none;
            color: #333;
            margin: 0 4px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .pagination a:hover {
            background-color: #f2f2f2;
        }
        .pagination span.current-page {
            background-color: #007bff;
            color: white;
            border: 1px solid #007bff;
            font-weight: bold;
        }
        .pagination span.disabled {
            color: #ccc;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <header>
        <h1>Jelajahi Tur Gokil Kita! ✈️</h1>
        <p>Pilih petualangan impianmu sekarang!</p>
    </header>

    <main>
        <?php if (!empty($tours)): ?>
            <div class="tour-grid">
                <?php foreach ($tours as $tour): ?>
                    <div class="tour-card">
                    <img src="<?php echo htmlspecialchars($tour['image_url']); ?>" alt="<?php echo htmlspecialchars($tour['tour_name']); ?>">
                    <div class="tour-card-content"> <h3><?php echo htmlspecialchars($tour['tour_name']); ?></h3>
                        <p class="price">Rp <?php echo number_format($tour['price'], 0, ',', '.'); ?></p>
                        <p class="duration"><?php echo htmlspecialchars($tour['duration']); ?></p>
                        <p class="description"><?php echo nl2br(htmlspecialchars(substr($tour['description'], 0, 100))) . (strlen($tour['description']) > 100 ? '...' : ''); ?></p>
                    <a href="detail_tour.php?id=<?php echo htmlspecialchars($tour['id']); ?>" class="btn-detail">Lihat Detail & Pesan</a>
                </div> </div>
                <?php endforeach; ?>
            </div>

            <div class="pagination">
                <?php if ($current_page > 1): ?>
                    <a href="?page=<?php echo $current_page - 1; ?>">Prev</a>
                <?php else: ?>
                    <span class="disabled">Prev</span>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <?php if ($i == $current_page): ?>
                        <span class="current-page"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($current_page < $total_pages): ?>
                    <a href="?page=<?php echo $current_page + 1; ?>">Next</a>
                <?php else: ?>
                    <span class="disabled">Next</span>
                <?php endif; ?>
            </div>

        <?php else: ?>
            <p>Belum ada tur yang tersedia saat ini. Cek lagi nanti ya!</p>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Travel Tour Gokil. Dijamin anti-bosan!</p>
    </footer>
</body>
</html>