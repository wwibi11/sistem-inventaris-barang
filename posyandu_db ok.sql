-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 26, 2026 at 10:04 AM
-- Server version: 11.2.2-MariaDB-log
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `posyandu_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `anak`
--

CREATE TABLE `anak` (
  `id` int(11) NOT NULL,
  `id_keluarga` int(11) NOT NULL,
  `nik` varchar(30) DEFAULT NULL,
  `nama` varchar(100) NOT NULL,
  `tempat_lahir` varchar(100) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `jenis_kelamin` enum('L','P') DEFAULT NULL,
  `anak_ke` int(11) DEFAULT NULL,
  `berat_lahir` decimal(5,2) DEFAULT NULL,
  `panjang_lahir` decimal(5,2) DEFAULT NULL,
  `nama_ayah` varchar(100) DEFAULT NULL,
  `nama_ibu` varchar(100) DEFAULT NULL,
  `status` enum('aktif','pindah','meninggal') DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `anak`
--

INSERT INTO `anak` (`id`, `id_keluarga`, `nik`, `nama`, `tempat_lahir`, `tanggal_lahir`, `jenis_kelamin`, `anak_ke`, `berat_lahir`, `panjang_lahir`, `nama_ayah`, `nama_ibu`, `status`, `created_at`, `foto`) VALUES
(2, 3, '11111111111', 'Yusuf Abdurrahmanmm', 'Kebumen', '2000-02-11', 'L', 1, 77.00, 80.00, 'wisnu wis', 'yuriza nurul a', 'aktif', '2026-05-20 20:31:23', NULL),
(4, 8, '23234567893', 'Ashfa Adlan', 'Kebumen', '2022-01-20', 'L', 2, 34.00, 33.99, 'wisnu w', 'yuriza nurul a', 'aktif', '2026-06-01 21:33:33', NULL),
(6, 3, 'asdwg', 'j', 'Kebumen', '2026-06-21', 'L', 2, 4.00, 50.00, 'wisnu wis', 'belum ada', 'aktif', '2026-06-26 09:08:15', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ibu_hamil`
--

CREATE TABLE `ibu_hamil` (
  `id` int(11) NOT NULL,
  `id_keluarga` int(11) NOT NULL,
  `nik` varchar(30) DEFAULT NULL,
  `nama_ibu` varchar(100) NOT NULL,
  `tempat_lahir` varchar(100) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `hamil_ke` int(11) DEFAULT 1,
  `usia_kehamilan` int(11) DEFAULT 0,
  `hpht` date DEFAULT NULL,
  `hpl` date DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `status` enum('Aktif','Melahirkan','Pindah') DEFAULT 'Aktif',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `ibu_hamil`
--

INSERT INTO `ibu_hamil` (`id`, `id_keluarga`, `nik`, `nama_ibu`, `tempat_lahir`, `tanggal_lahir`, `hamil_ke`, `usia_kehamilan`, `hpht`, `hpl`, `no_hp`, `status`, `created_at`) VALUES
(2, 8, '111111111112', 'sxdcvfbnm,', 'Kebumen', '2000-06-17', 1, 0, '2026-06-21', '2027-03-28', '000', 'Aktif', '2026-06-26 07:42:08');

-- --------------------------------------------------------

--
-- Table structure for table `imunisasi`
--

CREATE TABLE `imunisasi` (
  `id` int(11) NOT NULL,
  `id_anak` int(11) NOT NULL,
  `id_kegiatan` int(11) NOT NULL,
  `id_master_imunisasi` int(11) DEFAULT NULL,
  `jenis_imunisasi` varchar(100) NOT NULL,
  `tanggal` date DEFAULT NULL,
  `diberikan_oleh` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `imunisasi`
--

INSERT INTO `imunisasi` (`id`, `id_anak`, `id_kegiatan`, `id_master_imunisasi`, `jenis_imunisasi`, `tanggal`, `diberikan_oleh`, `created_at`) VALUES
(3, 2, 4, NULL, 'Polio', '2026-06-16', 1, '2026-06-16 17:58:36'),
(4, 4, 4, NULL, 'MR', '2026-06-16', 1, '2026-06-16 18:42:30'),
(5, 6, 4, 7, 'Polio', '2026-06-26', 1, '2026-06-26 09:11:35');

-- --------------------------------------------------------

--
-- Table structure for table `imunisasi_ibu_hamil`
--

CREATE TABLE `imunisasi_ibu_hamil` (
  `id` int(11) NOT NULL,
  `ibu_hamil_id` int(11) NOT NULL,
  `imunisasi_id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `diberikan_oleh` int(11) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `imunisasi_ibu_hamil`
--

INSERT INTO `imunisasi_ibu_hamil` (`id`, `ibu_hamil_id`, `imunisasi_id`, `tanggal`, `diberikan_oleh`, `keterangan`, `created_at`) VALUES
(1, 2, 10, '2026-06-26', 1, '', '2026-06-26 09:49:54');

-- --------------------------------------------------------

--
-- Table structure for table `kegiatan`
--

CREATE TABLE `kegiatan` (
  `id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `lokasi` varchar(150) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `pertemuan_ke` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `status` enum('scheduled','selesai') DEFAULT 'scheduled'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `kegiatan`
--

INSERT INTO `kegiatan` (`id`, `tanggal`, `lokasi`, `keterangan`, `pertemuan_ke`, `created_by`, `created_at`, `status`) VALUES
(4, '2026-06-16', 'PKD Kedungwaru', 'kjqqqqq', 1, 1, '2026-06-15 16:25:48', 'scheduled');

-- --------------------------------------------------------

--
-- Table structure for table `kehadiran`
--

CREATE TABLE `kehadiran` (
  `id` int(11) NOT NULL,
  `id_anak` int(11) NOT NULL,
  `id_kegiatan` int(11) NOT NULL,
  `status_hadir` enum('hadir','tidak') NOT NULL,
  `dicatat_oleh` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `kehadiran`
--

INSERT INTO `kehadiran` (`id`, `id_anak`, `id_kegiatan`, `status_hadir`, `dicatat_oleh`, `created_at`) VALUES
(7, 4, 4, 'hadir', 1, '2026-06-15 16:46:43'),
(8, 2, 4, 'hadir', 1, '2026-06-15 16:46:43'),
(22, 6, 4, 'hadir', 1, '2026-06-26 09:08:34');

-- --------------------------------------------------------

--
-- Table structure for table `kehadiran_ibu_hamil`
--

CREATE TABLE `kehadiran_ibu_hamil` (
  `id` int(11) NOT NULL,
  `ibu_hamil_id` int(11) NOT NULL,
  `id_kegiatan` int(11) NOT NULL,
  `status_hadir` enum('hadir','tidak') NOT NULL,
  `dicatat_oleh` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `kehadiran_ibu_hamil`
--

INSERT INTO `kehadiran_ibu_hamil` (`id`, `ibu_hamil_id`, `id_kegiatan`, `status_hadir`, `dicatat_oleh`, `created_at`) VALUES
(1, 1, 4, 'hadir', 1, '2026-06-26 06:09:43'),
(4, 2, 4, 'hadir', 1, '2026-06-26 08:56:57');

-- --------------------------------------------------------

--
-- Table structure for table `keluarga`
--

CREATE TABLE `keluarga` (
  `id` int(11) NOT NULL,
  `no_kk` varchar(30) DEFAULT NULL,
  `nama_kepala_keluarga` varchar(100) NOT NULL,
  `nik_ayah` varchar(30) DEFAULT NULL,
  `nama_ayah` varchar(100) DEFAULT NULL,
  `nik_ibu` varchar(30) DEFAULT NULL,
  `nama_ibu` varchar(100) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `rt` varchar(10) DEFAULT NULL,
  `rw` varchar(10) DEFAULT NULL,
  `desa` varchar(100) DEFAULT NULL,
  `kecamatan` varchar(100) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `keluarga`
--

INSERT INTO `keluarga` (`id`, `no_kk`, `nama_kepala_keluarga`, `nik_ayah`, `nama_ayah`, `nik_ibu`, `nama_ibu`, `alamat`, `rt`, `rw`, `desa`, `kecamatan`, `no_hp`, `created_at`) VALUES
(3, '1234567890000', 'Bapak Wisnu Wibisono', '1110000', 'wisnu wis', '00001111', 'belum ada', 'Madureso, Kuwarasan, Kebumen Regency, Central Java, Indonesia', '02', '02', 'vbnm', 'xcvbnmmmmmmmmmmmmmmmmmmm', '111', '2026-05-20 20:26:42'),
(8, '1234567890222', 'wisnu', '11100002222', 'wisnu w', '222', 'yuriza nurul a', 'Jalan Candiwulan, Rumah Bapak Warto, Desa Madureso, Kec. Kuwarasan. Kabupaten Kebumen', '8', '9', 'vbnm', 'xcvbnmmmmmmmmmmmmmmmmmmm', 'cvbnm,', '2026-06-26 06:44:07');

-- --------------------------------------------------------

--
-- Table structure for table `master_imunisasi`
--

CREATE TABLE `master_imunisasi` (
  `id` int(11) NOT NULL,
  `kategori` enum('Anak','Ibu Hamil') NOT NULL,
  `nama_imunisasi` varchar(100) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `master_imunisasi`
--

INSERT INTO `master_imunisasi` (`id`, `kategori`, `nama_imunisasi`, `keterangan`, `created_at`) VALUES
(7, 'Anak', 'BCG (Bacillus Calmette-GuÃ©rin', 'Imunisasi BCG', '2026-06-25 03:25:31'),
(8, 'Anak', 'Polio', 'Imunisasi Polio', '2026-06-25 03:25:31'),
(9, 'Anak', 'DPT-HB-Hib (vaksin kombinasi)', 'Imunisasi DPT-HB-Hib adalah vaksin kombinasi (pentavalen) wajib untuk melindungi anak dari 5 penyakit sekaligus: difteri, tetanus, pertusis (batuk rejan), hepatitis B, dan infeksi Haemophilus influenzae tipe b (penyebab pneumonia dan meningitis). Vaksin ini sangat krusial diberikan di awal kehidupan bayi karena kelima penyakit tersebut dapat memicu komplikasi fatal.', '2026-06-25 03:25:31'),
(10, 'Ibu Hamil', 'TT 1', 'Tetanus Toxoid 1', '2026-06-25 03:25:31'),
(11, 'Ibu Hamil', 'TT 2', 'Tetanus Toxoid 2', '2026-06-25 03:25:31'),
(12, 'Ibu Hamil', 'TT Booster', 'Booster Tetanus', '2026-06-25 03:25:31');

-- --------------------------------------------------------

--
-- Table structure for table `pemeriksaan`
--

CREATE TABLE `pemeriksaan` (
  `id` int(11) NOT NULL,
  `id_anak` int(11) NOT NULL,
  `id_kegiatan` int(11) NOT NULL,
  `umur_bulan` int(11) DEFAULT NULL,
  `berat_badan` decimal(5,2) DEFAULT NULL,
  `tinggi_badan` decimal(5,2) DEFAULT NULL,
  `lingkar_kepala` decimal(5,2) DEFAULT NULL,
  `status_gizi` varchar(50) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `diukur_oleh` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `pemeriksaan`
--

INSERT INTO `pemeriksaan` (`id`, `id_anak`, `id_kegiatan`, `umur_bulan`, `berat_badan`, `tinggi_badan`, `lingkar_kepala`, `status_gizi`, `catatan`, `diukur_oleh`, `created_at`) VALUES
(4, 4, 4, 53, 8.00, 50.00, 50.00, 'Baik', '', 1, '2026-06-15 17:28:44'),
(5, 2, 4, 316, 9.00, 50.00, 50.00, 'Baik', '', 1, '2026-06-15 17:28:44');

-- --------------------------------------------------------

--
-- Table structure for table `pemeriksaan_ibu_hamil`
--

CREATE TABLE `pemeriksaan_ibu_hamil` (
  `id` int(11) NOT NULL,
  `ibu_hamil_id` int(11) NOT NULL,
  `id_kegiatan` int(11) DEFAULT NULL,
  `tanggal_periksa` date NOT NULL,
  `usia_kehamilan` int(11) DEFAULT NULL,
  `berat_badan` decimal(5,2) DEFAULT NULL,
  `tekanan_darah` varchar(20) DEFAULT NULL,
  `lingkar_lengan` decimal(5,2) DEFAULT NULL,
  `tinggi_fundus` decimal(5,2) DEFAULT NULL,
  `keluhan` text DEFAULT NULL,
  `tindakan` text DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','kader','bidan') NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Wisnu Wibisono', 'wisnu@gmail.com', '$2y$10$2m7TbQ5U9qqS4KvmxFWRDuVXKQ.l/MmG5MiViSCTmiguKNXtDYTQe', 'admin', '2026-04-27 17:18:14', '2026-06-16 19:28:52');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `anak`
--
ALTER TABLE `anak`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nik` (`nik`),
  ADD KEY `idx_anak_keluarga` (`id_keluarga`);

--
-- Indexes for table `ibu_hamil`
--
ALTER TABLE `ibu_hamil`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ibu_hamil_keluarga` (`id_keluarga`);

--
-- Indexes for table `imunisasi`
--
ALTER TABLE `imunisasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_kegiatan` (`id_kegiatan`),
  ADD KEY `diberikan_oleh` (`diberikan_oleh`),
  ADD KEY `idx_imunisasi_anak` (`id_anak`),
  ADD KEY `fk_imunisasi_master` (`id_master_imunisasi`);

--
-- Indexes for table `imunisasi_ibu_hamil`
--
ALTER TABLE `imunisasi_ibu_hamil`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ibu_hamil_id` (`ibu_hamil_id`),
  ADD KEY `imunisasi_id` (`imunisasi_id`);

--
-- Indexes for table `kegiatan`
--
ALTER TABLE `kegiatan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `kehadiran`
--
ALTER TABLE `kehadiran`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unik_kehadiran` (`id_anak`,`id_kegiatan`),
  ADD KEY `dicatat_oleh` (`dicatat_oleh`),
  ADD KEY `idx_kehadiran_kegiatan` (`id_kegiatan`);

--
-- Indexes for table `kehadiran_ibu_hamil`
--
ALTER TABLE `kehadiran_ibu_hamil`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unik_kehadiran_ibu` (`ibu_hamil_id`,`id_kegiatan`);

--
-- Indexes for table `keluarga`
--
ALTER TABLE `keluarga`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `no_kk` (`no_kk`);

--
-- Indexes for table `master_imunisasi`
--
ALTER TABLE `master_imunisasi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pemeriksaan`
--
ALTER TABLE `pemeriksaan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unik_pemeriksaan` (`id_anak`,`id_kegiatan`),
  ADD KEY `diukur_oleh` (`diukur_oleh`),
  ADD KEY `idx_pemeriksaan_anak` (`id_anak`),
  ADD KEY `idx_pemeriksaan_kegiatan` (`id_kegiatan`);

--
-- Indexes for table `pemeriksaan_ibu_hamil`
--
ALTER TABLE `pemeriksaan_ibu_hamil`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ibu_hamil_id` (`ibu_hamil_id`),
  ADD KEY `fk_periksa_ibu_kegiatan` (`id_kegiatan`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `anak`
--
ALTER TABLE `anak`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `ibu_hamil`
--
ALTER TABLE `ibu_hamil`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `imunisasi`
--
ALTER TABLE `imunisasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `imunisasi_ibu_hamil`
--
ALTER TABLE `imunisasi_ibu_hamil`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `kegiatan`
--
ALTER TABLE `kegiatan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `kehadiran`
--
ALTER TABLE `kehadiran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `kehadiran_ibu_hamil`
--
ALTER TABLE `kehadiran_ibu_hamil`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `keluarga`
--
ALTER TABLE `keluarga`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `master_imunisasi`
--
ALTER TABLE `master_imunisasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `pemeriksaan`
--
ALTER TABLE `pemeriksaan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `pemeriksaan_ibu_hamil`
--
ALTER TABLE `pemeriksaan_ibu_hamil`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `anak`
--
ALTER TABLE `anak`
  ADD CONSTRAINT `anak_ibfk_1` FOREIGN KEY (`id_keluarga`) REFERENCES `keluarga` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ibu_hamil`
--
ALTER TABLE `ibu_hamil`
  ADD CONSTRAINT `fk_ibu_hamil_keluarga` FOREIGN KEY (`id_keluarga`) REFERENCES `keluarga` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `imunisasi`
--
ALTER TABLE `imunisasi`
  ADD CONSTRAINT `fk_imunisasi_master` FOREIGN KEY (`id_master_imunisasi`) REFERENCES `master_imunisasi` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `imunisasi_ibfk_1` FOREIGN KEY (`id_anak`) REFERENCES `anak` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `imunisasi_ibfk_2` FOREIGN KEY (`id_kegiatan`) REFERENCES `kegiatan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `imunisasi_ibfk_3` FOREIGN KEY (`diberikan_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `imunisasi_ibu_hamil`
--
ALTER TABLE `imunisasi_ibu_hamil`
  ADD CONSTRAINT `imunisasi_ibu_hamil_ibfk_1` FOREIGN KEY (`ibu_hamil_id`) REFERENCES `ibu_hamil` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `imunisasi_ibu_hamil_ibfk_2` FOREIGN KEY (`imunisasi_id`) REFERENCES `master_imunisasi` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kegiatan`
--
ALTER TABLE `kegiatan`
  ADD CONSTRAINT `kegiatan_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `kehadiran`
--
ALTER TABLE `kehadiran`
  ADD CONSTRAINT `kehadiran_ibfk_1` FOREIGN KEY (`id_anak`) REFERENCES `anak` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `kehadiran_ibfk_2` FOREIGN KEY (`id_kegiatan`) REFERENCES `kegiatan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `kehadiran_ibfk_3` FOREIGN KEY (`dicatat_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `pemeriksaan`
--
ALTER TABLE `pemeriksaan`
  ADD CONSTRAINT `pemeriksaan_ibfk_1` FOREIGN KEY (`id_anak`) REFERENCES `anak` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pemeriksaan_ibfk_2` FOREIGN KEY (`id_kegiatan`) REFERENCES `kegiatan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pemeriksaan_ibfk_3` FOREIGN KEY (`diukur_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `pemeriksaan_ibu_hamil`
--
ALTER TABLE `pemeriksaan_ibu_hamil`
  ADD CONSTRAINT `fk_periksa_ibu_kegiatan` FOREIGN KEY (`id_kegiatan`) REFERENCES `kegiatan` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pemeriksaan_ibu_hamil_ibfk_1` FOREIGN KEY (`ibu_hamil_id`) REFERENCES `ibu_hamil` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
