<?php

// Rahasia Ilahi untuk Koneksi Database Kita!
$host = 'localhost'; // Server database kita, biasanya ini
$db_name = 'db_travel_tour'; // Nama database yang tadi kita buat di phpMyAdmin (ini harus sama persis!)
$username = 'root'; // Username default Laragon (kalau kamu gak ganti)
$password = ''; // Password default Laragon (kosong kalau kamu gak set)

// Coba konekin ke database pake PDO, biar lebih aman dan modern!
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $username, $password);
    // Set mode error, biar kalau ada salah, PHP-nya teriak!
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Koneksi ke database sukses, Bosku! 😎"; // Kalau mau ngecek, bisa diuncomment ini
} catch (PDOException $e) {
    // Kalau gagal, kita tampilkan pesan error yang jelas, jangan sampai user ngira server down!
    die("Koneksi database gagal total: " . $e->getMessage() . " 😭");
}

?>