# Posyandu System

Sistem Informasi Posyandu berbasis **PHP Native** dan **MariaDB** untuk membantu digitalisasi pelayanan Posyandu, mulai dari pengelolaan data keluarga, balita, ibu hamil, kegiatan Posyandu, pemeriksaan kesehatan, imunisasi, hingga pelaporan.

---

## Preview

![Dashboard](img/screenshot/dash.png)

---

# Fitur

## Dashboard

* Statistik Keluarga
* Statistik Anak
* Statistik Ibu Hamil
* Statistik Kegiatan Posyandu
* Monitoring Pemeriksaan
* Monitoring Kehadiran
* Dashboard Ringkasan Data

---

## Master Data

### Data Keluarga

* CRUD Data Keluarga
* Detail Keluarga

### Data Anak

* CRUD Data Anak
* Detail Anak
* Grafik Pertumbuhan
* Upload Foto Anak

### Data Ibu Hamil

* CRUD Data Ibu Hamil
* Detail Ibu Hamil
* Grafik Perkembangan Kehamilan

### Master Imunisasi

* Kelola Jenis Imunisasi Anak
* Kelola Jenis Imunisasi Ibu Hamil

---

## Kegiatan Posyandu

* Jadwal Posyandu
* Kehadiran Anak
* Riwayat Kegiatan

---

## Pemeriksaan

### Pemeriksaan Anak

* Berat Badan
* Tinggi Badan
* Lingkar Kepala
* Status Gizi
* Catatan Pemeriksaan

### Pemeriksaan Ibu Hamil

* Usia Kehamilan
* Berat Badan
* Tekanan Darah
* Lingkar Lengan Atas (LILA)
* Tinggi Fundus Uteri
* Keluhan
* Tindakan
* Catatan Pemeriksaan

---

## Imunisasi

### Anak

* Pencatatan Imunisasi
* Riwayat Imunisasi

### Ibu Hamil

* Pencatatan Imunisasi TT
* Riwayat Imunisasi

---

## Manajemen Pengguna

* Login
* Role Admin
* Role Kader
* Role Bidan

---

# Teknologi

* PHP Native
* MariaDB / MySQL
* Bootstrap
* jQuery
* Font Awesome
* Chart.js
* Laragon

---

# Struktur Database

## Master

* users
* keluarga
* anak
* ibu_hamil
* master_imunisasi

## Transaksi

* kegiatan
* kehadiran
* pemeriksaan
* pemeriksaan_ibu_hamil
* imunisasi
* imunisasi_ibu_hamil

---

# Struktur Folder

```text
posyandu/
│
├── assets/
├── auth/
├── config/
├── modules/
│   ├── dashboard/
│   ├── keluarga/
│   ├── anak/
│   ├── ibu_hamil/
│   ├── kegiatan/
│   ├── kehadiran/
│   ├── pemeriksaan/
│   ├── pemeriksaan_ibu_hamil/
│   ├── imunisasi/
│   ├── imunisasi_ibu_hamil/
│   ├── master_imunisasi/
│   └── users/
│
├── views/
└── index.php
```

---

# Hak Akses

## Admin

* Mengelola seluruh data
* Mengelola pengguna
* Mengakses seluruh menu

## Kader

* Mengelola data keluarga
* Mengelola anak
* Mengelola ibu hamil
* Mengelola kegiatan
* Mengelola kehadiran
* Menginput pemeriksaan
* Menginput imunisasi

## Bidan

* Melakukan pemeriksaan
* Mengelola imunisasi
* Memantau perkembangan kesehatan

---

# Instalasi

1. Clone repository

```bash
git clone https://github.com/username/posyandu.git
```

2. Import database

```
database/posyandu_db.sql
```

3. Atur konfigurasi database

```
config/database.php
```

4. Jalankan menggunakan Laragon, XAMPP, atau server PHP lainnya.

---

# Screenshot

## Dashboard

![Dashboard](img/screenshot/dash.png)

## Data Anak

*(Tambahkan screenshot)*

## Data Ibu Hamil

*(Tambahkan screenshot)*

## Pemeriksaan

*(Tambahkan screenshot)*

## Imunisasi

*(Tambahkan screenshot)*

---

# Roadmap

### Selesai

* Dashboard
* Data Keluarga
* Data Anak
* Data Ibu Hamil
* Kegiatan Posyandu
* Kehadiran
* Pemeriksaan Anak
* Pemeriksaan Ibu Hamil
* Imunisasi Anak
* Imunisasi Ibu Hamil
* Master Imunisasi

### Pengembangan Selanjutnya

* Grafik Status Gizi
* Grafik Kehamilan
* Cetak KMS
* Export PDF
* Export Excel
* Notifikasi Jadwal Posyandu
* REST API

---

# Lisensi

Project ini dikembangkan sebagai bagian dari penelitian dan pengembangan **Sistem Informasi Posyandu Berbasis Web** menggunakan metode **Scrum** serta digunakan untuk kebutuhan pembelajaran, penelitian, dan pengembangan lebih lanjut.
