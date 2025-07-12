<?php
session_start();
ob_start(); // Mulai output buffering

include_once '../config.php'; // Pastikan path ini benar

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validasi input
    if (empty($username) || empty($password)) {
        $message = "Username dan password harus diisi.";
    } else {
        // Cek kredensial admin
        // Untuk contoh ini, kita asumsikan username='admin' dan password='admin123'
        // DI PRODUKSI, GUNAKAN HASHING PASSWORD (misal password_hash dan password_verify)
        if ($username === 'admin' && $password === 'admin123') {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $username;
            header("Location: index.php"); // Redirect ke halaman utama admin
            exit();
        } else {
            $message = "Username atau password salah.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Travel Tour</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        /* Optional: Add specific login form styles here if not already in style.css */
        .login-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            margin: 80px auto;
            text-align: center;
        }
        .login-container h1 {
            color: #333;
            margin-bottom: 30px;
        }
        .login-container .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        .login-container label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }
        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: calc(100% - 22px);
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
            box-sizing: border-box;
        }
        .login-container button[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1.1em;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 10px;
        }
        .login-container button[type="submit"]:hover {
            background-color: #0056b3;
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <main>
        <div class="login-container">
            <h1>Login Admin</h1>
            <?php if (!empty($message)): ?>
                <div class="error-message">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            <form action="login.php" method="POST">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit">Login</button>
            </form>
        </div>
    </main>
</body>
</html>
<?php
ob_end_flush();
?>