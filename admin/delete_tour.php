<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

ob_start(); // Kalau kamu pakai ob_start()
include '../config.php';
// ... sisa kode edit_tour.php ...
?>

<?php
include '../config.php'; // Koneksi database

// Pastikan request method-nya GET dan ada ID yang dikirim
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $tour_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

    // Validasi ID yang lebih kuat
    if ($tour_id > 0) {
        try {
            $stmt = $pdo->prepare("DELETE FROM tours WHERE id = :id");
            $stmt->bindParam(':id', $tour_id, PDO::PARAM_INT);
            $stmt->execute();

            header("Location: index.php?status=success&message=" . urlencode("Tur berhasil dihapus!"));
            exit();

        } catch (PDOException $e) {
            header("Location: index.php?status=error&message=" . urlencode("Error saat menghapus tur: " . htmlspecialchars($e->getMessage())));
            exit();
        }
    } else {
        header("Location: index.php?status=error&message=" . urlencode("ID tur tidak valid untuk dihapus."));
        exit();
    }
} else {
    header("Location: index.php?status=error&message=" . urlencode("ID tur tidak ditemukan untuk dihapus."));
    exit();
}
?>