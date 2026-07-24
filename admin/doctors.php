<?php
require_once '../includes/functions.php';
redirect_if_not_logged_in();
redirect_if_not_admin();
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token();

    if (($_POST['action'] ?? '') === 'delete') {
        $id = intval($_POST['id_dokter'] ?? 0);

        if ($id) {
            $delJadwal = $conn->prepare("DELETE FROM jadwal_dokter WHERE id_dokter = ?");
            $delJadwal->bind_param("i", $id);
            $delJadwal->execute();

            $delDokter = $conn->prepare("DELETE FROM doctors WHERE id_dokter = ?");
            $delDokter->bind_param("i", $id);
            $delDokter->execute();
        }

        header("Location: doctors.php?success=deleted");
        exit;
    }

    $nama = trim($_POST['nama'] ?? '');
    $jadwal = trim($_POST['jadwal'] ?? '');
    $id = isset($_POST['id_dokter']) ? intval($_POST['id_dokter']) : null;

    if ($nama && $jadwal) {
        if ($id) {
            $stmt = $conn->prepare("UPDATE doctors SET nama = ? WHERE id_dokter = ?");
            $stmt->bind_param("si", $nama, $id);
            $stmt->execute();

            $del = $conn->prepare("DELETE FROM jadwal_dokter WHERE id_dokter = ?");
            $del->bind_param("i", $id);
            $del->execute();
        } else {
            $stmt = $conn->prepare("INSERT INTO doctors (nama) VALUES (?)");
            $stmt->bind_param("s", $nama);
            $stmt->execute();
            $id = $conn->insert_id;
        }

        $baris = explode("\n", $jadwal);
        foreach ($baris as $bar) {
            if (preg_match("/^([A-Za-z]+):\s*(\d{2}:\d{2})\s*[-–]\s*(\d{2}:\d{2})$/u", trim($bar), $m)) {
                $hari = ucfirst(strtolower($m[1]));
                $mulai = $m[2];
                $selesai = $m[3];
                $stmt2 = $conn->prepare("INSERT INTO jadwal_dokter (id_dokter, hari, jam_mulai, jam_selesai) VALUES (?, ?, ?, ?)");
                $stmt2->bind_param("isss", $id, $hari, $mulai, $selesai);
                $stmt2->execute();
            }
        }

        header("Location: doctors.php?success=1");
        exit;
    }
}

$data = $conn->query("SELECT * FROM doctors ORDER BY nama ASC");

function getJadwalUtama($conn, $id) {
    $stmt = $conn->prepare("SELECT hari, MIN(jam_mulai) as jam_mulai, MAX(jam_selesai) as jam_selesai FROM jadwal_dokter WHERE id_dokter = ? GROUP BY hari ORDER BY FIELD(hari,'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu')");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $jadwal = [];
    while ($row = $result->fetch_assoc()) {
        $jadwal[] = "{$row['hari']}, " . substr($row['jam_mulai'], 0, 5) . " - " . substr($row['jam_selesai'], 0, 5);
    }
    return implode("<br>", $jadwal);
}

include '../includes/header.php';
?>

<section class="admin-page">
    <div class="admin-hero">
        <span class="eyebrow">Data Dokter</span>
        <h1>Manajemen Dokter</h1>
        <p>Tambah, ubah, dan rapikan jadwal praktik dokter agar reservasi pasien tetap tertata.</p>
    </div>

    <?php if (isset($_GET['success']) && $_GET['success'] === '1'): ?>
        <div class="admin-alert success">Data dokter berhasil disimpan.</div>
    <?php elseif (isset($_GET['success']) && $_GET['success'] === 'deleted'): ?>
        <div class="admin-alert danger">Data dokter berhasil dihapus.</div>
    <?php endif; ?>

    <section class="admin-panel">
        <div class="admin-panel-head">
            <div>
                <h2>Daftar Dokter</h2>
                <p class="section-text">Gunakan format jadwal: Senin: 09:00-17:30</p>
            </div>
            <button class="btn-admin" onclick="openDokterModal()"><i class="fas fa-plus"></i> Tambah Dokter</button>
        </div>

        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Jadwal Utama</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($d = $data->fetch_assoc()): ?>
                    <tr>
                        <td data-label="Nama"><?= htmlspecialchars($d['nama']) ?></td>
                        <td data-label="Jadwal"><?= getJadwalUtama($conn, $d['id_dokter']) ?: '-' ?></td>
                        <td data-label="Aksi">
                            <div class="admin-actions">
                                <button class="btn-muted" onclick='editDokter(<?= (int)$d["id_dokter"] ?>, <?= json_encode($d["nama"]) ?>)'>
                                    <i class="fas fa-pen"></i> Edit
                                </button>
                                <form method="POST" onsubmit="return confirm('Hapus data dokter ini?')">
                                    <?= csrf_input() ?>
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id_dokter" value="<?= (int) $d['id_dokter'] ?>">
                                    <button type="submit" class="btn-danger">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </section>
</section>

<div class="admin-modal" id="dokterModal">
    <div class="admin-modal-content">
        <div class="admin-modal-head">
            <h3 id="modalTitle">Tambah Dokter Baru</h3>
            <button class="admin-close" onclick="closeDokterModal()">&times;</button>
        </div>

        <form method="POST" class="admin-form">
            <?= csrf_input() ?>
            <input type="hidden" name="id_dokter" id="id_dokter">

            <label for="nama">Nama Dokter</label>
            <input type="text" name="nama" id="nama" required>

            <label for="jadwal">Jadwal</label>
            <textarea name="jadwal" id="jadwal" required placeholder="Contoh:
Senin: 09:00-17:30
Rabu: 13:00-16:00"></textarea>

            <button type="submit" class="btn-admin">Simpan</button>
        </form>
    </div>
</div>

<script>
function openDokterModal() {
    document.getElementById("dokterModal").classList.add("active");
    document.getElementById("modalTitle").innerText = "Tambah Dokter Baru";
    document.getElementById("id_dokter").value = "";
    document.getElementById("nama").value = "";
    document.getElementById("jadwal").value = "";
}

function closeDokterModal() {
    document.getElementById("dokterModal").classList.remove("active");
}

function editDokter(id, nama) {
    openDokterModal();
    document.getElementById("modalTitle").innerText = "Edit Dokter";
    document.getElementById("id_dokter").value = id;
    document.getElementById("nama").value = nama;

    fetch("get_jadwal_text.php?id_dokter=" + id)
        .then(res => res.text())
        .then(text => {
            document.getElementById("jadwal").value = text;
        });
}
</script>

<?php include '../includes/footer.php'; ?>
