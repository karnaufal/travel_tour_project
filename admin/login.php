<?php
session_start(); // Mulai sesi

// Cek apakah admin sudah login, jika ya, arahkan langsung ke dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: dashboard.php");
    exit();
}

include_once '../config.php'; // Sesuaikan path ke file konfigurasi database

$error_message = ''; // Variabel untuk menyimpan pesan error

// Proses jika form login disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validasi input
    if (empty($username) || empty($password)) {
        $error_message = "Username dan password tidak boleh kosong.";
    } else {
        try {
            // Ambil data admin dari database berdasarkan username
            $stmt = $pdo->prepare("SELECT id, username, password FROM admins WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verifikasi password
            if ($user && password_verify($password, $user['password'])) {
                // Login berhasil
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_username'] = $username; // Simpan username admin ke sesi
                header("Location: dashboard.php"); // Arahkan ke dashboard admin
                exit();
            } else {
                // Login gagal
                $error_message = "Username atau password salah.";
            }
        } catch (PDOException $e) {
            $error_message = "Terjadi kesalahan database: " . $e->getMessage();
            // Untuk debugging, bisa tampilkan error: echo "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - JalanJalan Kuy!</title>
    <link rel="stylesheet" href="../css/style.css"> <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Gaya khusus untuk halaman login */
        body {
            background-color: #f4f7f6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            box-sizing: border-box; /* Pastikan padding tidak menyebabkan overflow */
        }
        .login-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .login-container h2 {
            margin-bottom: 30px;
            color: #007bff;
            font-size: 2.2em;
        }
        .login-form .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        .login-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }
        .login-form input[type="text"],
        .login-form input[type="password"] {
            width: calc(100% - 20px); /* Kurangi padding */
            padding: 12px 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
            box-sizing: border-box; /* Sertakan padding dalam lebar */
        }
        .login-form button[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 1.1em;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .login-form button[type="submit"]:hover {
            background-color: #0056b3;
        }
        .error-message {
            color: #dc3545; /* Merah untuk pesan error */
            background-color: #f8d7da; /* Latar belakang merah muda */
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 20px;
            font-size: 0.9em;
        }
        .back-to-home {
            margin-top: 25px;
            font-size: 0.95em;
        }
        .back-to-home a {
            color: #007bff;
            text-decoration: none;
        }
        .back-to-home a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h2>Login Admin</h2>
        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <form action="login.php" method="POST" class="login-form">
            <div class="form-group">
                <label for="username"><i class="fas fa-user"></i> Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form>
        <div class="back-to-home">
            <p><a href="../index.php">Kembali ke Beranda</a></p>
        </div>
    </div>

    </body>
</html>