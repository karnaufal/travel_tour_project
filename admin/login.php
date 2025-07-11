<?php
session_start(); // Mulai sesi untuk manajemen login
ob_start(); // Mulai output buffering

include_once '../config.php'; // Koneksi database

$message = ''; // Untuk pesan error/sukses
$status_class = ''; // Untuk styling pesan

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $message = "Username dan password wajib diisi.";
        $status_class = 'error';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, username, password FROM admins WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($admin && password_verify($password, $admin['password'])) {
                // Login berhasil
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];

                header("Location: index.php"); // Redirect ke halaman admin utama
                exit();
            } else {
                $message = "Username atau password salah.";
                $status_class = 'error';
            }
        } catch (PDOException $e) {
            $message = "Terjadi kesalahan database: " . htmlspecialchars($e->getMessage());
            $status_class = 'error';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* Tambahan style khusus untuk halaman login jika diperlukan */
        .login-container {
            max-width: 400px;
            margin: 80px auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .login-container h1 {
            margin-bottom: 30px;
            color: #333;
        }
        .login-container .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        .login-container label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: bold;
        }
        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: calc(100% - 20px);
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        .login-container button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .login-container button:hover {
            background-color: #0056b3;
        }
        .form-status-message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 16px;
        }
        .form-status-message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <main>
        <div class="login-container">
            <h1>Admin Login ⚙️</h1>

            <?php if (!empty($message)): ?>
                <div class="form-status-message <?php echo $status_class; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required autocomplete="username">
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password">
                </div>
                <button type="submit">Login</button>
            </form>
        </div>
    </main>
</body>
</html>
<?php
ob_end_flush(); // Akhiri output buffering
?>