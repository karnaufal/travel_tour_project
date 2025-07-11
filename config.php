<?php
// Konfigurasi Database
$host = 'localhost'; // Biasanya localhost untuk development
$dbname = 'db_travel_tour'; // Nama database yang kamu buat di phpMyAdmin
$user = 'root'; // User default XAMPP/Laragon
$password = ''; // Password default XAMPP/Laragon (kosong)

// Buat koneksi PDO (PHP Data Objects)
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    // Set mode error PDO ke Exception agar error bisa ditangkap
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Koneksi database berhasil!"; // Pesan ini bisa dihapus setelah testing
} catch (PDOException $e) {
    // Jika koneksi gagal, tampilkan pesan error dan hentikan eksekusi script
    die("Koneksi database gagal: " . $e->getMessage());
}
?>