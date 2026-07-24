<?php
session_start();
date_default_timezone_set('Asia/Jakarta');
require_once '../includes/db.php';
require_once '../fpdf/fpdf.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'pasien') {
    die("Akses tidak sah.");
}

$pasien_id = (int) $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'Pasien';

function pdf_text($text)
{
    $text = (string) $text;
    if (function_exists('iconv')) {
        $converted = @iconv('UTF-8', 'windows-1252//TRANSLIT', $text);
        if ($converted !== false) {
            return $converted;
        }
    }
    return $text;
}

function format_date_id($date)
{
    $months = [
        '01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr',
        '05' => 'Mei', '06' => 'Jun', '07' => 'Jul', '08' => 'Agu',
        '09' => 'Sep', '10' => 'Okt', '11' => 'Nov', '12' => 'Des'
    ];

    $time = strtotime($date);
    if (!$time) {
        return $date;
    }

    return date('d', $time) . ' ' . $months[date('m', $time)] . ' ' . date('Y', $time);
}

function status_label($status)
{
    $labels = [
        'pending' => 'Menunggu',
        'confirmed' => 'Terkonfirmasi',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan'
    ];

    return $labels[$status] ?? ucfirst($status);
}

class ReservationHistoryPdf extends FPDF
{
    public $patientName = '';
    public $generatedAt = '';

    public function Header()
    {
        $this->SetFillColor(10, 79, 74);
        $this->Rect(0, 0, 210, 38, 'F');
        $this->SetFillColor(224, 111, 95);
        $this->Rect(0, 35, 210, 3, 'F');

        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 17);
        $this->SetXY(14, 10);
        $this->Cell(0, 7, pdf_text('Ratna Dental Care'), 0, 1);

        $this->SetFont('Arial', '', 9);
        $this->SetX(14);
        $this->Cell(0, 5, pdf_text('Riwayat reservasi pasien - dibuat otomatis dari sistem klinik'), 0, 1);

        $this->SetFont('Arial', 'B', 10);
        $this->SetXY(140, 11);
        $this->Cell(56, 6, pdf_text('LAPORAN RESERVASI'), 1, 1, 'C');
        $this->SetFont('Arial', '', 8);
        $this->SetX(140);
        $this->Cell(56, 5, pdf_text($this->generatedAt), 0, 1, 'C');
        $this->Ln(18);
    }

    public function Footer()
    {
        $this->SetY(-16);
        $this->SetDrawColor(217, 229, 226);
        $this->Line(14, $this->GetY(), 196, $this->GetY());
        $this->SetY(-12);
        $this->SetTextColor(98, 114, 110);
        $this->SetFont('Arial', '', 8);
        $this->Cell(91, 5, pdf_text('Ratna Dental Care'), 0, 0, 'L');
        $this->Cell(91, 5, pdf_text('Halaman ' . $this->PageNo() . '/{nb}'), 0, 0, 'R');
    }

    public function card($x, $y, $w, $title, $value, $accent)
    {
        $this->SetFillColor(255, 255, 255);
        $this->SetDrawColor(217, 229, 226);
        $this->Rect($x, $y, $w, 24, 'DF');
        $this->SetFillColor($accent[0], $accent[1], $accent[2]);
        $this->Rect($x, $y, 3, 24, 'F');

        $this->SetXY($x + 8, $y + 5);
        $this->SetTextColor(98, 114, 110);
        $this->SetFont('Arial', '', 8);
        $this->Cell($w - 10, 5, pdf_text($title), 0, 1);

        $this->SetX($x + 8);
        $this->SetTextColor(19, 32, 30);
        $this->SetFont('Arial', 'B', 14);
        $this->Cell($w - 10, 8, pdf_text($value), 0, 1);
    }

    public function fitText($text, $width)
    {
        $text = pdf_text($text);
        if ($this->GetStringWidth($text) <= $width) {
            return $text;
        }

        while (strlen($text) > 3 && $this->GetStringWidth($text . '...') > $width) {
            $text = substr($text, 0, -1);
        }

        return $text . '...';
    }
}

$user_stmt = $conn->prepare("SELECT username FROM users WHERE id_user = ?");
$user_stmt->bind_param("i", $pasien_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result()->fetch_assoc();
if ($user_result && !empty($user_result['username'])) {
    $username = $user_result['username'];
}

$query = "SELECT r.id_reservasi, d.nama AS dokter, r.tanggal, r.jam, r.status, r.antrian
          FROM reservations r
          JOIN doctors d ON r.id_dokter = d.id_dokter
          WHERE r.id_pasien = ?
          ORDER BY r.tanggal DESC, r.jam DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $pasien_id);
$stmt->execute();
$result = $stmt->get_result();

$reservations = [];
$counts = [
    'total' => 0,
    'confirmed' => 0,
    'pending' => 0,
    'completed' => 0,
    'cancelled' => 0
];

while ($row = $result->fetch_assoc()) {
    $reservations[] = $row;
    $counts['total']++;
    if (isset($counts[$row['status']])) {
        $counts[$row['status']]++;
    }
}

$pdf = new ReservationHistoryPdf();
$generatedAt = (new DateTimeImmutable('now', new DateTimeZone('Asia/Jakarta')))->format('d/m/Y H:i');
$pdf->AliasNbPages();
$pdf->patientName = $username;
$pdf->generatedAt = $generatedAt;
$pdf->SetTitle(pdf_text('Riwayat Reservasi - Ratna Dental Care'));
$pdf->SetAuthor(pdf_text('Ratna Dental Care'));
$pdf->SetMargins(14, 48, 14);
$pdf->SetAutoPageBreak(true, 22);
$pdf->AddPage();

$pdf->SetTextColor(19, 32, 30);
$pdf->SetFont('Arial', 'B', 18);
$pdf->Cell(0, 8, pdf_text('Riwayat Reservasi'), 0, 1);

$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(98, 114, 110);
$pdf->Cell(0, 6, pdf_text('Pasien: ' . $username . '   |   Dicetak: ' . $generatedAt), 0, 1);
$pdf->Ln(7);

$cardY = $pdf->GetY();
$pdf->card(14, $cardY, 42, 'Total', (string) $counts['total'], [15, 118, 110]);
$pdf->card(61, $cardY, 42, 'Terkonfirmasi', (string) $counts['confirmed'], [47, 125, 91]);
$pdf->card(108, $cardY, 42, 'Menunggu', (string) $counts['pending'], [163, 109, 20]);
$pdf->card(155, $cardY, 41, 'Selesai', (string) $counts['completed'], [224, 111, 95]);
$pdf->SetY($cardY + 32);

$pdf->SetFillColor(238, 247, 245);
$pdf->SetDrawColor(217, 229, 226);
$pdf->Rect(14, $pdf->GetY(), 182, 18, 'DF');
$pdf->SetXY(20, $pdf->GetY() + 4);
$pdf->SetTextColor(11, 79, 74);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 5, pdf_text('Detail reservasi pasien'), 0, 1);
$pdf->SetX(20);
$pdf->SetTextColor(98, 114, 110);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(0, 5, pdf_text('Gunakan dokumen ini sebagai arsip pribadi. Status dapat berubah mengikuti konfirmasi admin.'), 0, 1);
$pdf->Ln(10);

$headers = [
    ['No', 12],
    ['ID', 18],
    ['Dokter', 55],
    ['Tanggal', 31],
    ['Jam', 20],
    ['Status', 32],
    ['Ant.', 14]
];

$drawHeader = function () use ($pdf, $headers) {
    $pdf->SetFillColor(10, 79, 74);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetDrawColor(10, 79, 74);
    $pdf->SetFont('Arial', 'B', 8);

    foreach ($headers as $header) {
        $pdf->Cell($header[1], 9, pdf_text($header[0]), 1, 0, 'C', true);
    }
    $pdf->Ln();
};

$drawHeader();

if (empty($reservations)) {
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetTextColor(98, 114, 110);
    $pdf->SetFillColor(255, 255, 255);
    $pdf->SetDrawColor(217, 229, 226);
    $pdf->Cell(182, 18, pdf_text('Belum ada riwayat reservasi.'), 1, 1, 'C', true);
} else {
    $statusColors = [
        'pending' => [255, 246, 226, 163, 109, 20],
        'confirmed' => [232, 246, 239, 47, 125, 91],
        'completed' => [239, 247, 245, 15, 118, 110],
        'cancelled' => [252, 235, 235, 181, 67, 67]
    ];

    $no = 1;
    foreach ($reservations as $row) {
        if ($pdf->GetY() > 255) {
            $pdf->AddPage();
            $drawHeader();
        }

        $fill = ($no % 2 === 0) ? [247, 250, 249] : [255, 255, 255];
        $pdf->SetFillColor($fill[0], $fill[1], $fill[2]);
        $pdf->SetDrawColor(217, 229, 226);
        $pdf->SetTextColor(19, 32, 30);
        $pdf->SetFont('Arial', '', 8);

        $pdf->Cell(12, 10, (string) $no, 1, 0, 'C', true);
        $pdf->Cell(18, 10, '#' . $row['id_reservasi'], 1, 0, 'C', true);
        $pdf->Cell(55, 10, $pdf->fitText($row['dokter'], 49), 1, 0, 'L', true);
        $pdf->Cell(31, 10, pdf_text(format_date_id($row['tanggal'])), 1, 0, 'C', true);
        $pdf->Cell(20, 10, substr($row['jam'], 0, 5), 1, 0, 'C', true);

        $status = $row['status'];
        $color = $statusColors[$status] ?? [238, 247, 245, 98, 114, 110];
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        $pdf->Cell(32, 10, '', 1, 0, 'C', true);
        $pdf->SetFillColor($color[0], $color[1], $color[2]);
        $pdf->Rect($x + 3, $y + 2.4, 26, 5.4, 'F');
        $pdf->SetXY($x + 3, $y + 2.7);
        $pdf->SetTextColor($color[3], $color[4], $color[5]);
        $pdf->SetFont('Arial', 'B', 6.8);
        $pdf->Cell(26, 4.8, pdf_text(status_label($status)), 0, 0, 'C');
        $pdf->SetXY($x + 32, $y);

        $pdf->SetTextColor(19, 32, 30);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(14, 10, $row['antrian'] ? (string) $row['antrian'] : '-', 1, 1, 'C', true);
        $no++;
    }
}

$noteSpace = 8;
$noteHeight = 15;
if ($pdf->GetY() + $noteSpace + $noteHeight > 270) {
    $pdf->AddPage();
}

$pdf->Ln($noteSpace);
$pdf->SetFillColor(255, 240, 235);
$pdf->SetDrawColor(246, 206, 196);
$pdf->Rect(14, $pdf->GetY(), 182, 15, 'DF');
$pdf->SetXY(20, $pdf->GetY() + 4);
$pdf->SetTextColor(98, 114, 110);
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(0, 5, pdf_text('Catatan: Mohon datang sesuai jadwal dan lakukan konfirmasi ulang jika ada perubahan rencana kunjungan.'), 0, 1);

$pdf->Output('I', 'riwayat_reservasi_ratna_dental_care.pdf');
