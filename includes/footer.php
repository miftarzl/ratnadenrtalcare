<?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
</main>
</div>
<?php else: ?>
</div> <!-- Penutup .container dari header -->
<?php endif; ?>

<footer class="site-footer">
    <?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $basePath = (strpos($_SERVER['REQUEST_URI'] ?? '', '/klinikdoktergigi/') !== false) ? '/klinikdoktergigi/' : '/';
    ?>

    <div class="footer-inner">
        <div class="footer-col">
            <h3>Ratna Dental Care</h3>
            <p>Klinik gigi keluarga dengan layanan reservasi yang mudah dan tertata.</p>
        </div>

        <div class="footer-col footer-links">
            <h4>Link</h4>
            <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'pasien'): ?>
                <!-- Sudah login sebagai pasien -->
                <p><a href="<?= $basePath ?>pasien/dashboard.php">Beranda</a></p>
                <p><a href="<?= $basePath ?>pasien/about.php">Tentang Klinik</a></p>
                <p><a href="<?= $basePath ?>pasien/reservation.php">Reservasi</a></p>
            <?php else: ?>
                <!-- Pengunjung (belum login) -->
                <p><a href="<?= $basePath ?>index.php">Beranda</a></p>
                <p><a href="<?= $basePath ?>pasien/about.php">Tentang Klinik</a></p>
                <p><a href="<?= $basePath ?>login.php">Reservasi</a></p>
            <?php endif; ?>
        </div>

        <div class="footer-col">
            <h4>Contact</h4>
            <div class="footer-contact">
                <a class="footer-contact-item" href="https://wa.me/6281219722457" target="_blank" rel="noopener noreferrer" title="Hubungi melalui WhatsApp">
                    <i class="bi bi-whatsapp"></i>
                    <span>
                        <strong>WhatsApp</strong>
                        <small>0812 1972 2457</small>
                    </span>
                </a>
                <a class="footer-contact-item" href="https://maps.app.goo.gl/PJqgnzXjAQ2jN165A" target="_blank" rel="noopener noreferrer" title="Buka lokasi di Google Maps">
                    <i class="bi bi-geo-alt"></i>
                    <span>
                        <strong>Alamat</strong>
                        <small>Jl. Lumbu Timur Raya No. 129 Blok 5 RT 02 RW 030 Bojong Rawalumbu, Kota Bekasi</small>
                    </span>
                </a>
            </div>
        </div>  
    </div>
    <hr class="footer-rule">
    <p class="footer-copy">&copy; <?= date('Y') ?> Klinik Ratna Dental Care. All Rights Reserved.</p>
</footer>

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

</body>
</html>
