<?php
// Pastikan ini diatur di awal file untuk menangani error
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Sertakan file konfigurasi database
include_once 'config.php'; // Pastikan file config.php ada dan berisi koneksi PDO ($pdo)

// Cek apakah form disubmit dengan metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Kumpulkan dan sanitasi data dari form
    $tour_id = filter_var($_POST['tour_id'] ?? null, FILTER_VALIDATE_INT); // Validasi ID tur sebagai integer
    $tour_name = htmlspecialchars($_POST['tour_name'] ?? 'N/A');
    $name = htmlspecialchars($_POST['name'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $phone = htmlspecialchars($_POST['phone'] ?? '');
    $preferred_date = htmlspecialchars($_POST['preferred_date'] ?? null); // Bisa null jika tidak diisi
    $participants = filter_var($_POST['participants'] ?? 1, FILTER_VALIDATE_INT); // Validasi jumlah peserta sebagai integer
    $message = htmlspecialchars($_POST['message'] ?? 'Tidak ada pesan.');

    // Validasi sederhana (pastikan field wajib tidak kosong)
    if (empty($name) || empty($email) || empty($phone)) {
        header("Location: detail_tour.php?id=" . $tour_id . "&status=error&msg=Harap lengkapi semua kolom wajib (Nama, Email, Telepon).");
        exit();
    }

    // Pastikan $pdo object dari config.php tersedia
    if (!isset($pdo) || !$pdo instanceof PDO) {
        error_log("PDO connection not available in process_inquiry.php");
        header("Location: detail_tour.php?id=" . $tour_id . "&status=error&msg=Terjadi kesalahan sistem (koneksi database).");
        exit();
    }

    try {
        // Query untuk memasukkan data inquiry ke tabel 'inquiries'
        $stmt = $pdo->prepare("INSERT INTO inquiries (tour_id, tour_name, customer_name, customer_email, customer_phone, preferred_date, participants, message) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->execute([
            $tour_id,
            $tour_name,
            $name,
            $email,
            $phone,
            ($preferred_date === '' ? null : $preferred_date), // Simpan NULL jika tanggal kosong
            $participants,
            $message
        ]);

        // Redirect ke halaman detail tur dengan pesan sukses
        header("Location: detail_tour.php?id=" . $tour_id . "&status=success&msg=Pemesanan Anda berhasil dikirim!");
        exit();

    } catch (PDOException $e) {
        // Log error database
        error_log("Database error in process_inquiry.php: " . $e->getMessage());
        // Redirect ke halaman detail tur dengan pesan error
        header("Location: detail_tour.php?id=" . $tour_id . "&status=error&msg=Gagal menyimpan pesanan. Silakan coba lagi. Error: " . urlencode($e->getMessage()));
        exit();
    }

} else {
    // Jika diakses langsung tanpa POST, redirect ke halaman utama
    header("Location: index.php");
    exit();
}
?>