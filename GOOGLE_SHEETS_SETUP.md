# Google Sheets Integration Setup

Integrasi Google Sheets memungkinkan backup real-time data transaksi parkir ke Google Sheets sebagai backup dan untuk analisis data.

## Setup Google Sheets API

### 1. Buat Google Cloud Project
1. Kunjungi [Google Cloud Console](https://console.cloud.google.com/)
2. Buat project baru atau pilih project existing
3. Enable Google Sheets API:
   - Pergi ke "APIs & Services" > "Library"
   - Cari "Google Sheets API"
   - Klik "Enable"

### 2. Buat Service Account
1. Pergi ke "APIs & Services" > "Credentials"
2. Klik "Create Credentials" > "Service Account"
3. Isi detail service account
4. Klik "Create and Continue"
5. Di bagian "Keys", klik "Add Key" > "Create new key" > "JSON"
6. Download file JSON credentials

### 3. Buat Google Sheets
1. Kunjungi [Google Sheets](https://sheets.google.com/)
2. Buat spreadsheet baru
3. Copy Spreadsheet ID dari URL (bagian antara `/d/` dan `/edit`)

### 4. Konfigurasi Aplikasi
1. Copy file JSON credentials ke `storage/app/google-credentials.json`
2. Tambahkan ke file `.env`:
   ```
   GOOGLE_SHEETS_SPREADSHEET_ID=your_spreadsheet_id_here
   ```

### 5. Inisialisasi Google Sheets
Jalankan command berikut untuk setup awal:

```bash
# Setup sheet dengan headers
php artisan google-sheets:init

# Setup dan sync semua data existing
php artisan google-sheets:init --sync-existing
```

## Cara Kerja

### Real-time Sync
- Setiap kali transaksi selesai (kendaraan keluar), data akan otomatis disimpan ke Google Sheets
- Menggunakan Laravel Events dan Listeners untuk sync asynchronous

### Struktur Data
Data disimpan dengan kolom yang sama seperti export Excel:
- NO. PLAT
- JENIS
- AREA LOKASI
- WAKTU MASUK
- WAKTU KELUAR
- DURASI
- NILAI TARIF (IDR)
- TRANSACTION ID (kolom tersembunyi untuk tracking)

### Keamanan
- File credentials disimpan di `storage/app/` (tidak di-commit ke Git)
- Service account hanya memiliki akses ke spreadsheet tertentu
- Data disimpan dalam format yang sama dengan export untuk konsistensi

## Troubleshooting

### Error: "Invalid credentials"
- Pastikan file `google-credentials.json` ada di `storage/app/`
- Pastikan Service Account memiliki akses ke spreadsheet

### Error: "Spreadsheet not found"
- Periksa `GOOGLE_SHEETS_SPREADSHEET_ID` di `.env`
- Pastikan spreadsheet dapat diakses oleh Service Account

### Data tidak sync
- Periksa log Laravel untuk error
- Pastikan queue worker berjalan (jika menggunakan queue)
- Cek apakah event listener terdaftar dengan `php artisan event:list`

## Backup & Recovery

Google Sheets berfungsi sebagai backup sekunder. Jika database utama hilang:

1. Export data dari Google Sheets sebagai CSV
2. Import kembali ke database menggunakan fitur import yang ada
3. Atau gunakan data untuk analisis dan reporting

## Maintenance

### Cleanup Data Lama
Google Sheets akan terus bertambah. Untuk cleanup:

1. Buka Google Sheets
2. Filter dan hapus data lama secara manual
3. Atau buat script untuk archive data ke sheet terpisah

### Monitoring
Monitor log Laravel untuk error sync:
```bash
tail -f storage/logs/laravel.log | grep -i "google"
```