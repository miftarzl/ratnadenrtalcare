<?php
require_once '../includes/functions.php';
redirect_if_not_logged_in();
redirect_if_not_pasien();
require_once '../includes/db.php';

$message = "";
$error = "";
$dokter_dari_url = isset($_GET['id_dokter']) ? intval($_GET['id_dokter']) : null;

function hari_indonesia($tanggal) {
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

    return $hariMap[$hariEn] ?? '';
}

function jadwal_masih_bisa_dipesan($conn, $dokter_id, $tanggal, $jam) {
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal) || !preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $jam)) {
        return false;
    }

    $timezone = new DateTimeZone('Asia/Jakarta');
    $sekarang = new DateTimeImmutable('now', $timezone);
    $tanggalDipilih = DateTimeImmutable::createFromFormat('!Y-m-d', $tanggal, $timezone);

    if (!$tanggalDipilih || $tanggalDipilih->format('Y-m-d') !== $tanggal || $tanggalDipilih < $sekarang->setTime(0, 0)) {
        return false;
    }

    $jamLengkap = strlen($jam) === 5 ? $jam . ':00' : $jam;
    $waktuMulai = DateTimeImmutable::createFromFormat('!Y-m-d H:i:s', $tanggal . ' ' . $jamLengkap, $timezone);

    if (!$waktuMulai || $waktuMulai <= $sekarang) {
        return false;
    }

    $hari = hari_indonesia($tanggal);
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM jadwal_dokter WHERE id_dokter = ? AND hari = ? AND jam_mulai = ?");
    $stmt->bind_param("iss", $dokter_id, $hari, $jamLengkap);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();

    return (int) $data['total'] > 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_token();
    $dokter_id = intval($_POST['id_dokter'] ?? 0);
    $tanggal = $_POST['tanggal'] ?? '';
    $jam = $_POST['jam'] ?? '';

    if ($dokter_id && $tanggal && $jam) {
        if (!jadwal_masih_bisa_dipesan($conn, $dokter_id, $tanggal, $jam)) {
            $error = "Jadwal yang dipilih sudah lewat atau tidak tersedia. Silakan pilih waktu lain.";
        } else {
            $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM reservations WHERE id_dokter = ? AND tanggal = ? AND jam = ?");
            $stmt->bind_param("iss", $dokter_id, $tanggal, $jam);
            $stmt->execute();
            $data = $stmt->get_result()->fetch_assoc();

            if ($data['total'] == 0) {
                $cekAntrian = $conn->prepare("SELECT COUNT(*) AS total FROM reservations WHERE id_dokter = ? AND tanggal = ?");
                $cekAntrian->bind_param("is", $dokter_id, $tanggal);
                $cekAntrian->execute();
                $antrianData = $cekAntrian->get_result()->fetch_assoc();
                $no_antrian = $antrianData['total'] + 1;

                $stmt = $conn->prepare("INSERT INTO reservations (id_pasien, id_dokter, tanggal, jam, status, antrian) VALUES (?, ?, ?, ?, 'pending', ?)");
                $stmt->bind_param("iissi", $_SESSION['user_id'], $dokter_id, $tanggal, $jam, $no_antrian);
                if ($stmt->execute()) {
                    $_SESSION['show_notif'] = true;
                    $message = "Reservasi berhasil dibuat. No Antrian Anda: <strong>$no_antrian</strong>";
                } else {
                    $error = "Gagal membuat reservasi.";
                }
            } else {
                $error = "Jadwal ini sudah dipesan. Silakan pilih jam lain.";
            }
        }
    } else {
        $error = "Harap isi semua kolom.";
    }
}

$doctors = $conn->query("SELECT * FROM doctors ORDER BY nama ASC");
include '../includes/header.php';
?>

<link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<main class="page-main">
    <div class="page-shell">
        <section class="page-hero">
            <div>
                <span class="eyebrow">Reservasi</span>
                <h1>Buat Reservasi Baru</h1>
                <p>Pilih dokter, tanggal, dan waktu kunjungan sesuai jadwal yang tersedia.</p>
            </div>
        </section>

        <section class="form-panel">
            <h2><i class="fas fa-calendar-plus"></i> Detail Reservasi</h2>

    <?php if ($message): ?>
    <script>
    Swal.fire({
        icon: 'success',
        title: 'Reservasi Berhasil',
        html: '<?= $message ?>',
        timer: 4000,
        showConfirmButton: false
    }).then(() => {
        window.location.href = 'riwayat_reservasi.php';
    });
    </script>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

            <form method="POST" action="reservation.php">
                <?= csrf_input() ?>
                <label for="id_dokter">Pilih Dokter</label>
                <select name="id_dokter" id="id_dokter" required>
                    <option value="">-- Pilih Dokter --</option>
                    <?php while($doc = $doctors->fetch_assoc()): ?>
                        <?php $selected = ($dokter_dari_url && $dokter_dari_url == $doc['id_dokter']) ? 'selected' : ''; ?>
                        <option value="<?= $doc['id_dokter'] ?>" <?= $selected ?>><?= htmlspecialchars($doc['nama']) ?></option>
                    <?php endwhile; ?>
                </select>

                <label for="tanggal">Pilih Tanggal</label>
                <input type="text" name="tanggal" id="tanggal" placeholder="Klik untuk memilih tanggal" required readonly>
                <div id="jadwalInfoText"></div>

                <label for="jam">Pilih Waktu</label>
                <select name="jam" id="jam" required>
                    <option value="">-- Pilih Waktu --</option>
                </select>

                <button type="submit">Buat Reservasi</button>
            </form>
        </section>
    </div>
</main>

<script>
let allowedDays = [];
let flatpickrInstance = null;

function setupFlatpickr() {
    if (flatpickrInstance) flatpickrInstance.destroy();

    flatpickrInstance = flatpickr("#tanggal", {
        dateFormat: "Y-m-d",
        minDate: "today",
        disableMobile: true,
        enable: [function(date) {
            return allowedDays.includes(date.getDay());
        }]
    });
}

function loadJadwalInfo(dokterId) {
    $.get("jadwal_info.php", { id_dokter: dokterId }, function (data) {
        const res = typeof data === "string" ? JSON.parse(data) : data;
        $("#jadwalInfoText").text(res.text || "");
        allowedDays = Array.isArray(res.days) ? res.days.map(Number) : [];
        setupFlatpickr();
    }).fail(function () {
        $("#jadwalInfoText").text("Jadwal dokter belum bisa dimuat.");
        allowedDays = [];
        setupFlatpickr();
    });
}

function loadJam() {
    let dokterId = $("#id_dokter").val();
    let tanggal = $("#tanggal").val();
    if (dokterId && tanggal) {
        $.post("jadwal.php", { id_dokter: dokterId, tanggal: tanggal }, function(data) {
            $("#jam").html(data);
        });
    } else {
        $("#jam").html("<option value=''>-- Pilih Waktu --</option>");
    }
}

$("#id_dokter").on("change", function () {
    const dokterId = $(this).val();
    if (dokterId) {
        loadJadwalInfo(dokterId);
    } else {
        $("#jadwalInfoText").text("");
    }
    loadJam();
});
$("#tanggal").on("change", loadJam);

<?php if ($dokter_dari_url): ?>
setTimeout(() => {
    $("#id_dokter").trigger("change");
}, 300);
<?php endif; ?>
</script>

<?php include '../includes/footer.php'; ?>
