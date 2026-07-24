<?php
function get_layanan_list() {
    return [
        ["image" => "layanan_1.webp", "image_x" => "0", "image_y" => "0", "title" => "Penambalan Gigi", "desc" => "Mengembalikan bentuk dan fungsi gigi yang berlubang atau patah."],
        ["image" => "layanan_2.webp", "image_x" => "0", "image_y" => "0", "title" => "Perawatan Saluran Akar", "desc" => "Mengobati infeksi pada akar dan jaringan pulpa gigi."],
        ["image" => "layanan_3.webp", "image_x" => "0", "image_y" => "0", "title" => "Bleaching Gigi", "desc" => "Membantu mencerahkan warna gigi sesuai kondisi pasien."],
        ["image" => "layanan_4.webp", "image_x" => "0", "image_y" => "0", "title" => "Pemasangan Gigi Palsu", "desc" => "Mengganti gigi yang hilang dengan gigi tiruan yang sesuai."],
        ["image" => "layanan_5.webp", "image_x" => "0", "image_y" => "0", "title" => "Sealant Celah Gigi", "desc" => "Melindungi permukaan kunyah gigi belakang dari risiko berlubang."],
        ["image" => "layanan_6.webp", "image_x" => "0", "image_y" => "0", "title" => "Scaling Gigi", "desc" => "Membersihkan karang gigi dan plak untuk menjaga kesehatan gusi."],
        ["image" => "layanan_7.webp", "image_x" => "-14%", "image_y" => "-6%", "title" => "Cabut Gigi", "desc" => "Tindakan pencabutan gigi bermasalah setelah pemeriksaan dan pertimbangan dokter."],
        ["image" => "layanan_8.webp", "image_x" => "16%", "image_y" => "-19%", "title" => "Behel Gigi", "desc" => "Perawatan ortodonti untuk membantu merapikan susunan gigi sesuai hasil konsultasi."],
        ["image" => "layanan_9.webp", "image_x" => "-14%", "image_y" => "2%", "title" => "Veneer Gigi", "desc" => "Perawatan estetik untuk memperbaiki tampilan gigi tertentu setelah evaluasi dokter."],
        ["image" => "layanan_10.webp", "image_x" => "16%", "image_y" => "1%", "title" => "Implan Gigi", "desc" => "Pilihan pengganti gigi hilang yang membutuhkan pemeriksaan kondisi tulang dan gusi."]
    ];
}

function render_layanan_cards($assetBase = 'assets/img') {
    foreach (get_layanan_list() as $layanan) {
        $title = htmlspecialchars($layanan['title'], ENT_QUOTES, 'UTF-8');
        $desc = htmlspecialchars($layanan['desc'], ENT_QUOTES, 'UTF-8');

        if (isset($layanan['image'])) {
            $image = htmlspecialchars($assetBase . '/' . $layanan['image'], ENT_QUOTES, 'UTF-8');
            $imageX = htmlspecialchars($layanan['image_x'] ?? '0', ENT_QUOTES, 'UTF-8');
            $imageY = htmlspecialchars($layanan['image_y'] ?? '0', ENT_QUOTES, 'UTF-8');
            $media = "<img src='{$image}' alt='{$title}' loading='lazy' decoding='async' style='--image-x: {$imageX}; --image-y: {$imageY};'>";
            $mediaClass = "layanan-icon layanan-icon-image";
        } else {
            $icon = htmlspecialchars($layanan['icon'], ENT_QUOTES, 'UTF-8');
            $media = "<i class='fas {$icon}'></i>";
            $mediaClass = "layanan-icon layanan-icon-symbol";
        }

        echo "<article class='layanan-card'>
            <div class='{$mediaClass}'>{$media}</div>
            <div>
                <h4>{$title}</h4>
                <p>{$desc}</p>
            </div>
        </article>";
    }
}
?>
