<?php
require_once '../includes/functions.php';
redirect_if_not_logged_in();
redirect_if_not_pasien();
require_once '../includes/db.php';

if (isset($_POST['id_dokter'], $_POST['tanggal'])) {
    $dokter_id = intval($_POST['id_dokter']);
    $tanggal = $_POST['tanggal'];

    if (!$dokter_id || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
        http_response_code(400);
        exit;
    }

    $timezone = new DateTimeZone('Asia/Jakarta');
    $sekarang = new DateTimeImmutable('now', $timezone);
    $tanggalDipilih = DateTimeImmutable::createFromFormat('!Y-m-d', $tanggal, $timezone);

    if (!$tanggalDipilih || $tanggalDipilih->format('Y-m-d') !== $tanggal || $tanggalDipilih < $sekarang->setTime(0, 0)) {
        echo "<option value=''>Tidak ada jam tersedia</option>";
        exit;
    }

    // Konversi tanggal ke hari (dalam bahasa Indonesia)
    $hariEn = date('l', strtotime($tanggal));
    $hariMap = [
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu',
        'Sunday' => 'Minggu'
    ];
    $hari = $hariMap[$hariEn] ?? '';

    // Ambil semua entri jadwal sesuai hari dan dokter
    $stmt = $conn->prepare("SELECT jam_mulai, jam_selesai FROM jadwal_dokter WHERE id_dokter = ? AND hari = ? ORDER BY jam_mulai");
    $stmt->bind_param("is", $dokter_id, $hari);
    $stmt->execute();
    $result = $stmt->get_result();

    $options = "";
    while ($row = $result->fetch_assoc()) {
        $jam_mulai = substr($row['jam_mulai'], 0, 5);
        $jam_selesai = substr($row['jam_selesai'], 0, 5);
        $waktuMulai = DateTimeImmutable::createFromFormat('!Y-m-d H:i:s', $tanggal . ' ' . $row['jam_mulai'], $timezone);

        if ($waktuMulai && $waktuMulai <= $sekarang) {
            continue;
        }

        // Cek apakah jam_mulai sudah direservasi
        $cek = $conn->prepare("SELECT COUNT(*) AS total FROM reservations WHERE id_dokter = ? AND tanggal = ? AND jam = ?");
        $cek->bind_param("iss", $dokter_id, $tanggal, $row['jam_mulai']);
        $cek->execute();
        $cek_result = $cek->get_result()->fetch_assoc();

        if ($cek_result['total'] == 0) {
            $value = htmlspecialchars($row['jam_mulai'], ENT_QUOTES, 'UTF-8');
            $label = htmlspecialchars("$jam_mulai - $jam_selesai", ENT_QUOTES, 'UTF-8');
            $options .= "<option value='{$value}'>{$label}</option>";
        }
    }

    echo $options ?: "<option value=''>Tidak ada jam tersedia</option>";
}
