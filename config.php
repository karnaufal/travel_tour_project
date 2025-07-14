<?php
$host = 'localhost';
$db   = 'db_travel_tour'; // Ganti dengan nama database kamu
$user = 'root'; // Ganti dengan username database kamu
$pass = ''; // Ganti dengan password database kamu (kosong jika Laragon/XAMPP default)

$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Aktifkan mode error untuk PDO
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,     // Ambil hasil query sebagai array asosiatif
    PDO::ATTR_EMULATE_PREPARES   => false,                // Matikan emulasi prepared statements
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Di lingkungan produksi, log error ini dan berikan pesan user-friendly
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

// Tidak lagi menggunakan ITEMS_PER_PAGE karena kita beralih ke "Load More"
// Tapi jika suatu saat butuh, definisikan di sini.
// define('ITEMS_PER_PAGE', 6);

?>

