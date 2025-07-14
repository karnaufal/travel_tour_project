<?php
include_once 'config.php';

$tour = null;
if (isset($_GET['id'])) {
    $tour_id = $_GET['id'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM tours WHERE id = ?");
        $stmt->execute([$tour_id]);
        $tour = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Handle error
        echo "Error: " . $e->getMessage();
    }
}

if (!$tour) {
    echo "<!DOCTYPE html><html lang='id'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width, initial-scale=1.0'><title>Tur Tidak Ditemukan</title><link rel='stylesheet' href='css/style.css'></head><body>";
    echo "<header class='main-header'><div class='container header-content'><div class='logo'><a href='index.php'>JalanJalan Kuy!</a></div><nav class='main-nav'><ul><li><a href='index.php'>Home</a></li><li><a href='tours.php' class='active'>Paket Tur</a></li><li><a href='about.php'>Tentang Kami</a></li><li><a href='contact.php'>Kontak</a></li><li><a href='admin/login.php' class='btn-login-admin'>Login Admin</a></li></ul></nav></div></header>";
    echo "<section class='section-common' style='padding-top: 100px;'>";
    echo "<div class='container'><h1 style='text-align: center; color: var(--primary-color);'>Tur Tidak Ditemukan</h1><p style='text-align: center;'>Maaf, tur yang Anda cari tidak tersedia.</p><p style='text-align: center;'><a href='tours.php' class='btn-primary'>Kembali ke Daftar Tur</a></p></div>";
    echo "</section>";
    include_once 'includes/footer.php';
    echo "</body></html>";
    exit();
}

$review_message = '';
$review_message_type = '';

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $user_name = trim($_POST['user_name'] ?? '');
    $rating = (int)($_POST['rating'] ?? 0);
    $comment = trim($_POST['comment'] ?? '');

    if (empty($user_name) || $rating < 1 || $rating > 5 || empty($comment)) {
        $review_message = "Harap lengkapi semua kolom ulasan dengan benar.";
        $review_message_type = "error";
    } else {
        try {
            // Cek apakah user sudah pernah review untuk tur ini (opsional, bisa dihilangkan)
            // $check_stmt = $pdo->prepare("SELECT COUNT(*) FROM reviews WHERE tour_id = ? AND user_name = ?");
            // $check_stmt->execute([$tour_id, $user_name]);
            // if ($check_stmt->fetchColumn() > 0) {
            //     $review_message = "Anda sudah memberikan ulasan untuk tur ini.";
            //     $review_message_type = "error";
            // } else {
                $stmt = $pdo->prepare("INSERT INTO reviews (tour_id, user_name, rating, comment) VALUES (?, ?, ?, ?)");
                $stmt->execute([$tour_id, $user_name, $rating, $comment]);
                $review_message = "Ulasan Anda berhasil dikirim! Ulasan akan tampil setelah disetujui admin.";
                $review_message_type = "success";
            // }
        } catch (PDOException $e) {
            $review_message = "Gagal mengirim ulasan: " . $e->getMessage();
            $review_message_type = "error";
        }
    }
}

// Fetch approved reviews for this tour
$reviews = [];
try {
    $stmt_reviews = $pdo->prepare("SELECT user_name, rating, comment, review_date FROM reviews WHERE tour_id = ? AND is_approved = TRUE ORDER BY review_date DESC");
    $stmt_reviews->execute([$tour_id]);
    $reviews = $stmt_reviews->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Handle error
    error_log("Error fetching reviews: " . $e->getMessage());
}

// Calculate average rating
$average_rating = 0;
if (!empty($reviews)) {
    $total_rating = 0;
    foreach ($reviews as $review) {
        $total_rating += $review['rating'];
    }
    $average_rating = round($total_rating / count($reviews), 1);
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($tour['tour_name']); ?> - Detail Tur</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .tour-detail-section {
            padding: 100px 0 60px 0; /* Padding atas disesuaikan untuk fixed header */
            background-color: var(--light-bg);
        }
        .tour-detail-content {
            display: flex;
            flex-wrap: wrap;
            gap: 40px;
            background-color: var(--card-bg);
            padding: 30px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-medium);
        }
        .tour-detail-image {
            flex: 1;
            min-width: 350px;
            text-align: center;
        }
        .tour-detail-image img {
            max-width: 100%;
            height: 350px; /* Tinggi gambar konsisten */
            object-fit: cover;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-light);
        }
        .tour-detail-info {
            flex: 2;
            min-width: 450px;
        }
        .tour-detail-info h1 {
            font-size: 2.8em;
            color: var(--primary-color);
            margin-bottom: 15px;
        }
        .tour-detail-info .price {
            font-size: 2em;
            font-weight: 700;
            color: var(--accent-color);
            margin-bottom: 20px;
        }
        .tour-detail-info .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            color: var(--text-color);
            font-size: 1.1em;
        }
        .tour-detail-info .info-item i {
            margin-right: 15px;
            color: var(--secondary-color);
            font-size: 1.3em;
            width: 25px; /* Lebar ikon tetap */
            text-align: center;
        }
        .tour-detail-info p {
            margin-top: 25px;
            margin-bottom: 25px;
            line-height: 1.7;
            font-size: 1.05em;
            color: var(--light-text);
        }
        .tour-detail-info .btn-primary {
            display: inline-block;
            padding: 15px 30px;
            border-radius: 50px;
            font-size: 1.1em;
            font-weight: 600;
            margin-top: 20px;
        }

        /* --- Reviews Section --- */
        .reviews-section {
            padding: 60px 0;
            background-color: var(--card-bg);
            margin-top: 40px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-medium);
        }
        .reviews-section h2 {
            text-align: center;
            font-size: 2.5em;
            color: var(--primary-color);
            margin-bottom: 40px;
        }
        .review-summary {
            text-align: center;
            margin-bottom: 30px;
            font-size: 1.3em;
            color: var(--text-color);
        }
        .review-summary .stars i {
            color: #ffc107; /* Warna bintang kuning */
            margin: 0 2px;
            font-size: 1.4em;
        }
        .review-form-container {
            max-width: 700px;
            margin: 0 auto 50px auto;
            background-color: var(--light-bg);
            padding: 30px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-light);
        }
        .review-form-container h3 {
            text-align: center;
            font-size: 1.8em;
            color: var(--text-color);
            margin-bottom: 25px;
        }
        .review-form-container .form-group label {
            font-size: 1em;
        }
        .rating-input {
            display: flex;
            justify-content: center;
            gap: 5px;
            margin-bottom: 20px;
        }
        .rating-input input[type="radio"] {
            display: none;
        }
        .rating-input label {
            cursor: pointer;
            font-size: 2em;
            color: #ccc;
            transition: color 0.2s ease;
        }
        .rating-input label:hover,
        .rating-input label:hover ~ label,
        .rating-input input[type="radio"]:checked ~ label {
            color: #ffc107; /* Warna bintang saat di-hover/dipilih */
        }
        /* Mengatur agar bintang di kanan radio button yang dipilih juga ikut aktif */
        .rating-input label:has(+ input[type="radio"]:checked) {
            color: #ffc107;
        }
        .reviews-list {
            max-width: 900px;
            margin: 0 auto;
        }
        .review-item {
            background-color: var(--light-bg);
            padding: 25px;
            margin-bottom: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-light);
            border-left: 5px solid var(--primary-color);
        }
        .review-item .review-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .review-item .review-header .user-info {
            font-weight: 600;
            color: var(--text-color);
            font-size: 1.1em;
        }
        .review-item .review-header .rating-stars i {
            color: #ffc107;
        }
        .review-item .review-date {
            font-size: 0.85em;
            color: var(--light-text);
            margin-bottom: 10px;
            text-align: right;
        }
        .review-item .review-comment {
            font-size: 1em;
            color: var(--light-text);
            line-height: 1.6;
        }
        .no-reviews-message {
            text-align: center;
            color: var(--light-text);
            font-size: 1.1em;
            margin-top: 30px;
        }
    </style>
</head>
<body>

    <header class="main-header">
        <div class="container header-content">
            <div class="logo">
                <a href="index.php">JalanJalan Kuy!</a>
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="tours.php" class="active">Paket Tur</a></li>
                    <li><a href="about.php">Tentang Kami</a></li>
                    <li><a href="contact.php">Kontak</a></li>
                    <li><a href="admin/login.php" class="btn-login-admin">Login Admin</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="tour-detail-section">
        <div class="container">
            <div class="tour-detail-content">
                <div class="tour-detail-image">
                    <?php if (!empty($tour['image']) && file_exists('uploads/' . $tour['image'])): ?>
                        <img src="uploads/<?php echo htmlspecialchars($tour['image']); ?>" alt="<?php echo htmlspecialchars($tour['tour_name']); ?>">
                    <?php else: ?>
                        <img src="images/placeholder.jpg" alt="No Image Available">
                    <?php endif; ?>
                </div>
                <div class="tour-detail-info">
                    <h1><?php echo htmlspecialchars($tour['tour_name']); ?></h1>
                    <div class="price">Rp <?php echo number_format($tour['price'], 0, ',', '.'); ?></div>
                    <div class="info-item"><i class="far fa-clock"></i> <span>Durasi: <?php echo htmlspecialchars($tour['duration']); ?></span></div>
                    <div class="info-item"><i class="fas fa-map-marker-alt"></i> <span>Lokasi: <?php echo htmlspecialchars($tour['location']); ?></span></div>
                    <div class="info-item">
                        <i class="fas fa-star"></i>
                        <span>Rating: <?php echo $average_rating > 0 ? htmlspecialchars($average_rating) . " / 5.0" : "Belum ada rating"; ?> (<?php echo count($reviews); ?> ulasan)</span>
                    </div>
                    <p><?php echo nl2br(htmlspecialchars($tour['description'])); ?></p>
                    <a href="booking_form.php?tour_id=<?php echo htmlspecialchars($tour['id']); ?>" class="btn-primary">Pesan Sekarang</a>
                </div>
            </div>
        </div>
    </section>

    <section class="reviews-section container">
        <h2>Ulasan Pelanggan</h2>
        <?php if (!empty($reviews)): ?>
            <div class="review-summary">
                <div class="stars">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="<?php echo ($i <= $average_rating) ? 'fas fa-star' : 'far fa-star'; ?>"></i>
                    <?php endfor; ?>
                </div>
                Rata-rata rating: <strong><?php echo htmlspecialchars($average_rating); ?> / 5.0</strong> dari <?php echo count($reviews); ?> ulasan.
            </div>
        <?php else: ?>
            <p class="no-reviews-message">Belum ada ulasan untuk tur ini. Jadilah yang pertama memberikan ulasan!</p>
        <?php endif; ?>

        <div class="review-form-container">
            <h3>Berikan Ulasan Anda</h3>
            <?php if ($review_message): ?>
                <div class="status-message <?php echo $review_message_type; ?>">
                    <?php echo htmlspecialchars($review_message); ?>
                </div>
            <?php endif; ?>
            <form action="tour_detail.php?id=<?php echo htmlspecialchars($tour['id']); ?>" method="POST">
                <div class="form-group">
                    <label for="user_name">Nama Anda:</label>
                    <input type="text" id="user_name" name="user_name" required>
                </div>
                <div class="form-group">
                    <label>Rating:</label>
                    <div class="rating-input">
                        <input type="radio" id="star5" name="rating" value="5" required><label for="star5" title="5 bintang"><i class="fas fa-star"></i></label>
                        <input type="radio" id="star4" name="rating" value="4"><label for="star4" title="4 bintang"><i class="fas fa-star"></i></label>
                        <input type="radio" id="star3" name="rating" value="3"><label for="star3" title="3 bintang"><i class="fas fa-star"></i></label>
                        <input type="radio" id="star2" name="rating" value="2"><label for="star2" title="2 bintang"><i class="fas fa-star"></i></label>
                        <input type="radio" id="star1" name="rating" value="1"><label for="star1" title="1 bintang"><i class="fas fa-star"></i></label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="comment">Komentar Anda:</label>
                    <textarea id="comment" name="comment" rows="5" required></textarea>
                </div>
                <div style="text-align: center;">
                    <button type="submit" name="submit_review" class="btn-primary">Kirim Ulasan</button>
                </div>
            </form>
        </div>

        <div class="reviews-list">
            <?php if (!empty($reviews)): ?>
                <?php foreach ($reviews as $review): ?>
                    <div class="review-item">
                        <div class="review-header">
                            <span class="user-info"><?php echo htmlspecialchars($review['user_name']); ?></span>
                            <div class="rating-stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="<?php echo ($i <= $review['rating']) ? 'fas fa-star' : 'far fa-star'; ?>"></i>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <div class="review-date"><?php echo date('d M Y, H:i', strtotime($review['review_date'])); ?></div>
                        <div class="review-comment"><?php echo nl2br(htmlspecialchars($review['comment'])); ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <?php endif; ?>
        </div>
    </section>

    <?php include_once 'includes/footer.php'; ?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            // Highlight navigasi aktif di frontend
            const currentPath = window.location.pathname.split('/').pop();
            $('nav.main-nav ul li a').removeClass('active');
            if (currentPath === '' || currentPath === 'index.php') {
                $('nav.main-nav ul li a[href="index.php"]').addClass('active');
            } else if (currentPath === 'tours.php' || currentPath.startsWith('tour_detail.php')) {
                $('nav.main-nav ul li a[href="tours.php"]').addClass('active');
            } else if (currentPath === 'about.php') {
                $('nav.main-nav ul li a[href="about.php"]').addClass('active');
            } else if (currentPath === 'contact.php') {
                $('nav.main-nav ul li a[href="contact.php"]').addClass('active');
            }
        });
    </script>
</body>
</html>