<?php
// Halaman login
session_start();
if (isset($_SESSION['user_id'])) {
    // Redirect sesuai role
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: pasien/dashboard.php");
    }
    exit();
}
require 'includes/db.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if ($username && $password) {
        $stmt = $conn->prepare("SELECT id_user, username, password, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {

        if (password_verify($password, $row['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $row['id_user'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];

            if ($row['role'] === 'admin') {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: pasien/dashboard.php");
            }
            exit();
        } else {
                $error = "Password salah";
            }
        } else {
            $error = "Username tidak ditemukan";
        }
    } else {
        $error = "Harap isi username dan password";
    }
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login - Klinik Dokter Gigi</title>

<?php $basePath = (strpos($_SERVER['REQUEST_URI'] ?? '', '/klinikdoktergigi/') !== false) ? '/klinikdoktergigi/' : '/'; ?>
    <link rel="stylesheet" href="<?= $basePath ?>assets/css/site.css?v=2">
</head>
<body class="login-page">
    <div class="login-box">
        <h2>Klinik Dokter Gigi</h2>
        <p class="login-subtitle">Masuk untuk membuat reservasi dan melihat riwayat kunjungan Anda.</p>
        <?php if($error): ?>
            <div class="error"><?=htmlspecialchars($error)?></div>
        <?php endif; ?>
        <form method="post" action="">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" required />
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required />
            <input type="submit" value="Masuk" />
        </form>
        <p class="login-link">
            Belum punya akun? <a href="register.php">Daftar di sini</a>
        </p>

    </div>
</body>
</html>
