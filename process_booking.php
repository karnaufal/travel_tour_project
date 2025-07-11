<?php
include 'config.php'; // Panggil file koneksi database kita

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tour_id = filter_var($_POST['tour_id'] ?? '', FILTER_SANITIZE_NUMBER_INT);
    $tour_name = filter_var($_POST['tour_name'] ?? '', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $customer_name = filter_var($_POST['customer_name'] ?? '', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $customer_email = filter_var($_POST['customer_email'] ?? '', FILTER_SANITIZE_EMAIL);
    $num_participants = filter_var($_POST['num_participants'] ?? '', FILTER_SANITIZE_NUMBER_INT);

    $errors = [];
    if (empty($tour_id) || !is_numeric($tour_id) || $tour_id <= 0) {
        $errors[] = "ID Tur tidak valid.";
    }
    if (empty($customer_name)) {
        $errors[] = "Nama lengkap wajib diisi.";
    }
    if (empty($customer_email) || !filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email tidak valid atau kosong.";
    }
    if (empty($num_participants) || !is_numeric($num_participants) || $num_participants <= 0) {
        $errors[] = "Jumlah peserta tidak valid atau kosong.";
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO bookings (tour_id, customer_name, customer_email, num_participants) VALUES (:tour_id, :customer_name, :customer_email, :num_participants)");

            $stmt->bindParam(':tour_id', $tour_id, PDO::PARAM_INT);
            $stmt->bindParam(':customer_name', $customer_name, PDO::PARAM_STR);
            $stmt->bindParam(':customer_email', $customer_email, PDO::PARAM_STR);
            $stmt->bindParam(':num_participants', $num_participants, PDO::PARAM_INT);

            $stmt->execute();

            header("Location: detail_tour.php?id=" . $tour_id . "&status=success&tour_name=" . urlencode($tour_name) . "&customer_name=" . urlencode($customer_name));
            exit();

        } catch (PDOException $e) {
            header("Location: detail_tour.php?id=" . $tour_id . "&status=error&message=" . urlencode("Terjadi kesalahan saat menyimpan pesanan: " . $e->getMessage()));
            exit();
        }
    } else {
        header("Location: detail_tour.php?id=" . $tour_id . "&status=error_validation&message=" . urlencode(implode("<br>", $errors)));
        exit();
    }

} else {
    header("Location: index.php");
    exit();
}
?>