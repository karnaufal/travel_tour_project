<?php
session_start();
ob_start();

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include_once '../config.php'; // Koneksi database

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $booking_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

    if ($booking_id <= 0) {
        header("Location: bookings.php?status=error&message=" . urlencode("ID pemesanan tidak valid."));
        exit();
    }

    try {
        // Hapus pemesanan dari database
        $stmt = $pdo->prepare("DELETE FROM bookings WHERE id = :id");
        $stmt->bindParam(':id', $booking_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            // Redirect dengan pesan sukses
            header("Location: bookings.php?status=success&message=" . urlencode("Pemesanan berhasil dihapus!"));
            exit();
        } else {
            // Redirect dengan pesan error jika gagal eksekusi query
            header("Location: bookings.php?status=error&message=" . urlencode("Gagal menghapus pemesanan."));
            exit();
        }
    } catch (PDOException $e) {
        // Redirect dengan pesan error jika ada masalah database
        header("Location: bookings.php?status=error&message=" . urlencode("Kesalahan database saat menghapus pemesanan: " . htmlspecialchars($e->getMessage())));
        exit();
    }
} else {
    // Jika tidak ada ID yang diberikan
    header("Location: bookings.php?status=error&message=" . urlencode("ID pemesanan tidak ditemukan untuk dihapus."));
    exit();
}
ob_end_flush();
?>