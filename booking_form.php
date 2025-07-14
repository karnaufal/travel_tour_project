<?php
session_start();
ob_start();

include_once 'config.php'; // Path ke koneksi database PDO

$tour = null;
$tour_id = isset($_GET['tour_id']) ? (int)$_GET['tour_id'] : (isset($_POST['tour_id']) ? (int)$_POST['tour_id'] : 0);

// Ambil detail tur berdasarkan tour_id untuk ditampilkan di form
if ($tour_id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT id, tour_name, price, image, duration FROM tours WHERE id = ?");
        $stmt->execute([$tour_id]);
        $tour = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error fetching tour in booking_form.php: " . $e->getMessage());
    }
}

// Jika tur tidak ditemukan atau tour_id tidak valid, redirect ke daftar tur
if (!$tour) {
    header('Location: paket_tur.php');
    exit();
}

$message = '';
$message_type = ''; // 'success' or 'error'

// Logika pemrosesan form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name = htmlspecialchars(trim($_POST['customer_name']));
    $customer_email = htmlspecialchars(trim($_POST['customer_email']));
    $num_participants = intval($_POST['num_participants']);
    $booking_date = htmlspecialchars(trim($_POST['booking_date']));

    // Validasi input
    if (empty($customer_name) || empty($customer_email) || $num_participants <= 0 || empty($booking_date)) {
        $message = 'Harap lengkapi semua bidang form dengan benar!';
        $message_type = 'error';
    } elseif (!filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Format email tidak valid.';
        $message_type = 'error';
    } else {
        try {
            // Hitung total harga
            $total_price = $tour['price'] * $num_participants;

            $stmt = $pdo->prepare("INSERT INTO bookings (tour_id, customer_name, customer_email, num_participants, booking_date, total_price) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$tour['id'], $customer_name, $customer_email, $num_participants, $booking_date, $total_price]);

            $message = 'Pemesanan Anda berhasil! Total harga: Rp ' . number_format($total_price, 0, ',', '.');
            $message_type = 'success';
            
            $_POST = array(); // Mengosongkan data POST untuk form

        } catch (PDOException $e) {
            error_log("Database error inserting booking in booking_form.php: " . $e->getMessage());
            $message = 'Terjadi kesalahan saat memproses pemesanan Anda. Mohon coba lagi. Detail: ' . htmlspecialchars($e->getMessage());
            $message_type = 'error';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Tur: <?php echo htmlspecialchars($tour['tour_name'] ?? 'Tidak Ditemukan'); ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* General form container and card styling */
        .booking-page-container {
            padding: 120px 0 60px 0;
            background-color: var(--light-bg);
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: calc(100vh - 100px);
        }

        .booking-card-wrapper {
            display: flex;
            flex-wrap: wrap; /* Allows wrapping on smaller screens */
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-medium);
            max-width: 900px; /* Adjust max width as needed */
            width: 100%;
            overflow: hidden; /* Ensures rounded corners */
        }

        .booking-tour-summary {
            flex: 1;
            min-width: 300px; /* Minimum width for the summary part */
            padding: 30px;
            background-color: var(--primary-color); /* Darker background for contrast */
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .booking-tour-summary::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.6)); /* Overlay for image */
            z-index: 1;
        }

        .booking-tour-summary img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 0;
        }

        .booking-summary-content {
            position: relative;
            z-index: 2; /* Ensure content is above overlay */
            width: 100%;
        }

        .booking-summary-content h2 {
            font-size: 2.2em;
            margin-bottom: 15px;
            color: white;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
        }
        .booking-summary-content p {
            font-size: 1.1em;
            margin-bottom: 8px;
            color: rgba(255,255,255,0.9);
        }
        .booking-summary-content .price-display {
            font-size: 1.8em;
            font-weight: 700;
            margin-top: 20px;
            color: var(--accent-color); /* Use accent color for price */
        }
        .booking-summary-content .info-item {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 10px;
            font-size: 1em;
        }
        .booking-summary-content .info-item i {
            margin-right: 10px;
            font-size: 1.2em;
        }

        .booking-form-content {
            flex: 1.5; /* Form takes more space */
            min-width: 400px; /* Minimum width for the form part */
            padding: 40px;
        }

        .booking-form-content h2 {
            text-align: center;
            color: var(--primary-color);
            margin-bottom: 30px;
            font-size: 2em;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative; /* For icon positioning */
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-color);
            font-weight: 600;
        }

        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="number"],
        .form-group input[type="date"] {
            width: calc(100% - 40px); /* Adjust for padding and icon */
            padding: 12px 12px 12px 40px; /* Left padding for icon */
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius-small);
            font-size: 1em;
            color: var(--text-color);
            background-color: var(--input-bg);
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-group input[type="text"]:focus,
        .form-group input[type="email"]:focus,
        .form-group input[type="number"]:focus,
        .form-group input[type="date"]:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(var(--primary-color-rgb), 0.2);
            outline: none;
        }

        .form-group .input-icon {
            position: absolute;
            left: 12px;
            top: 42px; /* Adjust based on label height */
            color: var(--secondary-color);
            font-size: 1.1em;
        }
        /* Adjust for date input icon */
        .form-group input[type="date"] + .input-icon {
            top: 42px; /* Consistent top position */
        }


        .booking-form-content input[type="submit"] {
            width: 100%;
            padding: 15px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            font-size: 1.1em;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .booking-form-content input[type="submit"]:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: var(--border-radius-small);
            font-weight: 600;
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

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .booking-card-wrapper {
                flex-direction: column;
                max-width: 95%;
            }
            .booking-tour-summary,
            .booking-form-content {
                min-width: unset;
                width: 100%;
            }
            .booking-tour-summary {
                padding: 40px 20px;
                border-radius: var(--border-radius) var(--border-radius) 0 0;
            }
            .booking-form-content {
                padding: 30px 20px;
            }
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
                    <li><a href="paket_tur.php" class="active">Paket Tur</a></li>
                    <li><a href="tentang_kami.php">Tentang Kami</a></li>
                    <li><a href="kontak.php">Kontak</a></li>
                    <li><a href="admin/login.php" class="btn-login-admin">Login Admin</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="booking-page-container">
        <div class="booking-card-wrapper">
            <div class="booking-tour-summary">
                <?php
                    $image_name_booking = $tour['image'] ?? '';
                    $image_path_booking = 'uploads/' . $image_name_booking;
                    $actual_image_src_booking = (file_exists($image_path_booking) && !empty($image_name_booking)) ? htmlspecialchars($image_path_booking) : 'images/placeholder.jpg';
                ?>
                <img src="<?php echo $actual_image_src_booking; ?>" alt="<?php echo htmlspecialchars($tour['tour_name'] ?? 'Gambar Tur'); ?>">
                <div class="booking-summary-content">
                    <h2><?php echo htmlspecialchars($tour['tour_name'] ?? 'Detail Tur'); ?></h2>
                    <p class="info-item"><i class="far fa-clock"></i> <?php echo htmlspecialchars($tour['duration'] ?? 'Durasi Tidak Tersedia'); ?></p>
                    <p class="price-display">Rp <?php echo number_format($tour['price'] ?? 0, 0, ',', '.'); ?> / Orang</p>
                </div>
            </div>

            <div class="booking-form-content">
                <h2>Isi Detail Pemesanan</h2>
                <?php if (!empty($message)): ?>
                    <div class="message <?php echo $message_type; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <form action="booking_form.php?tour_id=<?php echo htmlspecialchars($tour['id'] ?? ''); ?>" method="POST">
                    <input type="hidden" name="tour_id" value="<?php echo htmlspecialchars($tour['id'] ?? ''); ?>">

                    <div class="form-group">
                        <label for="customer_name">Nama Lengkap:</label>
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" id="customer_name" name="customer_name" value="<?php echo htmlspecialchars($_POST['customer_name'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="customer_email">Email:</label>
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" id="customer_email" name="customer_email" value="<?php echo htmlspecialchars($_POST['customer_email'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="num_participants">Jumlah Peserta:</label>
                        <i class="fas fa-users input-icon"></i>
                        <input type="number" id="num_participants" name="num_participants" min="1" value="<?php echo htmlspecialchars($_POST['num_participants'] ?? 1); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="booking_date">Tanggal Keberangkatan:</label>
                        <i class="fas fa-calendar-alt input-icon"></i>
                        <input type="date" id="booking_date" name="booking_date" value="<?php echo htmlspecialchars($_POST['booking_date'] ?? ''); ?>" required>
                    </div>

                    <input type="submit" value="Kirim Pemesanan">
                </form>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> JalanJalan Kuy!. All rights reserved.</p>
        <p style="font-size: 0.8em; margin-top: 5px;">Dibuat Type-Spype</p>
    </footer>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            // Highlight navigasi aktif
            const currentPath = window.location.pathname.split('/').pop();
            $('nav.main-nav ul li a').removeClass('active');
            if (currentPath === '' || currentPath === 'index.php') {
                $('nav.main-nav ul li a[href="index.php"]').addClass('active');
            } else if (currentPath === 'paket_tur.php' || currentPath.startsWith('detail_tour.php') || currentPath.startsWith('booking_form.php')) {
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
<?php
ob_end_flush();
?>