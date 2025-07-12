<?php
session_start();
ob_start();

include_once 'config.php'; // Path ke koneksi database PDO

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tour_id = intval($_POST['tour_id']);
    $customer_name = htmlspecialchars(trim($_POST['customer_name']));
    $customer_email = htmlspecialchars(trim($_POST['customer_email']));
    $num_participants = intval($_POST['num_participants']);
    $booking_date = htmlspecialchars(trim($_POST['booking_date'])); // Format YYYY-MM-DD

    // Validasi dasar
    if (empty($customer_name) || empty($customer_email) || empty($num_participants) || empty($booking_date)) {
        header("Location: detail_tour.php?id=" . $tour_id . "&status=error&message=" . urlencode("Semua field harus diisi!"));
        exit();
    }

    if (!filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
        header("Location: detail_tour.php?id=" . $tour_id . "&status=error&message=" . urlencode("Format email tidak valid."));
        exit();
    }

    if ($num_participants <= 0) {
        header("Location: detail_tour.php?id=" . $tour_id . "&status=error&message=" . urlencode("Jumlah peserta harus lebih dari 0."));
        exit();
    }

    try {
        // Simpan data pemesanan ke database
        $stmt = $pdo->prepare("INSERT INTO bookings (tour_id, customer_name, customer_email, num_participants, booking_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$tour_id, $customer_name, $customer_email, $num_participants, $booking_date]);

        // Redirect kembali ke halaman detail_tour dengan pesan sukses
        header("Location: detail_tour.php?id=" . $tour_id . "&status=success&message=" . urlencode("Pemesanan tur berhasil!"));
        exit();

    } catch (PDOException $e) {
        // Redirect dengan pesan error jika ada masalah database
        header("Location: detail_tour.php?id=" . $tour_id . "&status=error&message=" . urlencode("Terjadi kesalahan saat menyimpan pemesanan: " . $e->getMessage()));
        exit();
    }
} else {
    // Jika diakses tanpa metode POST, redirect ke halaman utama
    header("Location: index.php");
    exit();
}
ob_end_flush();
?>