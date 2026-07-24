<?php
session_start();
require_once 'includes/db.php';

$pesan = "";
$berhasil = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $konfirmasi = $_POST['konfirmasi_password'] ?? '';

    if (strlen($username) < 3 || strlen($username) > 15 || !preg_match('/^[A-Za-z0-9_]+$/', $username)) {
        $pesan = "Username harus 3-15 karakter dan hanya boleh huruf, angka, atau underscore.";
    } elseif (strlen($password) < 8) {
        $pesan = "Password minimal 8 karakter.";
    } elseif ($password !== $konfirmasi) {
        $pesan = "Password dan konfirmasi tidak cocok!";
    } else {
        $cek = $conn->prepare("SELECT id_user FROM users WHERE username = ?");
        $cek->bind_param("s", $username);
        $cek->execute();
        $hasil = $cek->get_result();

        if ($hasil->num_rows > 0) {
            $pesan = "Username sudah digunakan!";
        } else {
            $password_hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'pasien')");
            $stmt->bind_param("ss", $username, $password_hashed);

            if ($stmt->execute()) {
                $berhasil = true;
            } else {
                $pesan = "Gagal menyimpan ke database.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Klinik Gigi</title>
<?php $basePath = (strpos($_SERVER['REQUEST_URI'] ?? '', '/klinikdoktergigi/') !== false) ? '/klinikdoktergigi/' : '/'; ?>
    <link rel="stylesheet" href="<?= $basePath ?>assets/css/site.css?v=2">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
</head>
<body class="login-page">
    <div class="login-box">
        <h2>Daftar Akun Pasien</h2>
        <p class="login-subtitle">Buat akun untuk reservasi dan memantau status kunjungan Anda.</p>

        <?php if ($pesan): ?>
            <div class="pesan"><?= htmlspecialchars($pesan) ?></div>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" required minlength="3" maxlength="15" pattern="[A-Za-z0-9_]+">

            <label for="password">Password</label>
            <input type="password" name="password" id="password" required minlength="8">

            <label for="konfirmasi_password">Konfirmasi Password</label>
            <input type="password" name="konfirmasi_password" id="konfirmasi_password" required minlength="8">

            <button type="submit">Daftar</button>
        </form>

        <p class="login-link">
            Sudah punya akun? <a href="login.php">Masuk di sini</a>
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php if ($berhasil): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: 'Pendaftaran berhasil, silakan login.',
            confirmButtonText: 'Ke Halaman Login'
        }).then(() => {
            window.location.href = 'login.php';
        });
    </script>
    <?php endif; ?>
</body>
</html>
