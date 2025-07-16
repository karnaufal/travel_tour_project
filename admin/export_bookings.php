<?php
// Pastikan ini diatur di awal file untuk menangani error (untuk debugging)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Pastikan file koneksi database terhubung.
// Path ke config.php dari folder admin adalah '../config.php'
include_once '../config.php';

session_start(); // Wajib: Mulai session di setiap halaman yang menggunakan session

// --- Pengecekan Session Login Admin ---
// Jika user belum login sebagai admin, redirect ke halaman login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php'); // Arahkan ke halaman login di folder admin
    exit();
}
// --- Akhir Pengecekan Session ---

try {
    // Query untuk mengambil semua data dari tabel bookings
    $stmt = $pdo->query("SELECT * FROM bookings ORDER BY id DESC");
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Jika tidak ada data, bisa redirect atau tampilkan pesan
    if (empty($bookings)) {
        echo "Tidak ada data pemesanan untuk diekspor.";
        exit();
    }

    // Nama file CSV yang akan diunduh
    $filename = 'pemesanan_tour_' . date('Ymd_His') . '.csv';

    // Set header untuk download file CSV
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    // Buka output stream
    $output = fopen('php://output', 'w');

    // Ambil nama kolom sebagai header CSV
    $headers = array_keys($bookings[0]);
    fputcsv($output, $headers);

    // Masukkan data ke CSV
    foreach ($bookings as $row) {
        fputcsv($output, $row);
    }

    // Tutup output stream
    fclose($output);
    exit();

} catch (PDOException $e) {
    // Tangani error jika terjadi masalah database
    error_log("Error exporting bookings: " . $e->getMessage());
    die("Terjadi kesalahan saat mengekspor data: " . $e->getMessage());
}
?>