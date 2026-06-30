# 📦 SISTEM INVENTARIS BARANG

Sistem Manajemen Inventaris Barang berbasis **PHP Native** dan **MySQL** untuk membantu digitalisasi pengelolaan barang di sekolah, kantor, yayasan, atau UKM. Dilengkapi dengan multi-role akses, peminjaman, pengembalian, dashboard interaktif, dan export laporan.

---

## 📋 DAFTAR ISI

- [Preview](#preview)
- [Fitur](#fitur)
- [Teknologi](#teknologi)
- [Struktur Database](#struktur-database)
- [Struktur Folder](#struktur-folder)
- [Hak Akses](#hak-akses)
- [Instalasi](#instalasi)
- [Akun Default](#akun-default)
- [Alur Peminjaman](#alur-peminjaman)
- [Alur Pengembalian](#alur-pengembalian)
- [Screenshot](#screenshot)
- [Roadmap](#roadmap)
- [Troubleshooting](#troubleshooting)
- [Kontribusi](#kontribusi)
- [Lisensi](#lisensi)
- [Kontak](#kontak)

---

## PREVIEW

![Dashboard](assets/img/screenshot/dashboard.png)

---

## FITUR

### Dashboard
- Total Barang
- Total Barang Dipinjam
- Total Barang Rusak
- Total Peminjaman Aktif
- Grafik Barang per Kategori (Chart.js)
- Monitoring Stok Menipis
- Peminjaman Terlambat
- Aktivitas Terbaru

---

### Manajemen User (Multi-Role)

#### Super Admin
- Akses penuh seluruh sistem
- Manajemen user (CRUD)
- Pengaturan sistem

#### Admin
- Mengelola seluruh data master
- Mengelola peminjaman & pengembalian
- Mengakses laporan & riwayat

#### Staff
- Melihat data barang & peminjam
- Melakukan peminjaman & pengembalian
- Mengakses laporan

---

### Master Data

#### Data Barang
- CRUD Data Barang
- Upload Foto Barang
- Auto-generate Kode Barang
- Filter berdasarkan Kategori & Kondisi
- Search berdasarkan Nama/Kode
- Tracking Stok Minimal
- Status Barang (Tersedia/Dipinjam/Perbaikan/Hilang)

#### Kategori Barang
- CRUD Kategori
- Icon Kategori
- Statistik per Kategori

#### Data Peminjam
- CRUD Data Peminjam
- Tipe Peminjam (Internal/External/Student/Employee)
- Status Aktif/Non-Aktif

---

### Transaksi

#### Peminjaman
- Keranjang Sementara (Temp Loans)
- Pinjam Banyak Barang Sekaligus
- Pilih Peminjam dari Database
- Atur Tanggal Pinjam & Kembali
- Status Peminjaman (Pending/Dipinjam/Dikembalikan/Terlambat/Hilang)
- Riwayat Peminjaman

#### Pengembalian
- Proses Pengembalian Barang
- Cek Kondisi Barang (Baik/Rusak/Perbaikan)
- Update Stok Otomatis
- Update Status Peminjaman

---

### Laporan

#### Export PDF
- Laporan Data Barang
- Laporan Peminjaman

#### Export Excel
- Laporan Data Barang
- Laporan Peminjaman
- Laporan Statistik

---

### Riwayat & Audit Trail
- Riwayat Perubahan Barang
- Riwayat Peminjaman & Pengembalian
- Tracking User Activity

---

### Pengaturan Sistem
- Nama Aplikasi
- Versi Aplikasi
- Durasi Peminjaman Default
- Maksimal Item per Peminjaman
- Email Notifikasi
- Mode Pemeliharaan

---

## TEKNOLOGI

| Komponen | Teknologi |
|----------|-----------|
| Backend | PHP Native (OOP) |
| Database | MySQL / MariaDB |
| Frontend | Bootstrap 5 |
| Library JS | jQuery 3, Font Awesome 5, Chart.js 3 |
| Export PDF | DomPDF |
| Export Excel | PhpSpreadsheet |
| Server | Laragon / XAMPP |

---

## STRUKTUR DATABASE

### Master Data
- `users` - Manajemen user & autentikasi
- `categories` - Kategori barang
- `items` - Data master barang
- `borrowers` - Data peminjam

### Transaksi
- `loans` - Header peminjaman
- `loan_details` - Detail peminjaman
- `returns` - Header pengembalian
- `return_details` - Detail pengembalian
- `temp_loans` - Keranjang sementara

### Audit & Pengaturan
- `item_history` - Riwayat perubahan barang
- `settings` - Pengaturan sistem

### Relasi Database
```
users (1) ─── (n) items
users (1) ─── (n) loans
users (1) ─── (n) returns
users (1) ─── (n) item_history

categories (1) ─── (n) items

borrowers (1) ─── (n) loans

items (1) ─── (n) loan_details
items (1) ─── (n) return_details
items (1) ─── (n) temp_loans
items (1) ─── (n) item_history

loans (1) ─── (n) loan_details
loans (1) ─── (n) returns

loan_details (1) ─── (n) return_details
returns (1) ─── (n) return_details
```

---

## STRUKTUR FOLDER

```
inventaris-app/
│
├── assets/
│   ├── css/              # Custom CSS
│   ├── js/               # Custom JavaScript
│   └── img/              # Gambar & logo
│
├── auth/
│   ├── login.php         # Halaman login
│   ├── logout.php        # Proses logout
│   └── proses_login.php  # Proses autentikasi
│
├── config/
│   └── database.php      # Konfigurasi database
│
├── helpers/
│   ├── functions.php     # Fungsi global
│   ├── cart.php          # Fungsi keranjang peminjaman
│   └── validation.php    # Validasi input
│
├── modules/
│   ├── dashboard/        # Dashboard & grafik
│   ├── items/            # CRUD barang
│   ├── categories/       # CRUD kategori
│   ├── borrowers/        # CRUD peminjam
│   ├── loans/            # Peminjaman & keranjang
│   ├── returns/          # Pengembalian
│   ├── reports/          # Laporan PDF & Excel
│   ├── history/          # Riwayat barang
│   ├── users/            # Manajemen user (Super Admin)
│   └── settings/         # Pengaturan sistem (Super Admin)
│
├── views/
│   ├── header.php        # Header HTML
│   ├── sidebar.php       # Sidebar navigasi
│   ├── topbar.php        # Topbar
│   └── footer.php        # Footer & scripts
│
├── uploads/
│   └── items/            # Tempat upload foto barang
│
├── vendor/               # Library pihak ketiga
├── .htaccess
├── index.php             # Routing utama
├── database.sql          # Database schema
└── README.md             # Dokumentasi
```

---

## HAK AKSES

### Super Admin

| Modul | Akses |
|-------|-------|
| Dashboard | ✅ Full |
| Data Barang | ✅ CRUD |
| Kategori | ✅ CRUD |
| Data Peminjam | ✅ CRUD |
| Peminjaman | ✅ CRUD |
| Pengembalian | ✅ CRUD |
| Laporan | ✅ Full |
| Riwayat | ✅ Full |
| Manajemen User | ✅ Full |
| Pengaturan | ✅ Full |

### Admin

| Modul | Akses |
|-------|-------|
| Dashboard | ✅ Full |
| Data Barang | ✅ CRUD |
| Kategori | ✅ CRUD |
| Data Peminjam | ✅ CRUD |
| Peminjaman | ✅ CRUD |
| Pengembalian | ✅ CRUD |
| Laporan | ✅ Full |
| Riwayat | ✅ Full |
| Manajemen User | ❌ |
| Pengaturan | ❌ |

### Staff

| Modul | Akses |
|-------|-------|
| Dashboard | ✅ Full |
| Data Barang | 🔧 Read + Pinjam |
| Kategori | ❌ |
| Data Peminjam | 🔧 Read Only |
| Peminjaman | ✅ Pinjam/Kembali |
| Pengembalian | ✅ Kembali |
| Laporan | ✅ Full |
| Riwayat | ❌ |
| Manajemen User | ❌ |
| Pengaturan | ❌ |

**Keterangan:**
- ✅ = Akses penuh
- 🔧 = Akses terbatas
- ❌ = Tidak ada akses

---

## INSTALASI

### 1. Clone Repository

```bash
git clone https://github.com/username/inventaris-app.git
cd inventaris-app
```

### 2. Setup Database

Buka **phpMyAdmin** atau **MySQL Workbench**, lalu jalankan:

```sql
-- Buat database
CREATE DATABASE inventaris_db;

-- Import database
mysql -u root -p inventaris_db < database.sql
```

Atau import melalui phpMyAdmin:
1. Buka phpMyAdmin
2. Klik **Import**
3. Pilih file `database.sql`
4. Klik **Go**

### 3. Konfigurasi Database

Edit file `config/database.php`:

```php
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'inventaris_db');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### 4. Setup Folder Uploads

```bash
# Buat folder uploads
mkdir -p uploads/items

# Beri permission (Linux/Mac)
chmod 777 uploads/items

# Windows: klik kanan folder > Properties > Security > Allow Full Control
```

### 5. Setup Vendor/Library

#### DomPDF (Export PDF)
```bash
composer require dompdf/dompdf
```

#### PhpSpreadsheet (Export Excel)
```bash
composer require phpoffice/phpspreadsheet
```

#### Manual Library
Download dan letakkan di folder `vendor/`:
- [Bootstrap 5](https://getbootstrap.com/)
- [Font Awesome 5](https://fontawesome.com/)
- [jQuery 3](https://jquery.com/)
- [Chart.js 3](https://www.chartjs.org/)

### 6. Jalankan Aplikasi

Buka browser dan akses:

```
http://localhost/inventaris-app/
```

---

## AKUN DEFAULT

| Role | Username | Email | Password |
|------|----------|-------|----------|
| **Super Admin** | superadmin | superadmin@inventaris.com | **password123** |
| **Admin** | admin | admin@inventaris.com | **password123** |
| **Staff** | staff | staff@inventaris.com | **password123** |

---

## ALUR PEMINJAMAN

```
1. User pilih menu "Peminjaman"
   ↓
2. Cari barang yang akan dipinjam
   ↓
3. Klik "Pinjam" → barang masuk ke keranjang (temp_loans)
   ↓
4. Ulangi step 2-3 untuk menambah barang lain
   ↓
5. Lihat keranjang → bisa ubah jumlah atau hapus
   ↓
6. Klik "Proses Peminjaman"
   ↓
7. Pilih peminjam & tanggal kembali
   ↓
8. Konfirmasi → data pindah ke loans & loan_details
   ↓
9. Stok barang otomatis berkurang
   ↓
10. Keranjang kosong
```

---

## ALUR PENGEMBALIAN

```
1. User pilih menu "Pengembalian"
   ↓
2. Cari peminjaman aktif
   ↓
3. Klik "Kembalikan"
   ↓
4. Pilih barang yang dikembalikan
   ↓
5. Cek kondisi barang (Baik/Rusak/Perbaikan)
   ↓
6. Konfirmasi → data pindah ke returns & return_details
   ↓
7. Stok barang otomatis bertambah
   ↓
8. Status peminjaman berubah jadi "Dikembalikan"
```

---

## SCREENSHOT

### Dashboard
![Dashboard](assets/img/screenshot/dashboard.png)

### Data Barang
![Data Barang](assets/img/screenshot/items.png)

### Peminjaman
![Peminjaman](assets/img/screenshot/loans.png)

### Keranjang Peminjaman
![Keranjang](assets/img/screenshot/cart.png)

### Laporan Export
![Laporan](assets/img/screenshot/reports.png)

---

## ROADMAP

### Selesai ✅

- [x] Database Design
- [x] Multi-Role Authentication
- [x] Dashboard dengan Grafik
- [x] CRUD Barang
- [x] CRUD Kategori
- [x] CRUD Peminjam
- [x] Peminjaman dengan Keranjang
- [x] Pengembalian
- [x] Export PDF (DomPDF)
- [x] Export Excel (PhpSpreadsheet)
- [x] Riwayat Barang (Audit Trail)
- [x] Pengaturan Sistem

### Pengembangan Selanjutnya 🚀

- [ ] Notifikasi Email untuk Peminjaman Terlambat
- [ ] QR Code untuk Setiap Barang
- [ ] Mobile Responsive Improvement
- [ ] REST API untuk Integrasi
- [ ] Backup Database Otomatis
- [ ] Multi-Language Support
- [ ] Import Data dari Excel/CSV
- [ ] Dashboard dengan Filter Periode
- [ ] Report Grafik Peminjaman per Bulan

---

## TROUBLESHOOTING

### 1. Error 404 - Halaman Tidak Ditemukan

**Solusi:**
- Pastikan file module ada di `modules/{module}/{action}.php`
- Cek routing di `index.php`
- Pastikan URL benar: `index.php?url=module/action`

### 2. Error 403 - Akses Ditolak

**Solusi:**
- Periksa role user di session
- Cek hak akses di `index.php`
- Login dengan akun yang memiliki hak akses

### 3. Upload Foto Gagal

**Solusi:**
- Pastikan folder `uploads/items/` memiliki permission 777
- Cek ukuran file (maks 2MB)
- Format yang didukung: JPG, PNG, GIF, WebP
- Cek error di `php.ini`: `upload_max_filesize` dan `post_max_size`

### 4. Export PDF Gagal

**Solusi:**
- Pastikan DomPDF terinstall
- Cek memory_limit di php.ini (min 128M)
- Cek error log di `vendor/dompdf/dompdf/log`

### 5. Export Excel Gagal

**Solusi:**
- Pastikan PhpSpreadsheet terinstall
- Cek extension `zip` di PHP aktif
- Cek memory_limit (min 256M untuk data besar)

### 6. Login Tidak Berhasil

**Solusi:**
- Cek username/email dan password
- Password default: **password123**
- Cek session di `auth/proses_login.php`
- Pastikan database terhubung

---

## KONTRIBUSI

1. Fork repository
2. Buat branch baru (`git checkout -b fitur-baru`)
3. Commit perubahan (`git commit -m 'Menambahkan fitur baru'`)
4. Push ke branch (`git push origin fitur-baru`)
5. Buat Pull Request

### Panduan Kontribusi

- Ikuti standar coding PSR-12 untuk PHP
- Gunakan komentar yang jelas
- Perbarui dokumentasi jika diperlukan
- Test sebelum commit

---

## LISENSI

Proyek ini dikembangkan sebagai bagian dari penelitian dan pengembangan **Sistem Inventaris Barang Berbasis Web** serta digunakan untuk kebutuhan pembelajaran, penelitian, dan pengembangan lebih lanjut.

---

## KONTAK

**Nama Pengembang**
- Email: email@domain.com
- GitHub: [github.com/username](https://github.com/username)
- Website: [domain.com](https://domain.com)

---

## TERIMA KASIH

- [Bootstrap Team](https://getbootstrap.com/)
- [Chart.js](https://www.chartjs.org/)
- [DomPDF](https://github.com/dompdf/dompdf)
- [PhpSpreadsheet](https://github.com/PHPOffice/PhpSpreadsheet)
- [Font Awesome](https://fontawesome.com/)
- [jQuery](https://jquery.com/)

---

**Dibuat dengan ❤️ untuk memudahkan manajemen inventaris barang.**

---

> **Catatan:** Untuk dokumentasi lengkap, silakan lihat file `docs/` atau kunjungi [Wiki](https://github.com/username/inventaris-app/wiki).