<?php
require_once '../includes/functions.php';
redirect_if_not_logged_in();
redirect_if_not_admin();
require_once '../includes/db.php';

if (isset($_GET['id_dokter'])) {
    $dokter_id = intval($_GET['id_dokter']);

    $stmt = $conn->prepare("SELECT hari, jam_mulai, jam_selesai 
                            FROM jadwal_dokter 
                            WHERE id_dokter = ? 
                            ORDER BY FIELD(hari,'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'), jam_mulai");
    $stmt->bind_param("i", $dokter_id);
    $stmt->execute();
    $res = $stmt->get_result();

    $output = [];
    while ($row = $res->fetch_assoc()) {
        $output[] = "{$row['hari']}: " . substr($row['jam_mulai'], 0, 5) . "-" . substr($row['jam_selesai'], 0, 5);
    }

    echo implode("\n", $output);
}
