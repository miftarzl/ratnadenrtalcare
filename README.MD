# Aplikasi Reservasi Klinik Dokter Gigi

Aplikasi web reservasi klinik gigi dengan PHP dan MySQL/MariaDB. Aplikasi memiliki dua jenis pengguna, yaitu admin dan pasien, serta chatbot informasi klinik berbasis IndoBERT.

## Fitur

- Halaman informasi Klinik Ratna Dental Care.
- Registrasi dan login pasien.
- Login admin.
- Admin dapat mengelola dokter dan jadwal praktik.
- Pasien dapat membuat reservasi berdasarkan dokter, tanggal, dan jadwal yang tersedia.
- Pasien dapat melihat status dan riwayat reservasi.
- Admin dapat mengonfirmasi atau membatalkan reservasi.
- Export riwayat reservasi pasien ke PDF.
- Chatbot untuk informasi umum dan konsultasi awal seputar klinik gigi.
- Footer dengan kontak WhatsApp dan alamat Google Maps.

## Struktur Direktori

```text
.
├── admin/                         # Dashboard dan fitur admin
├── assets/                        # CSS, JavaScript, gambar, dan aset lainnya
├── db/
│   └── klinik_dokter_gigi.sql     # Struktur dan data awal database
├── flask_api/                     # API chatbot Flask
│   ├── app.py                     # Endpoint API chatbot
│   ├── intents.json               # Intent dan respons chatbot
│   ├── label_encoder.pkl          # Label encoder hasil training
│   ├── model_chatbot_indobert/    # Model IndoBERT asli/backup
│   ├── model_chatbot_onnx/        # Model runtime ONNX INT8
│   ├── requirements.txt           # Dependency Flask dan ONNX Runtime
├── includes/                      # Koneksi database, header, footer, dan helper
├── pasien/                        # Halaman dan proses pasien
├── fpdf/                          # Library untuk export PDF
├── index.php                      # Halaman utama aplikasi
├── login.php                      # Halaman login
├── register.php                   # Halaman registrasi
└── reset_password.php             # Halaman reset password
```

## Kebutuhan Sistem Lokal

- XAMPP atau web server yang mendukung PHP.
- PHP 8.2 atau versi yang kompatibel.
- MySQL/MariaDB.
- Python 3.11 untuk menjalankan chatbot.
- Browser modern.

## Instalasi Database

1. Jalankan Apache dan MySQL/MariaDB.
2. Buka phpMyAdmin.
3. Import file `db/klinik_dokter_gigi.sql`.
4. Pastikan nama database adalah `klinik_dokter_gigi`.
5. Sesuaikan konfigurasi database pada `includes/db.php`:

```php
$host = "127.0.0.1";
$user = "root";
$password = "";
$dbname = "klinik_dokter_gigi";
$port = 3307;
```

Port `3307` mengikuti konfigurasi XAMPP lokal saat ini. Pada VPS, port MySQL/MariaDB biasanya `3306`, dan sebaiknya menggunakan user database khusus dengan password.

## Menjalankan Website Lokal

Setelah Apache dan database aktif, buka:

```text
http://localhost/klinikdoktergigi/
```

## Menjalankan Chatbot Lokal

Buka terminal dari root proyek, lalu jalankan:

```powershell
cd flask_api
pip install -r requirements.txt
python app.py
```

API chatbot berjalan di:

```text
http://127.0.0.1:5000/
```

Endpoint chatbot:

```text
POST http://127.0.0.1:5000/chatbot
```

Contoh request:

```json
{
  "message": "halo kak mau scaling gigi"
}
```

Frontend menggunakan endpoint tersebut melalui `assets/js/chatbot.js`.

## Model Chatbot

Model chatbot tetap menggunakan IndoBERT yang telah di-fine-tuning untuk klasifikasi intent.

- Training/model asli: `flask_api/model_chatbot_indobert/`
- Model runtime aktif: `flask_api/model_chatbot_onnx/model_int8.onnx`
- Runtime inferensi: ONNX Runtime CPU
- Label intent: `label_encoder.pkl`
- Data intent dan respons: `intents.json`

Model IndoBERT dikonversi ke format ONNX dan dioptimasi dengan INT8 quantization untuk mengurangi ukuran model dan mempercepat inferensi CPU. Konversi ini tidak mengubah model menjadi model lain dan tidak membutuhkan retraining.

Jika hanya mengubah teks `responses` pada `intents.json`, chatbot tidak perlu dilatih ulang. Cukup restart Flask agar perubahan terbaca.

## Hosting VPS

Untuk hosting, model asli PyTorch tidak wajib di-upload. Runtime cukup menggunakan model ONNX INT8 dan file tokenizer di dalam `model_chatbot_onnx/`.

Dari folder `flask_api` pada VPS:

```bash
pip install -r requirements.txt
gunicorn app:app --bind 127.0.0.1:5000
```

Gunakan Nginx atau Apache sebagai reverse proxy dari subdomain API, misalnya `https://api.domainanda.com`, ke `127.0.0.1:5000`.

Setelah API online, ubah URL endpoint pada `assets/js/chatbot.js` dari:

```javascript
http://127.0.0.1:5000/chatbot
```

menjadi URL API VPS, misalnya:

```javascript
https://api.domainanda.com/chatbot
```

Website PHP dan API chatbot dapat berada pada domain/subdomain yang berbeda, selama endpoint API menggunakan HTTPS dan konfigurasi CORS mengizinkan domain website.

## Catatan Keamanan

- Jangan menggunakan user database `root` tanpa password pada production.
- Ganti password akun admin dan akun contoh setelah database di-import.
- Jangan menjalankan Flask dengan `debug=True` pada VPS.
- Simpan file model dan database hanya di server yang memiliki akses terbatas.
