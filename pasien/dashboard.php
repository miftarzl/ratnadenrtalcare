<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'pasien') {
    header("Location: ../index.php");
    exit;
}
require_once '../includes/db.php';
require_once '../includes/layanan_data.php';
include '../includes/header.php';

$id_user = $_SESSION['user_id'];

// Ambil reservasi terakhir
$stmt = $conn->prepare("SELECT tanggal, jam, status FROM reservations WHERE id_pasien = ? ORDER BY updated_at DESC LIMIT 1");
$stmt->bind_param("i", $id_user);
$stmt->execute();
$notif = $stmt->get_result()->fetch_assoc();

// Hitung total reservasi dan status per kategori
$total_res = $conn->prepare("SELECT COUNT(*) as total FROM reservations WHERE id_pasien = ?");
$total_res->bind_param("i", $id_user);
$total_res->execute();
$total = $total_res->get_result()->fetch_assoc()['total'];

$status_counts = ['confirmed' => 0, 'pending' => 0, 'cancelled' => 0];
$status_stmt = $conn->prepare("SELECT status, COUNT(*) as count FROM reservations WHERE id_pasien = ? GROUP BY status");
$status_stmt->bind_param("i", $id_user);
$status_stmt->execute();
$result_status = $status_stmt->get_result();
while ($row = $result_status->fetch_assoc()) {
    $status_counts[$row['status']] = $row['count'];
}

$pesan_notif = "";
$kelas = "";
if ($notif) {
    $tgl = date("d M Y", strtotime($notif['tanggal']));
    $jam = date("H:i", strtotime($notif['jam']));
    switch ($notif['status']) {
        case 'confirmed':
            $pesan_notif = "Reservasi pada $tgl pukul $jam telah <strong>DIKONFIRMASI</strong>.";
            $kelas = "notif success";
            break;
        case 'pending':
            $pesan_notif = "Reservasi pada $tgl pukul $jam masih <strong>MENUNGGU KONFIRMASI</strong>.";
            $kelas = "notif info";
            break;
        case 'cancelled':
            $pesan_notif = "Reservasi pada $tgl pukul $jam telah <strong>DIBATALKAN</strong>.";
            $kelas = "notif danger";
            break;
    }

    $last_status = $_SESSION['last_status'] ?? null;
    if ($notif['status'] !== $last_status) {
        $_SESSION['show_notif'] = true;
        $_SESSION['last_status'] = $notif['status'];
    }
}
?>

<main class="dashboard-main">
    <div class="dashboard">
        <?php if ($pesan_notif && isset($_SESSION['show_notif'])): ?>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
        Swal.fire({
            icon: '<?= $notif['status'] === 'confirmed' ? 'success' : ($notif['status'] === 'cancelled' ? 'error' : 'info') ?>',
            title: 'Status Reservasi',
            html: '<?= addslashes($pesan_notif) ?>',
            timer: 4000,
            timerProgressBar: true,
            showConfirmButton: false
        });
        </script>
        <?php unset($_SESSION['show_notif']); ?>
        <?php endif; ?>

        <section class="dashboard-hero">
            <div>
                <span class="eyebrow">Area Pasien</span>
                <h1 id="greeting-text">Halo, <?= htmlspecialchars($_SESSION['username']) ?></h1>
                <p id="greeting-desc">
                    Pantau reservasi Anda dan pilih layanan perawatan gigi dari satu tempat.
                </p>
            </div>
        </section>

        <?php if ($pesan_notif): ?>
            <div class="<?= $kelas ?>"><?= $pesan_notif ?></div>
        <?php endif; ?>

        <section class="info">
            <h2>Perawatan gigi dengan alur yang lebih jelas</h2>
            <p>
                Gunakan dashboard ini untuk membuat reservasi, melihat status kunjungan,
                dan memeriksa riwayat perawatan Anda.
            </p>
        </section>

        <section class="stats-grid" aria-label="Ringkasan reservasi">
            <div class="stats-card" title="Total Reservasi">
                <i class="fas fa-calendar-check"></i>
                <span>Total Reservasi: <?= $total ?></span>
            </div>
            <div class="stats-card stats-confirmed" title="Reservasi Dikonfirmasi">
                <i class="fas fa-check-circle"></i>
                <span>Dikonfirmasi: <?= $status_counts['confirmed'] ?></span>
            </div>
            <div class="stats-card stats-pending" title="Reservasi Pending">
                <i class="fas fa-hourglass-half"></i>
                <span>Pending: <?= $status_counts['pending'] ?></span>
            </div>
            <div class="stats-card stats-cancelled" title="Reservasi Dibatalkan">
                <i class="fas fa-times-circle"></i>
                <span>Dibatalkan: <?= $status_counts['cancelled'] ?></span>
            </div>
        </section>

        <section class="spesialis">
            <div class="service-head">
                <div>
                    <p class="section-kicker">Layanan</p>
                    <h2 class="section-title">Perawatan yang tersedia</h2>
                </div>
            </div>

            <div class="layanan-wrapper">
                <?php
                render_layanan_cards('../assets/img');
                ?>
            </div>
        </section>
    </div>
</main>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const now = new Date();
    const hour = now.getHours();
    let greet = "Halo";

    if (hour >= 5 && hour < 10) {
        greet = "Selamat Pagi";
    } else if (hour >= 10 && hour < 15) {
        greet = "Selamat Siang";
    } else if (hour >= 15 && hour < 18) {
        greet = "Selamat Sore";
    } else {
        greet = "Selamat Malam";
    }

    const user = <?= json_encode($_SESSION['username']) ?>;
    document.getElementById("greeting-text").textContent = `${greet}, ${user}`;
});
</script>

<?php include '../includes/footer.php'; ?>
