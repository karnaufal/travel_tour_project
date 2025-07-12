<?php
session_start();
ob_start();

include_once '../config.php'; // Koneksi database PDO

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $tour_id = intval($_GET['id']);
    $upload_dir = '../uploads/';

    try {
        // Ambil nama file gambar tur sebelum dihapus dari database
        $stmt_get_image = $pdo->prepare("SELECT image FROM tours WHERE id = ?");
        $stmt_get_image->execute([$tour_id]);
        $tour_data = $stmt_get_image->fetch(PDO::FETCH_ASSOC);

        $image_to_delete = $tour_data['image'] ?? null; // Dapatkan nama file gambar

        // Hapus tur dari database
        $stmt_delete_tour = $pdo->prepare("DELETE FROM tours WHERE id = ?");
        $stmt_delete_tour->execute([$tour_id]);

        // Jika tur berhasil dihapus dan ada gambar, hapus file gambarnya
        if ($stmt_delete_tour->rowCount() > 0 && !empty($image_to_delete) && file_exists($upload_dir . $image_to_delete)) {
            unlink($upload_dir . $image_to_delete);
        }

        header("Location: index.php?status=success&message=Tur berhasil dihapus!");
        exit();
    } catch (PDOException $e) {
        header("Location: index.php?status=error&message=Gagal menghapus tur: " . urlencode($e->getMessage()));
        exit();
    }
} else {
    header("Location: index.php?status=error&message=ID tur tidak ditemukan.");
    exit();
}
ob_end_flush();
?>