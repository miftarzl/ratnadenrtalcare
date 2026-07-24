<?php
function render_admin_reservation_rows($conn) {
    $reservasi = $conn->query("
        SELECT r.id_reservasi, u.username AS pasien, d.nama AS dokter, r.tanggal, r.jam, r.status
        FROM reservations r
        JOIN users u ON r.id_pasien = u.id_user
        JOIN doctors d ON r.id_dokter = d.id_dokter
        ORDER BY r.tanggal DESC, r.jam DESC
    ");

    if (!$reservasi || $reservasi->num_rows === 0) {
        echo "<tr><td colspan='6' data-label='Reservasi'>Belum ada reservasi.</td></tr>";
        return;
    }

    while ($row = $reservasi->fetch_assoc()) {
        $status_class = 'status-' . strtolower($row['status']);
        ?>
        <tr>
            <td data-label="Pasien"><?= htmlspecialchars($row['pasien']) ?></td>
            <td data-label="Dokter"><?= htmlspecialchars($row['dokter']) ?></td>
            <td data-label="Tanggal"><?= date("d M Y", strtotime($row['tanggal'])) ?></td>
            <td data-label="Jam"><?= substr($row['jam'], 0, 5) ?></td>
            <td data-label="Status" class="<?= $status_class ?>"><?= ucfirst($row['status']) ?></td>
            <td data-label="Aksi">
                <div class="admin-actions">
                    <?php if ($row['status'] === 'pending'): ?>
                        <form method="POST" onsubmit="return confirm('Konfirmasi reservasi ini?')">
                            <?= csrf_input() ?>
                            <input type="hidden" name="id_reservasi" value="<?= (int) $row['id_reservasi'] ?>">
                            <input type="hidden" name="action" value="confirmed">
                            <button type="submit" class="btn-admin">
                                <i class="fas fa-check"></i> Konfirmasi
                            </button>
                        </form>
                        <form method="POST" onsubmit="return confirm('Batalkan reservasi ini?')">
                            <?= csrf_input() ?>
                            <input type="hidden" name="id_reservasi" value="<?= (int) $row['id_reservasi'] ?>">
                            <input type="hidden" name="action" value="cancelled">
                            <button type="submit" class="btn-danger">
                                <i class="fas fa-xmark"></i> Batalkan
                            </button>
                        </form>
                    <?php else: ?>
                        <span>-</span>
                    <?php endif; ?>
                </div>
            </td>
        </tr>
        <?php
    }
}
?>
