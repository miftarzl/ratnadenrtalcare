<?php
// Koneksi database dengan dukungan Environment Variables (Docker / Production)
$host     = getenv('DB_HOST') ?: "127.0.0.1";
$user     = getenv('DB_USER') ?: "root";
$password = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : "";
$dbname   = getenv('DB_NAME') ?: "klinik_dokter_gigi";
$port     = getenv('DB_PORT') ? (int)getenv('DB_PORT') : 3307;

$conn = new mysqli($host, $user, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
