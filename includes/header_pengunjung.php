<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ratna Dental Care</title>
  <link rel="stylesheet" href="/klinikdoktergigi/assets/css/site.css?v=2">
  <link rel="stylesheet" href="/klinikdoktergigi/assets/css/chatbot.css?v=2">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<header class="site-header site-header-public" id="mainHeader">
  <div class="logo">
    <img src="/klinikdoktergigi/assets/img/logo.webp" alt="Ratna Dental Care">
    <span>Ratna Dental Care</span>
  </div>
  <button class="nav-toggle" onclick="toggleMenu()"><i class="fas fa-bars"></i></button>
  <nav class="nav-public" id="navMenu">
    <a href="/klinikdoktergigi/index.php">Beranda</a>
    <a href="/klinikdoktergigi/#layanan">Layanan</a>
    <a href="/klinikdoktergigi/pasien/about.php">Tentang</a>
    <a href="/klinikdoktergigi/login.php">Masuk/Register</a>
  </nav>
</header>

<div class="container">
<!-- Konten halaman dimulai di sini -->

<script>
  function toggleMenu() {
    const nav = document.getElementById('navMenu');
    nav.classList.toggle('show');
  }

  window.addEventListener('scroll', () => {
    const header = document.getElementById('mainHeader');
    if (window.scrollY > 50) {
      header.classList.add('scrolled');
    } else {
      header.classList.remove('scrolled');
    }
  });
</script>
