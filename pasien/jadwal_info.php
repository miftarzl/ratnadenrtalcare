<?php
require_once '../includes/functions.php';
redirect_if_not_logged_in();
redirect_if_not_pasien();
require_once '../includes/db.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_GET['id_dokter'])) {
    http_response_code(400);
    echo json_encode(["text" => "Dokter belum dipilih.", "days" => []]);
    exit;
}

$id = intval($_GET['id_dokter']);

$resNama = $conn->prepare("SELECT nama FROM doctors WHERE id_dokter = ?");
$resNama->bind_param("i", $id);
$resNama->execute();
$namaRow = $resNama->get_result()->fetch_assoc();

if (!$namaRow) {
    http_response_code(404);
    echo json_encode(["text" => "Dokter tidak ditemukan.", "days" => []]);
    exit;
}

$stmt = $conn->prepare("SELECT hari, MIN(jam_mulai) AS mulai, MAX(jam_selesai) AS selesai
                        FROM jadwal_dokter
                        WHERE id_dokter = ?
                        GROUP BY hari
                        ORDER BY FIELD(hari,'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu')");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

$hariAngka = [
    "Minggu" => 0,
    "Senin" => 1,
    "Selasa" => 2,
    "Rabu" => 3,
    "Kamis" => 4,
    "Jumat" => 5,
    "Sabtu" => 6
];

$jadwal = [];
$allowedDays = [];

while ($row = $result->fetch_assoc()) {
    $jadwal[] = "{$row['hari']} (" . substr($row['mulai'], 0, 5) . "-" . substr($row['selesai'], 0, 5) . ")";
    if (isset($hariAngka[$row['hari']])) {
        $allowedDays[] = $hariAngka[$row['hari']];
    }
}

echo json_encode([
    "text" => "Jadwal {$namaRow['nama']}: " . implode(", ", $jadwal),
    "days" => $allowedDays
]);
