<?php
date_default_timezone_set('Asia/Jakarta');

function ensure_session_started() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function is_logged_in() {
    ensure_session_started();
    return isset($_SESSION['user_id']);
}

function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function is_pasien() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'pasien';
}

function redirect_if_not_logged_in() {
    if (!is_logged_in()) {
        header("Location: ../login.php");
        exit();
    }
}

function redirect_if_not_admin() {
    if (!is_admin()) {
        header("Location: ../login.php");
        exit();
    }
}

function redirect_if_not_pasien() {
    if (!is_pasien()) {
        header("Location: ../login.php");
        exit();
    }
}

function csrf_token() {
    ensure_session_started();

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_input() {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

function verify_csrf_token() {
    ensure_session_started();
    $token = $_POST['csrf_token'] ?? '';

    if (!$token || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        exit('Token keamanan tidak valid.');
    }
}
?>
