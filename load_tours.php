<?php
include_once 'config.php'; // Pastikan path ini benar

header('Content-Type: text/html'); // Pastikan output adalah HTML

if (isset($_POST['offset']) && isset($_POST['limit'])) {
    $offset = intval($_POST['offset']);
    $limit = intval($_POST['limit']);

    try {
        $stmt = $pdo->prepare("SELECT id, tour_name, description, price, duration, image FROM tours ORDER BY id DESC LIMIT ?, ?");
        // Parameter untuk LIMIT (?, ?) adalah (OFFSET, LIMIT)
        $stmt->execute([$offset, $limit]);
        $tours = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($tours)) {
            foreach ($tours as $tour) {
                // Render HTML untuk setiap tur yang dimuat
                echo '
                <div class="tour-card">
                    ';
                if (!empty($tour['image']) && file_exists('uploads/' . $tour['image'])) {
                    echo '<img src="uploads/' . htmlspecialchars($tour['image']) . '" alt="' . htmlspecialchars($tour['tour_name']) . '">';
                } else {
                    echo '<img src="images/placeholder.jpg" alt="No Image Available">';
                }
                echo '
                    <div class="card-content">
                        <h3>' . htmlspecialchars($tour['tour_name']) . '</h3>
                        <p class="duration"><i class="far fa-clock"></i> ' . htmlspecialchars($tour['duration']) . '</p>
                        <p class="price">Harga: Rp ' . number_format($tour['price'], 0, ',', '.') . '</p>
                        <p class="description">' . htmlspecialchars(substr($tour['description'], 0, 80)) . '...</p>
                        <a href="detail_tour.php?id=' . htmlspecialchars($tour['id']) . '" class="btn btn-detail">Lihat Detail & Pesan</a>
                    </div>
                </div>
                ';
            }
        }
    } catch (PDOException $e) {
        // Bisa log error di sini, tapi jangan tampilkan ke user
        // error_log("Error loading tours: " . $e->getMessage());
        echo ''; // Kirim string kosong agar JavaScript tahu tidak ada data
    }
} else {
    echo ''; // Kirim string kosong jika parameter tidak lengkap
}
?>