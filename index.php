<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/layanan_data.php';
include 'includes/header_pengunjung.php';

$is_logged_in = isset($_SESSION['username']) && ($_SESSION['role'] ?? '') === 'pasien';
$pasien_id = $_SESSION['user_id'] ?? null;

$total = 0;
if ($is_logged_in && $pasien_id) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM reservations WHERE id_pasien = ?");
    $stmt->bind_param("i", $pasien_id);
    $stmt->execute();
    $total = $stmt->get_result()->fetch_assoc()['total'];
}
?>

<main class="home-main">
    <section class="home-hero home-hero-fallback">
        <div class="hero-inner">
            <div class="hero-copy-block reveal">
                <p class="eyebrow">Ratna Dental Care</p>
                <h1>Perawatan gigi yang terasa tenang sejak reservasi</h1>
                <p class="hero-copy">
                    Klinik gigi keluarga di Bekasi dengan jadwal yang tertata, dokter berpengalaman,
                    dan proses reservasi yang dibuat sederhana untuk pasien.
                </p>
                <div class="hero-actions">
                    <a class="btn-primary" href="<?= $is_logged_in ? 'pasien/reservation.php' : 'login.php' ?>">
                        <i class="fas fa-calendar-check"></i>
                        Buat Reservasi
                    </a>
                    <a class="btn-secondary" href="#layanan">
                        <i class="fas fa-arrow-down"></i>
                        Lihat Layanan
                    </a>
                </div>
            </div>

            <div class="hero-metrics reveal" style="--delay: .12s;">
                <div>
                    <strong><?= count(get_layanan_list()) ?></strong>
                    <span>Layanan utama</span>
                </div>
                <div>
                    <strong><?= $is_logged_in ? $total : 'Cepat' ?></strong>
                    <span><?= $is_logged_in ? 'Reservasi Anda' : 'Reservasi online' ?></span>
                </div>
                <div>
                    <strong>Bekasi</strong>
                    <span>Rawalumbu</span>
                </div>
            </div>

            <div class="hero-proof reveal" style="--delay: .18s;">
                <span><i class="fas fa-location-dot"></i> JL. Lumbu Timur Raya No. 129</span>
                <span><i class="fas fa-clock"></i> Senin - Sabtu, 09:00 - 17:30 WIB</span>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="intro-grid reveal">
            <div class="intro-copy">
                <p class="section-kicker">Tentang Klinik</p>
                <h2 class="section-title">Klinik gigi keluarga dengan layanan yang mudah dipahami.</h2>
                <p class="section-text">
                    Kami membantu pasien merencanakan perawatan gigi dengan informasi yang jelas,
                    jadwal yang tertata, dan pelayanan yang nyaman untuk kunjungan rutin maupun tindakan lanjutan.
                </p>
            </div>

            <div class="clinic-note reveal" style="--delay: .12s;">
                <i class="fas fa-tooth"></i>
                <strong>
                    <?= $is_logged_in ? 'Selamat datang kembali.' : 'Belum punya akun pasien?' ?>
                </strong>
                <p class="section-text" style="margin:0;">
                    <?= $is_logged_in
                        ? "Anda sudah memiliki $total riwayat reservasi di sistem."
                        : "Daftar atau masuk untuk membuat reservasi dan memantau status kunjungan Anda." ?>
                </p>
            </div>
        </div>
    </section>

    <section class="section" id="layanan">
        <div class="service-head reveal">
            <div>
                <p class="section-kicker">Layanan</p>
                <h2 class="section-title">Perawatan yang tersedia</h2>
            </div>
            <p>
                Pilih layanan sesuai kebutuhan, lalu buat reservasi agar tim klinik dapat menyiapkan
                jadwal kunjungan Anda dengan lebih rapi.
            </p>
        </div>

        <div class="layanan-wrapper reveal">
            <?php
            render_layanan_cards('assets/img');
            ?>
        </div>
    </section>

    <section class="section process-section">
        <div class="service-head reveal">
            <div>
                <p class="section-kicker">Alur Kunjungan</p>
                <h2 class="section-title">Dibuat jelas dari awal.</h2>
            </div>
            <p>
                Anda tidak perlu menebak prosesnya. Semua dimulai dari reservasi, konfirmasi jadwal,
                lalu kunjungan sesuai kebutuhan perawatan.
            </p>
        </div>

        <div class="process-grid">
            <article class="process-card reveal">
                <span>01</span>
                <h3>Pilih jadwal</h3>
                <p>Tentukan dokter dan waktu kunjungan yang tersedia melalui sistem reservasi.</p>
            </article>
            <article class="process-card reveal" style="--delay: .08s;">
                <span>02</span>
                <h3>Konfirmasi</h3>
                <p>Admin memeriksa permintaan dan mengatur status reservasi Anda.</p>
            </article>
            <article class="process-card reveal" style="--delay: .16s;">
                <span>03</span>
                <h3>Datang periksa</h3>
                <p>Kunjungi klinik sesuai jadwal dan konsultasikan rencana perawatan dengan dokter.</p>
            </article>
        </div>
    </section>

    <section class="home-cta reveal">
        <div>
            <p class="section-kicker">Reservasi</p>
            <h2>Siap merapikan jadwal kunjungan gigi Anda?</h2>
        </div>
        <a class="btn-primary btn-dark" href="<?= $is_logged_in ? 'pasien/reservation.php' : 'login.php' ?>">
            <i class="fas fa-calendar-plus"></i>
            Mulai Sekarang
        </a>
    </section>
</main>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const revealItems = document.querySelectorAll(".reveal");
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add("is-visible");
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12 });

    revealItems.forEach((item) => observer.observe(item));
});
</script>

<?php include 'includes/chatbot_widget.php'; ?>

<?php $basePath = (strpos($_SERVER['REQUEST_URI'] ?? '', '/klinikdoktergigi/') !== false) ? '/klinikdoktergigi/' : '/'; ?>
<script src="<?= $basePath ?>assets/js/chatbot.js?v=4"></script>

<?php include 'includes/footer.php'; ?>
