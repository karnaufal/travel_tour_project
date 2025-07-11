<?php
ob_start(); // Mulai output buffering untuk menghindari "headers already sent"
include_once 'config.php'; // Panggil file koneksi database kita

// Cek apakah request datang dari metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Ambil dan bersihkan data dari form
    $tour_id = filter_var($_POST['tour_id'] ?? '', FILTER_SANITIZE_NUMBER_INT);
    $tour_name = filter_var($_POST['tour_name'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS); // Untuk pesan konfirmasi
    $customer_name = filter_var($_POST['customer_name'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
    $customer_email = filter_var($_POST['customer_email'] ?? '', FILTER_SANITIZE_EMAIL);
    $num_participants = filter_var($_POST['num_participants'] ?? '', FILTER_SANITIZE_NUMBER_INT);
    $booking_date_str = $_POST['booking_date'] ?? ''; // Tanggal dari input type="date"

    $errors = []; // Array untuk menyimpan pesan error validasi

    // 2. Validasi Data
    if (empty($tour_id) || $tour_id <= 0) {
        $errors[] = "ID Tur tidak valid.";
    }
    if (empty($customer_name)) {
        $errors[] = "Nama Lengkap wajib diisi.";
    }
    if (empty($customer_email) || !filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email tidak valid.";
    }
    if (empty($num_participants) || $num_participants <= 0) {
        $errors[] = "Jumlah peserta harus angka positif.";
    }
    if (empty($booking_date_str)) {
        $errors[] = "Tanggal keberangkatan wajib diisi.";
    } else {
        // Validasi format tanggal (YYYY-MM-DD)
        $date_obj = DateTime::createFromFormat('Y-m-d', $booking_date_str);
        if (!$date_obj || $date_obj->format('Y-m-d') !== $booking_date_str) {
            $errors[] = "Format tanggal keberangkatan tidak valid (gunakan YYYY-MM-DD).";
        } else {
            // Konversi tanggal ke format yang aman untuk database jika diperlukan (sudah YYYY-MM-DD)
            $booking_date_db = $booking_date_str;
        }
    }

    // Jika ada error validasi, redirect kembali dengan pesan error
    if (!empty($errors)) {
        $errorMessage = implode("<br>", $errors);
        header("Location: detail_tour.php?id=" . $tour_id . "&status=error_validation&message=" . urlencode($errorMessage));
        exit();
    }

    // 3. Simpan Data ke Database
    try {
        // Cek dulu apakah tour_id benar-benar ada di tabel tours
        $stmt_check_tour = $pdo->prepare("SELECT id FROM tours WHERE id = :tour_id");
        $stmt_check_tour->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
        $stmt_check_tour->execute();
        if (!$stmt_check_tour->fetch()) {
            header("Location: detail_tour.php?id=" . $tour_id . "&status=error&message=" . urlencode("Tur yang Anda coba pesan tidak ditemukan."));
            exit();
        }

        // Masukkan data pemesanan ke tabel bookings
        $stmt_insert = $pdo->prepare("INSERT INTO bookings (tour_id, customer_name, customer_email, num_participants, booking_date) VALUES (:tour_id, :customer_name, :customer_email, :num_participants, :booking_date)");

        $stmt_insert->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
        $stmt_insert->bindParam(':customer_name', $customer_name);
        $stmt_insert->bindParam(':customer_email', $customer_email);
        $stmt_insert->bindParam(':num_participants', $num_participants, PDO::PARAM_INT);
        $stmt_insert->bindParam(':booking_date', $booking_date_db); // Menggunakan tanggal yang sudah divalidasi

        $stmt_insert->execute();

        // Redirect ke halaman detail tur dengan pesan sukses
        header("Location: detail_tour.php?id=" . $tour_id . "&status=success&customer_name=" . urlencode($customer_name) . "&tour_name=" . urlencode($tour_name));
        exit();

    } catch (PDOException $e) {
        // Jika terjadi error database saat menyimpan
        header("Location: detail_tour.php?id=" . $tour_id . "&status=error&message=" . urlencode("Terjadi kesalahan database saat memproses pemesanan: " . htmlspecialchars($e->getMessage())));
        exit();
    }

} else {
    // Jika diakses langsung tanpa POST request, redirect ke halaman utama
    header("Location: index.php");
    exit();
}
ob_end_flush(); // Akhiri output buffering
?>