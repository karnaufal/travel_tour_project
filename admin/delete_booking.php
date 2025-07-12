<?php
session_start();
ob_start();

include_once '../config.php'; // Koneksi database PDO

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $booking_id = intval($_GET['id']);
    try {
        $stmt = $pdo->prepare("DELETE FROM bookings WHERE id = ?");
        $stmt->execute([$booking_id]);

        header("Location: bookings.php?status=success&message=Pemesanan berhasil dihapus!");
        exit();
    } catch (PDOException $e) {
        header("Location: bookings.php?status=error&message=Gagal menghapus pemesanan: " . urlencode($e->getMessage()));
        exit();
    }
} else {
    header("Location: bookings.php?status=error&message=ID pemesanan tidak ditemukan.");
    exit();
}
ob_end_flush();
?>