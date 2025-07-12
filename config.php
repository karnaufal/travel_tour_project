<?php
$host = 'localhost';
$db   = 'db_travel_tour'; // Ganti dengan nama database kamu
$user = 'root'; // Ganti dengan username database kamu
$pass = ''; // Ganti dengan password database kamu (kosong jika XAMPP default)

$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Aktifkan mode error untuk PDO (menampilkan exception)
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,     // Ambil hasil query sebagai array asosiatif
    PDO::ATTR_EMULATE_PREPARES   => false,                // Matikan emulasi prepared statements untuk keamanan
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Jika koneksi gagal, hentikan skrip dan tampilkan pesan error
    // Di lingkungan produksi, log error ini dan berikan pesan user-friendly
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

// --- TAMBAHKAN BARIS INI UNTUK PAGINATION ---
define('ITEMS_PER_PAGE', 6); // Definisikan berapa tur per halaman
// --- AKHIR TAMBAHAN ---

?>