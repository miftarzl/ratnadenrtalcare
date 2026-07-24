<?php
require_once '../includes/functions.php';
redirect_if_not_logged_in();
redirect_if_not_admin();
require_once '../includes/db.php';
require_once 'reservation_rows_partial.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token();

    $id = intval($_POST['id_reservasi'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($id && in_array($action, ['confirmed', 'cancelled'], true)) {
        $stmt = $conn->prepare("UPDATE reservations SET status = ? WHERE id_reservasi = ? AND status = 'pending'");
        $stmt->bind_param("si", $action, $id);
        $stmt->execute();

        header("Location: reservations.php?success=" . $action);
        exit;
    }
}

include '../includes/header.php';
?>

<section class="admin-page">
    <div class="admin-hero">
        <span class="eyebrow">Data Reservasi</span>
        <h1>Kelola Reservasi</h1>
        <p>Konfirmasi atau batalkan reservasi pasien dengan tampilan yang lebih mudah dipindai.</p>
    </div>

    <?php if (isset($_GET['success']) && $_GET['success'] === 'confirmed'): ?>
        <div class="admin-alert success">Reservasi berhasil dikonfirmasi.</div>
    <?php elseif (isset($_GET['success']) && $_GET['success'] === 'cancelled'): ?>
        <div class="admin-alert danger">Reservasi berhasil dibatalkan.</div>
    <?php endif; ?>

    <section class="admin-panel">
        <div class="admin-panel-head">
            <h2>Daftar Reservasi</h2>
            <span class="admin-live-status" id="reservationLiveStatus">
                <i class="fas fa-rotate"></i> Live update aktif
            </span>
        </div>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Pasien</th>
                        <th>Dokter</th>
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="reservationTableBody">
                    <?php render_admin_reservation_rows($conn); ?>
                </tbody>
            </table>
        </div>
    </section>
</section>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const tableBody = document.getElementById("reservationTableBody");
    const liveStatus = document.getElementById("reservationLiveStatus");

    async function refreshReservations() {
        try {
            const response = await fetch(`reservation_rows.php?t=${Date.now()}`, {
                headers: { "X-Requested-With": "XMLHttpRequest" },
                cache: "no-store"
            });

            if (!response.ok) {
                throw new Error("Gagal memuat data reservasi.");
            }

            const html = await response.text();
            if (tableBody.innerHTML.trim() !== html.trim()) {
                tableBody.innerHTML = html;
            }

            liveStatus.innerHTML = '<i class="fas fa-check-circle"></i> Terupdate otomatis';
            liveStatus.classList.remove("is-error");
        } catch (error) {
            liveStatus.innerHTML = '<i class="fas fa-triangle-exclamation"></i> Update tertunda';
            liveStatus.classList.add("is-error");
        }
    }

    setInterval(refreshReservations, 7000);
});
</script>

<?php include '../includes/footer.php'; ?>
