<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pasien') {
    header("Location: ../login.php");
    exit;
}

require_once '../includes/db.php';
include '../includes/header.php';

$pasien_id = $_SESSION['user_id'];
$query = "SELECT r.id_reservasi, d.nama AS dokter, r.tanggal, r.jam, r.status, r.antrian
          FROM reservations r
          JOIN doctors d ON r.id_dokter = d.id_dokter
          WHERE r.id_pasien = ?
          ORDER BY r.tanggal DESC, r.jam DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $pasien_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<main class="page-main">
    <div class="page-shell">
        <section class="page-hero">
            <div>
                <span class="eyebrow">Riwayat</span>
                <h1>Riwayat Reservasi Anda</h1>
                <p>Lihat status kunjungan, jadwal, dan nomor antrean reservasi yang sudah dibuat.</p>
            </div>
        </section>

        <section class="table-panel">
            <h2><i class="fas fa-history"></i> Data Reservasi</h2>

            <div class="export-btn">
                <a href="riwayat_reservasi_export_pdf.php" target="_blank"><i class="fas fa-file-pdf"></i> Export ke PDF</a>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Dokter</th>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Status</th>
                            <th>Antrian</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $no = 1;
                        while ($row = $result->fetch_assoc()): 
                            $status_class = 'status-' . strtolower($row['status']);
                        ?>
                        <tr>
                            <td data-label="No"><?= $no++; ?></td>
                            <td data-label="Dokter"><?= htmlspecialchars($row['dokter']); ?></td>
                            <td data-label="Tanggal"><?= date("d M Y", strtotime($row['tanggal'])); ?></td>
                            <td data-label="Jam"><?= substr($row['jam'], 0, 5); ?></td>
                            <td data-label="Status" class="<?= $status_class ?>"><?= ucfirst($row['status']); ?></td>
                            <td data-label="Antrian"><?= $row['antrian']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
