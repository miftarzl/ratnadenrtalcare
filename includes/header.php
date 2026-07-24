<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Ratna Dental Care</title>
  <link rel="stylesheet" href="/klinikdoktergigi/assets/css/site.css?v=2">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<?php if (isset($_SESSION['username']) && ($_SESSION['role'] ?? '') === 'admin'): ?>
<div class="admin-layout">
  <aside class="admin-sidebar" id="adminSidebar">
    <div class="admin-brand">
      <span class="admin-brand-logo">
        <img src="/klinikdoktergigi/assets/img/logo.webp" alt="Ratna Dental Care">
      </span>
      <span>Ratna Dental</span>
    </div>
    <nav class="admin-nav">
      <a href="dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
      <a href="doctors.php"><i class="fas fa-user-md"></i> Data Dokter</a>
      <a href="reservations.php"><i class="fas fa-calendar-check"></i> Data Reservasi</a>
      <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Keluar</a>
    </nav>
  </aside>

  <main class="admin-main">
    <div class="admin-topbar">
      <button class="admin-menu-toggle" onclick="toggleAdminMenu()"><i class="fas fa-bars"></i></button>
      <div>
        <strong>Admin Klinik</strong>
        <span><?= htmlspecialchars($_SESSION['username']) ?></span>
      </div>
    </div>

    <div class="admin-overlay" id="adminOverlay" onclick="toggleAdminMenu()"></div>

    <div class="admin-confirm" id="adminConfirm">
      <div class="admin-confirm-box">
        <h3 id="adminConfirmTitle">Konfirmasi Aksi</h3>
        <p id="adminConfirmText">Lanjutkan aksi ini?</p>
        <div class="admin-confirm-actions">
          <button type="button" class="btn-muted" onclick="closeAdminConfirm()">Batal</button>
          <a href="#" class="btn-danger" id="adminConfirmYes">Lanjutkan</a>
        </div>
      </div>
    </div>

    <script>
      function toggleAdminMenu() {
        document.getElementById('adminSidebar').classList.toggle('active');
        document.getElementById('adminOverlay').classList.toggle('active');
      }

      function closeAdminConfirm() {
        document.getElementById('adminConfirm').classList.remove('active');
      }

      document.addEventListener('click', function(e) {
        const trigger = e.target.closest('[data-confirm]');
        if (!trigger) return;

        e.preventDefault();
        document.getElementById('adminConfirmText').textContent = trigger.dataset.confirm;
        document.getElementById('adminConfirmYes').href = trigger.href;
        document.getElementById('adminConfirm').classList.add('active');
      });
    </script>
<?php else: ?>
<header class="site-header site-header-app">
  <div class="logo">
    <img src="/klinikdoktergigi/assets/img/logo.webp" alt="Ratna Dental Care">
    <span>Ratna Dental Care</span>
  </div>
  <button class="menu-toggle" onclick="toggleMenu()"><i class="fas fa-bars"></i></button>

  <nav class="app-nav" id="mainNav">
    <?php if(isset($_SESSION['username'])): ?>
      <?php if($_SESSION['role'] === 'admin'): ?>
        <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="doctors.php"><i class="fas fa-user-md"></i> Data Dokter</a>
        <a href="reservations.php"><i class="fas fa-calendar-check"></i> Data Reservasi</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Keluar</a>
      <?php elseif($_SESSION['role'] === 'pasien'): ?>
        <a href="dashboard.php"><i class="fas fa-home"></i> Beranda</a>
        <a href="reservation.php"><i class="fas fa-calendar-plus"></i> Reservasi</a>
        <a href="riwayat_reservasi.php"><i class="fas fa-history"></i> Riwayat</a>
        <a href="about.php"><i class="fas fa-info-circle"></i> Tentang</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Keluar</a>
      <?php endif; ?>
    <?php else: ?>
      <a href="<?= strpos($_SERVER['PHP_SELF'], '/pasien/') !== false ? '../login.php' : 'login.php' ?>"><i class="fas fa-sign-in-alt"></i> Login</a>
    <?php endif; ?>
  </nav>
</header>

<!-- Floating Buttons -->
<button onclick="scrollToTop()" class="scroll-top" title="Kembali ke Atas"><i class="fas fa-arrow-up"></i></button>

<div class="container">

<script>
  function toggleMenu() {
    const nav = document.getElementById('mainNav');
    nav.classList.toggle('active');
  }

  window.addEventListener('scroll', () => {
    const header = document.querySelector('header');
    const scrollBtn = document.querySelector('.scroll-top');
    if (window.scrollY > 50) {
      header.classList.add('scrolled');
    } else {
      header.classList.remove('scrolled');
    }

    scrollBtn.style.display = window.scrollY > 200 ? "block" : "none";
  });

  function scrollToTop() {
    window.scrollTo({ top: 0, behavior: "smooth" });
  }
</script>

<?php endif; ?>
