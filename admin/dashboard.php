<?php
require_once '../includes/functions.php';
redirect_if_not_logged_in();
redirect_if_not_admin();
require_once '../includes/db.php';
include '../includes/header.php';

$jumlah_dokter = $conn->query("SELECT COUNT(*) AS total FROM doctors")->fetch_assoc()['total'];
$jumlah_reservasi = $conn->query("SELECT COUNT(*) AS total FROM reservations")->fetch_assoc()['total'];
$pending = $conn->query("SELECT COUNT(*) AS total FROM reservations WHERE status = 'pending'")->fetch_assoc()['total'];
$jumlah_pasien = $conn->query("SELECT COUNT(DISTINCT id_pasien) AS total FROM reservations")->fetch_assoc()['total'];
?>

<section class="admin-page">
    <div class="admin-hero">
        <span class="eyebrow">Dashboard</span>
        <h1>Ringkasan Klinik</h1>
        <p>Kelola dokter, reservasi, dan aktivitas pasien dari satu tempat yang rapi.</p>
    </div>

    <div class="admin-stats">
        <article class="admin-stat-card">
            <i class="fas fa-user-md"></i>
            <div>
                <h3>Jumlah Dokter</h3>
                <p><?= $jumlah_dokter ?></p>
            </div>
        </article>
        <article class="admin-stat-card">
            <i class="fas fa-calendar-check"></i>
            <div>
                <h3>Total Reservasi</h3>
                <p><?= $jumlah_reservasi ?></p>
            </div>
        </article>
        <article class="admin-stat-card">
            <i class="fas fa-clock"></i>
            <div>
                <h3>Reservasi Pending</h3>
                <p><?= $pending ?></p>
            </div>
        </article>
        <article class="admin-stat-card">
            <i class="fas fa-users"></i>
            <div>
                <h3>Total Pasien</h3>
                <p><?= $jumlah_pasien ?></p>
            </div>
        </article>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
