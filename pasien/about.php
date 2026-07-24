<?php
session_start();

if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'pasien') {
    include '../includes/header.php';
} else {
    include '../includes/header_pengunjung.php';
}
?>

<main class="page-main">
    <div class="page-shell">
        <section class="page-hero">
            <h1>
            <span class="hero-badge">
            Ratna Dental Care</span>
            </h1>
        </section>

        <section class="content-panel">
            <h2>Tentang Ratna Dental Care</h2>
            <p>
                <strong>Ratna Dental Care</strong> hadir untuk membantu pasien menjaga kesehatan gigi dan mulut
                dengan pelayanan yang ramah, tertata, dan mudah dipahami.
            </p>
            <p>
                Dengan dukungan dokter gigi berpengalaman dan staf yang ramah, kami membantu pasien
                menentukan perawatan sesuai kondisi dan kebutuhan.
            </p>

            <h3><i class="fas fa-eye"></i> Visi</h3>
            <p>Menjadi klinik gigi pilihan masyarakat yang mengutamakan kualitas, kenyamanan, dan kepuasan pasien.</p>

            <h3><i class="fas fa-bullseye"></i> Misi</h3>
            <ul>
                <li>Memberikan layanan gigi dengan standar yang baik dan pendekatan humanis.</li>
                <li>Mengedukasi pasien tentang pentingnya menjaga kesehatan gigi dan mulut.</li>
                <li>Menjaga proses reservasi dan perawatan tetap jelas.</li>
                <li>Menciptakan suasana klinik yang bersih, nyaman, dan bersahabat.</li>
            </ul>

            <h3><i class="fas fa-clock"></i> Jadwal Buka</h3>
            <div class="info-box">
                <p><strong>Senin - Sabtu:</strong> 09:00 - 17:30 WIB<br>
                <strong>Minggu & Libur Nasional:</strong> Tutup</p>
            </div>

            <h3><i class="fas fa-map-marker-alt"></i> Lokasi & Kontak</h3>
            <div class="info-box">
                <p>
                    <strong>Alamat:</strong> JL. Lumbu Timur Raya No. 129 Blok 5 Rt 02 Rw 030 Bojong Rawalumbu Kota Bekasi<br>
                    <strong>Telepon:</strong> 0812-1972-2457
                </p>
            </div>
        </section>
    </div>
</main>

<?php include '../includes/footer.php'; ?>