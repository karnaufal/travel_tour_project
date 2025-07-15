<?php
include_once 'config.php';

$tour = null;
if (isset($_GET['id'])) {
    $tour_id = $_GET['id'];
    try {
        // Query TANPA memilih kolom 'location'
        $stmt = $pdo->prepare("SELECT id, tour_name, description, price, duration, image FROM tours WHERE id = ?");
        $stmt->execute([$tour_id]);
        $tour = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in detail_tour.php: " . $e->getMessage());
    }
}

if (!$tour) {
    // HTML untuk halaman "Tur Tidak Ditemukan"
    echo "<!DOCTYPE html><html lang='id'><head><meta charset='UTF-8'><meta name='viewport' content='width=device-width, initial-scale=1.0'><title>Tur Tidak Ditemukan</title><link rel='stylesheet' href='css/style.css'></head><body>";
    echo "<header class='main-header'><div class='container header-content'><div class='logo'><a href='index.php'>JalanJalan Kuy!</a></div><nav class='main-nav'><ul><li><a href='index.php'>Home</a></li><li><a href='paket_tur.php' class='active'>Paket Tur</a></li><li><a href='tentang_kami.php'>Tentang Kami</a></li><li><a href='kontak.php'>Kontak</a></li><li><a href='admin/login.php' class='btn-login-admin'>Login Admin</a></li></ul></nav></div></header>";
    echo "<section class='section-common' style='padding-top: 100px;'>";
    echo "<div class='container'><h1 style='text-align: center; color: var(--primary-color);'>Tur Tidak Ditemukan</h1><p style='text-align: center;'>Maaf, tur yang Anda cari tidak tersedia.</p><p style='text-align: center;'><a href='paket_tur.php' class='btn-primary'>Kembali ke Daftar Tur</a></p></div>";
    echo "</section>";
    echo "</body></html>";
    exit();
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($tour['tour_name'] ?? 'Detail Tur'); ?> - Detail Tour</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .tour-detail-section {
            padding: 100px 0 60px 0;
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
            height: 350px;
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
            width: 25px;
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
                    <li><a href="paket_tur.php" class="active">Paket Tour</a></li>
                    <li><a href="tentang_kami.php">Tentang Kami</a></li>
                    <li><a href="kontak.php">Kontak</a></li>
                    <!-- <li><a href="admin/login.php" class="btn-login-admin">Login Admin</a></li> -->
                </ul>
            </nav>
        </div>
    </header>

    <section class="tour-detail-section">
        <div class="container">
            <div class="tour-detail-content">
                <div class="tour-detail-image">
                    <?php
                    $image_name_detail = $tour['image'] ?? '';
                    $image_path_detail = 'uploads/' . $image_name_detail;
                    $actual_image_src_detail = (file_exists($image_path_detail) && !empty($image_name_detail)) ? htmlspecialchars($image_path_detail) : 'images/placeholder.jpg';
                    $tour_name_alt_detail = htmlspecialchars($tour['tour_name'] ?? 'Gambar Tur');
                    ?>
                    <img src="<?php echo $actual_image_src_detail; ?>" alt="<?php echo $tour_name_alt_detail; ?>">
                </div>
                <div class="tour-detail-info">
                    <h1><?php echo htmlspecialchars($tour['tour_name'] ?? 'Nama Tur Tidak Ditemukan'); ?></h1>
                    <div class="price">Rp <?php echo number_format($tour['price'] ?? 0, 0, ',', '.'); ?></div>
                    <div class="info-item"><i class="far fa-clock"></i> <span>Durasi: <?php echo htmlspecialchars($tour['duration'] ?? 'Tidak Tersedia'); ?></span></div>
                    <p><?php echo nl2br(htmlspecialchars($tour['description'] ?? 'Deskripsi tidak tersedia.')); ?></p>
                    <a href="booking_form.php?tour_id=<?php echo htmlspecialchars($tour['id'] ?? ''); ?>" class="btn-primary">Pesan Sekarang</a>
                </div>
            </div>
        </div>
    </section>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            const currentPath = window.location.pathname.split('/').pop();
            $('nav.main-nav ul li a').removeClass('active');
            if (currentPath === '' || currentPath === 'index.php') {
                $('nav.main-nav ul li a[href="index.php"]').addClass('active');
            } else if (currentPath === 'paket_tur.php' || currentPath.startsWith('detail_tour.php')) {
                $('nav.main-nav ul li a[href="paket_tur.php"]').addClass('active');
            } else if (currentPath === 'tentang_kami.php') {
                $('nav.main-nav ul li a[href="tentang_kami.php"]').addClass('active');
            } else if (currentPath === 'kontak.php') {
                $('nav.main-nav ul li a[href="kontak.php"]').addClass('active');
            }
        });
    </script>
</body>
</html>